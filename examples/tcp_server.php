<?php

/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/11/30
 * Time: 下午2:19
 */
date_default_timezone_set("UTC");

require_once __DIR__ . "/../vendor/autoload.php";

$config = new \Serverx\Conf\TCPServerConfig();
$config->setDaemonize(0);
$config->setRunDir(__DIR__);
$config->setLogDir(__DIR__ . '/logs');
$server = new Serverx\Serv\TCPServer($config);
$server->run();