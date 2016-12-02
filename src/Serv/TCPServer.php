<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午4:29
 */

namespace Serverx\Serv;


use Serverx\Conf\ServerConfig;
use Serverx\Protocol\TCPProtocol;

class TCPServer extends BaseServ
{

    protected function initSwooleServer(ServerConfig $config)
    {
        $server = new \swoole_server($config->getHost(), $config->getPort());
        $server->set($config->toSwooleConfigArray());
        $server->on('Start', array($this, 'onSwooleStart'));
        $server->on('Shutdown', array($this, 'onSwooleShutdown'));
        $server->on('WorkerStart', array($this, 'onWorkerStart'));

        $server->on('connect', array($this, 'onConnect'));
        $server->on('receive', array($this, 'onReceive'));
        $server->on('close', array($this, 'onClose'));

        if ($config->isTaskEnable()) {
            $server->on('Task', array($this, 'onTask'));
            $server->on('Finish', array($this, 'onFinish'));
        }
        return $server;
    }


    protected function getPidFile()
    {
        $appDir = $this->getServerConfig()->getRunDir();
        $runDir = $appDir . DIRECTORY_SEPARATOR . 'run';
        if (!file_exists($runDir)) {
            mkdir($runDir);
        }
        return $runDir . DIRECTORY_SEPARATOR . 'serverx.pid';
    }

    public function onConnect(\swoole_server $serv, $fd)
    {
//        echo "connect $fd";
    }

    public function onReceive(\swoole_server $serv, $fd, $from_id, $data)
    {
        $rev = TCPProtocol::decode($data);
        if ($rev === 'PING') {
            $this->sendResult($serv, $fd, 'PONG');
        } else {
            $this->sendResult($serv, $fd, $this->handlerReceive($rev));
        }
    }

    public function onClose(\swoole_server $serv, $fd)
    {
//        echo "close $fd";
    }

    protected function handlerReceive($data)
    {
        return $data;
    }

    private function sendResult(\swoole_server $serv, $fd, $data)
    {
        $serv->send($fd, TCPProtocol::encode($data));
    }
}