<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午5:03
 */
date_default_timezone_set("UTC");

require_once __DIR__ . "/../vendor/autoload.php";

$config = new \Serverx\Conf\TCPServerConfig();
$config->setPort(9797);
$config->setDaemonize(0);
$config->setRunDir(__DIR__);
$config->setLogDir(__DIR__ . '/logs');
$config->setLogLevel();
$config->registerApp('App', __DIR__);
$server = new Serverx\Serv\RPCServer($config);
$server->run();