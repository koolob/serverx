<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 下午12:00
 */
date_default_timezone_set("UTC");

require_once __DIR__ . "/../vendor/autoload.php";

$config = new \Serverx\Conf\WebsocketServerConfig();
$config->setHost('0.0.0.0');
$config->setDaemonize(0);
$config->setRunDir(__DIR__);
$config->setLogDir(__DIR__ . '/logs');
$config->registerApp('App', __DIR__);

$server = new \Serverx\Serv\MixRPCServer($config);

$rpcconfig = new \Serverx\Conf\TCPServerConfig();
$rpcconfig->setHost('0.0.0.0');
$rpcconfig->setPort(9797);
$server->addNewListen($rpcconfig);

$server->addHandleTypes(\Serverx\Serv\HttpServer::HANDLE_TYPE_HTTP, array(
    'system.health',
    'system.status',
));

$server->addHandleTypes(\Serverx\Serv\RPCServer::HANDLE_TYPE_RPC, array(
    'index.index',
));

$server->run();