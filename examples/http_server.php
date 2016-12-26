<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 下午2:31
 */
date_default_timezone_set("UTC");

require_once __DIR__ . "/../vendor/autoload.php";

$config = new \Serverx\Conf\ServerConfig();
$config->setDaemonize(0);
$config->setRunDir(__DIR__);
$config->setLogDir(__DIR__ . '/logs');
$config->registerApp('App', __DIR__);
$server = new \Serverx\Serv\HttpServer($config);
$server->run();