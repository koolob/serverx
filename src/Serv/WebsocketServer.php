<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 上午11:04
 */

namespace Serverx\Serv;


use Serverx\Conf\ServerConfig;

class WebsocketServer extends BaseServ
{

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
        return $runDir . DIRECTORY_SEPARATOR . 'serverx_ws.pid';
    }

    public function onOpen(\swoole_websocket_server $serv, $fd)
    {
//        echo "open $fd";
    }

    public function onMessage(\swoole_websocket_server $serv, \swoole_websocket_frame $frame)
    {

    }

    public function onClose(\swoole_websocket_server $serv, $fd)
    {
//        echo "close $fd";
    }


}