<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 上午11:03
 */

namespace Serverx\Serv;


use Serverx\Conf\ServerConfig;

class HttpServer extends BaseServ
{

    protected function initSwooleServer(ServerConfig $config)
    {
        $server = new \swoole_http_server($config->getHost(), $config->getPort());
        $server->set($config->toSwooleConfigArray());
        $server->on('Start', array($this, 'onSwooleStart'));
        $server->on('Shutdown', array($this, 'onSwooleShutdown'));
        $server->on('WorkerStart', array($this, 'onWorkerStart'));

        $server->on('request', array($this, 'onRequest'));

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
        return $runDir . DIRECTORY_SEPARATOR . 'serverx_http.pid';
    }

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        
    }
}