<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午4:59
 */

namespace Serverx\Conf;


use Serverx\Exception\Server\ConfigError;
use Serverx\Exception\ServerAppDirException;

class ServerConfig
{
    private $reactor_num = -1;
    private $worker_num = 2;
    private $max_request = -1;
    private $task_worker_num = -1;
    private $task_ipc_mode = -1;
    private $task_max_request = -1;
    private $dispatch_mode = -1;
    private $daemonize = 0;
    private $log_file = null;

    private $host = '127.0.0.1';
    private $port = '8080';

    private $runDir = null;
    private $logDir = null;
    private $appNamespace = null;
    private $appDir = null;

    private $debug = false;

    /**
     * @return null
     */
    public function getRunDir()
    {
        return $this->runDir;
    }

    /**
     * @param null $appSrc
     */
    public function setRunDir($runDir)
    {
        if (file_exists($runDir)) {
            $this->runDir = $runDir;
        } else {
            throw new ConfigError("app dir $runDir not exist");
        }
    }

    /**
     * @return null
     */
    public function getLogDir()
    {
        if (empty($this->logDir)) {
            return $this->runDir;
        } else {
            return $this->logDir;
        }
    }

    /**
     * @param null $logDir
     */
    public function setLogDir($logDir)
    {
        if (file_exists($logDir)) {
            $this->logDir = $logDir;
        } else {
            if (mkdir($logDir, 0777, true)) {
                $this->logDir = $logDir;
            } else {
                throw new ConfigError("log dir $logDir not exist");
            }
        }
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return int
     */
    public function getReactorNum()
    {
        return $this->reactor_num;
    }

    /**
     * @param int $reactor_num
     */
    public function setReactorNum($reactor_num)
    {
        $this->reactor_num = $reactor_num;
    }

    /**
     * @return int
     */
    public function getWorkerNum()
    {
        return $this->worker_num;
    }

    /**
     * @param int $worker_num
     */
    public function setWorkerNum($worker_num)
    {
        $this->worker_num = $worker_num;
    }

    /**
     * @return int
     */
    public function getMaxRequest()
    {
        return $this->max_request;
    }

    /**
     * @param int $max_request
     */
    public function setMaxRequest($max_request)
    {
        $this->max_request = $max_request;
    }

    /**
     * @return int
     */
    public function getTaskWorkerNum()
    {
        return $this->task_worker_num;
    }

    /**
     * @param int $task_worker_num
     */
    public function setTaskWorkerNum($task_worker_num)
    {
        $this->task_worker_num = $task_worker_num;
    }

    /**
     * @return int
     */
    public function getTaskIpcMode()
    {
        return $this->task_ipc_mode;
    }

    /**
     * @param int $task_ipc_mode
     */
    public function setTaskIpcMode($task_ipc_mode)
    {
        $this->task_ipc_mode = $task_ipc_mode;
    }

    /**
     * @return int
     */
    public function getTaskMaxRequest()
    {
        return $this->task_max_request;
    }

    /**
     * @param int $task_max_request
     */
    public function setTaskMaxRequest($task_max_request)
    {
        $this->task_max_request = $task_max_request;
    }

    /**
     * @return int
     */
    public function getDispatchMode()
    {
        return $this->dispatch_mode;
    }

    /**
     * @param int $dispatch_mode
     */
    public function setDispatchMode($dispatch_mode)
    {
        $this->dispatch_mode = $dispatch_mode;
    }

    /**
     * @return int
     */
    public function getDaemonize()
    {
        return $this->daemonize;
    }

    /**
     * @param int $daemonize
     */
    public function setDaemonize($daemonize)
    {
        $this->daemonize = $daemonize;
    }

    /**
     * @return null
     */
    public function getLogFile()
    {
        return $this->log_file;
    }

    /**
     * @param null $log_file
     */
    public function setLogFile($log_file)
    {
        if (file_exists($log_file)) {
            $this->log_file = $log_file;
        }
    }

    public function isTaskEnable()
    {
        return $this->getTaskWorkerNum() > 0;
    }

    public function registerApp($namespace, $dir)
    {
        $this->appNamespace = $namespace;
        $this->appDir = $dir;
    }

    public function getControllerDir()
    {
        return $this->appDir . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return null
     */
    public function getAppNamespace()
    {
        return $this->appNamespace;
    }

    public function toSwooleConfigArray()
    {
        $config = array(
            'worker_num' => $this->getWorkerNum()
        );

        $this->addConfig($config, 'reactor_num', $this->getReactorNum());
        $this->addConfig($config, 'max_request', $this->getMaxRequest());
        $this->addConfig($config, 'dispatch_mode', $this->getDispatchMode());

        return $config;
    }

    private function addConfig($array, $key, $val)
    {
        if (!empty($val) && $val > 0) {
            $array[$key] = $val;
        }
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     */
    public function enableDebug()
    {
        $this->debug = true;
    }


}