<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 上午11:05
 */

namespace Serverx\Serv;


use Serverx\Conf\ServerConfig;
use Serverx\Protocol\TCPProtocol;

class MixServer extends BaseServ
{
    private $listenConfigs = array();

    protected function initSwooleServer(ServerConfig $config)
    {
        $server = new \swoole_websocket_server($config->getHost(), $config->getPort());
        $server->set($config->toSwooleConfigArray());
        $server->on('Start', array($this, 'onSwooleStart'));
        $server->on('Shutdown', array($this, 'onSwooleShutdown'));
        $server->on('WorkerStart', array($this, 'onWorkerStart'));

        $server->on('open', array($this, 'onOpen'));
        $server->on('message', array($this, 'onMessage'));
        $server->on('close', array($this, 'onClose'));
        $server->on('request', array($this, 'onRequest'));

        if ($config->isTaskEnable()) {
            $server->on('Task', array($this, 'onTask'));
            $server->on('Finish', array($this, 'onFinish'));
        }

        foreach ($this->listenConfigs as $listenConfig) {
            $listen = $server->listen($listenConfig->getHost(), $listenConfig->getPort(), $listenConfig->getSockType());
            $listen->set($listenConfig->toSwooleConfigArray());
            $listen->on('connect', array($this, 'onConnect'));
            $listen->on('receive', array($this, 'onReceive'));
            $listen->on('close', array($this, 'onClose'));
            $listen->on('packet', array($this, 'onPacket'));
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
        return $runDir . DIRECTORY_SEPARATOR . 'serverx_mix.pid';
    }

    public function addNewListen(ServerConfig $config)
    {
        $this->listenConfigs[] = $config;
    }

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $response->status(404);
        $response->end("");
    }

    public function onOpen(\swoole_websocket_server $serv, $fd)
    {
//        echo "open $fd";
    }

    public function onMessage(\swoole_websocket_server $serv, \swoole_websocket_frame $frame)
    {
        $serv->push($frame->fd, $frame->data);
    }

    public function onClose(\swoole_server $serv, $fd)
    {
//        echo "close $fd";
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

    protected function handlerReceive($data)
    {
        return $data;
    }

    private function sendResult(\swoole_server $serv, $fd, $data)
    {
        $serv->send($fd, TCPProtocol::encode($data));
    }

    public function onPacket(\swoole_server $serv, $data, $addr)
    {
        var_dump($addr);
    }
}