<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午4:53
 */

namespace Serverx\Serv;


use Serverx\Conf\ServerConfig;
use Serverx\Exception\ServerAppDirException;

abstract class BaseServ
{
    private $swoole_server;
    private $serverConfig;

    function __construct(ServerConfig $config)
    {
        $this->serverConfig = $config;
    }

    abstract protected function initSwooleServer(ServerConfig $config);

    abstract protected function getPidFile();

    public function run()
    {
        $this->swoole_server = $this->initSwooleServer($this->getServerConfig());
        $this->swoole_server->start();
    }

    protected function onSwooleStart(\swoole_server $serv)
    {
        $this->setProcessName("serverx-master");
        $this->setPID($serv->master_pid);
    }

    protected function onSwooleShutdown(\swoole_server $serv)
    {
        $this->delPID();
    }

    protected function onWorkerStart(\swoole_server $serv, $worker_id)
    {
        if ($serv->taskworker) {
            $this->setProcessName('serverx-tasker');
        } else {
            $this->setProcessName('serverx-worker');
        }
        echo "serverx $worker_id start at " . date("Ymd:His") . PHP_EOL;
    }

    protected function onTask(\swoole_server $serv, $task_id, $from_id, $data)
    {

    }

    protected function onFinish(\swoole_server $serv, $task_id, $data)
    {

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
            throw new ServerAppDirException();
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
}