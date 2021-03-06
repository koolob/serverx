<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午4:53
 */

namespace Serverx\Serv;


use Katzgrau\KLogger\Logger;
use Serverx\Conf\ServerConfig;
use Serverx\Exception\App\EmptyParams;
use Serverx\Exception\App\NotFound;
use Serverx\Exception\Server\ConfigError;

abstract class BaseServ
{
    private $swoole_server;
    private $serverConfig;
    private $logger;
    private $name;

    private $handleTypes = array();

    private $worker_start_callback = null;

    private $enableRequestLog = false;

    function __construct(ServerConfig $config)
    {
        $this->serverConfig = $config;
        set_error_handler(array($this, 'errorHandle'));
    }

    abstract protected function initSwooleServer(ServerConfig $config);

    abstract protected function getPidFile();

    public function run()
    {
        $this->swoole_server = $this->initSwooleServer($this->getServerConfig());
        $this->swoole_server->start();
    }

    public function onSwooleStart(\swoole_server $serv)
    {
        try {
            $this->setProcessName("serverx-master");
            $this->setPID($serv->master_pid);
            echo "swoole server version:" . swoole_version() . PHP_EOL;
        } catch (\Exception $e) {
            $this->swoole_server->shutdown();
            throw $e;
        }
    }

    public function onSwooleShutdown(\swoole_server $serv)
    {
        $this->delPID();
    }

    public function onWorkerStart(\swoole_server $serv, $worker_id)
    {
        $base = 'serverx-';
        if ($this->swoole_server->taskworker) {
            $base .= 'tasker-';
        } else {
            $base .= 'worker-';
        }
        $base .= $worker_id;
        $this->name = $base;
        $this->setProcessName($base);
        echo "$base start at " . date("Ymd:His") . PHP_EOL;
        $this->logger = new Logger($this->serverConfig->getLogDir(), $this->serverConfig->getLogLevel(), array(
            'extension' => date('H') . '.log',
        ));
        if ($this->worker_start_callback != null && is_callable($this->worker_start_callback)) {
            call_user_func($this->worker_start_callback, $this);
        }
    }

    public function onTask(\swoole_server $serv, $task_id, $from_id, $data)
    {
        if (empty($data) || !isset($data['class'])) {
            return null;
        } else {
            $taskName = $data['class'];
            $params = $data['params'];
            if (!class_exists($taskName, true)) {
                return null;
            }
            $taskClass = new $taskName($this);
            try {
                $result = $taskClass->exec($params);
                $data['result'] = $result;
                return $data;
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    public function onFinish(\swoole_server $serv, $task_id, $data)
    {
        if (empty($data) || !isset($data['class'])) {
            return;
        } else {
            $taskName = $data['class'];
            $params = $data['params'];
            $result = $data['result'];
            if (!class_exists($taskName, true)) {
                return;
            }
            $taskClass = new $taskName($this);
            try {
                $taskClass->after($params, $result);
            } catch (\Exception $e) {
                return;
            }
        }
    }

    public function errorHandle($error, $error_string, $filename, $line, $symbols)
    {

    }

    protected function handle($controller, $action, array $params, $extras = array(), $type = 0)
    {
        if (isset($this->handleTypes[$type])) {
            if (!in_array("$controller.$action", $this->handleTypes[$type])) {
                if (!in_array("$controller.*", $this->handleTypes[$type])) {
                    throw new NotFound("$controller.$action not allow");
                }
            }
        }

        $controllerClassName = '\\' . $this->getServerConfig()->getAppNamespace() . '\\Controller\\' . ucwords($controller);
        if (!class_exists($controllerClassName)) {
            $file = $this->getServerConfig()->getControllerDir() . ucwords($controller) . '.php';
            if (file_exists($file)) {
                require_once $file;
            } else {
                throw new NotFound("class $controllerClassName not found");
            }
        }
        if (!class_exists($controllerClassName)) {
            throw new NotFound("class $controllerClassName not found");
        }
        $controllerClass = new $controllerClassName($this);

        $actionMethod = $controllerClass->getActionMethod($action);
        if (!method_exists($controllerClass, $actionMethod)) {
            throw new NotFound("method $actionMethod not found");
        }

        if (!$controllerClass->checkParams($controller, $action, $params)) {
            throw new EmptyParams();
        }

        $controllerClass->setExtras($extras);
        $data = $controllerClass->beforeMethod($controller, $action, $params);
        if (empty($data)) {
            $data = $controllerClass->$actionMethod($params);
        }
        if ($this->enableRequestLog && $controllerClass->isEnableRequestLog()) {
            $ret = is_array($data) ? json_encode($data) : $data;
            $this->info("[REQUEST][$controller][$action][--" . json_encode($params) . "--][RESPONSE][--" . $ret . "--]");
        }
        return $data;
    }

    public function info($message)
    {
        $this->logger->info('[' . $this->name . '] ' . $message);
    }

    public function warning($message)
    {
        $this->logger->warning('[' . $this->name . '] ' . $message);
    }

    public function error($message)
    {
        $this->logger->error('[' . $this->name . '] ' . $message);
    }

    private function setProcessName($name)
    {
        if (PHP_OS == "Darwin") {
            //Mac OS 执行这个命令有问题
            return;
        }
        if (function_exists("cli_set_process_title")) {
            cli_set_process_title($name);
        } elseif (function_exists('swoole_set_process_name')) {
            swoole_set_process_name($name);
        }
    }

    private function setPID($pid)
    {
        if (!file_put_contents($this->getPidFile(), $pid)) {
            throw new ConfigError("can not write pid");
        }
    }

    private function delPID()
    {
        unlink($this->getPidFile());
    }

    protected function getServerConfig()
    {
        return $this->serverConfig;
    }

    public function status()
    {
        return $this->swoole_server->stats();
    }

    public function run_task($data)
    {
        $this->swoole_server->task($data);
    }

    public function addHandleTypes($type, array $names)
    {
        $this->handleTypes[$type] = $names;
    }

    public function setWorkerStartCallback($func)
    {
        $this->worker_start_callback = $func;
    }

    public function enableRequestLog()
    {
        $this->enableRequestLog = true;
    }

    public function stopWorker()
    {
        $this->swoole_server->stop();
    }
}