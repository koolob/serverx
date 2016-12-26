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
use Serverx\Exception\App\NotFound;
use Serverx\Exception\Server\ConfigError;

abstract class BaseServ
{
    private $swoole_server;
    private $serverConfig;
    private $logger;

    function __construct(ServerConfig $config)
    {
        $this->serverConfig = $config;
        $this->logger = new Logger($config->getLogDir());
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
        if ($serv->taskworker) {
            $this->setProcessName('serverx-tasker');
        } else {
            $this->setProcessName('serverx-worker');
        }
        echo "serverx $worker_id start at " . date("Ymd:His") . PHP_EOL;
    }

    public function onTask(\swoole_server $serv, $task_id, $from_id, $data)
    {

    }

    public function onFinish(\swoole_server $serv, $task_id, $data)
    {

    }

    public function errorHandle($error, $error_string, $filename, $line, $symbols)
    {

    }

    protected function handle($controller, $action, array $params, $extras = array())
    {
        $controllerClassName = '\\' . $this->getServerConfig()->getAppNamespace() . '\\Controller\\' . ucwords($controller);
        if (!class_exists($controllerClassName)) {
            require_once $this->getServerConfig()->getControllerDir() . ucwords($controller) . '.php';
        }
        if (!class_exists($controllerClassName)) {
            throw new NotFound("class $controllerClassName not found");
        }
        $controllerClass = new $controllerClassName($this);
        $actionMethod = $controllerClass->getActionMethod($action);
        if (!method_exists($controllerClass, $actionMethod)) {
            throw new NotFound("method $actionMethod not found");
        }
        $controllerClass->setExtras($extras);
        return $controllerClass->$actionMethod($params);
    }

    public function info($message)
    {
        $this->logger->info($message);
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
}