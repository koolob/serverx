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
$config->setDaemonize(0);
$config->setRunDir(__DIR__);
$config->setLogDir(__DIR__ . '/logs');
$config->registerApp('App', __DIR__);

$server = new \Serverx\Serv\MixServer($config);

$rpcconfig = new \Serverx\Conf\TCPServerConfig();
$rpcconfig->setHost('0.0.0.0');
$rpcconfig->setPort(9797);
$server->addNewListen($rpcconfig);

$server->run();