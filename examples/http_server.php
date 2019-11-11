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
$config->setHost('0.0.0.0');
$config->setPort('8080');
$config->setDaemonize(0);
$config->setRunDir(__DIR__);
$config->setLogDir(__DIR__ . '/logs');
$config->setLogLevel();
$config->setMaxRequest(10000);
$config->setDispatchMode(3);
$config->registerApp('App', __DIR__);
$server = new \Serverx\Serv\HttpServer($config);
//$server->addHandleTypes(\Serverx\Serv\HttpServer::HANDLE_TYPE_HTTP, array(
//    'system.health',//可用于健康检查
//    'system.status',//可用于监控信息
//));
$server->run();