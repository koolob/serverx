<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/1/12
 * Time: 下午6:39
 */
date_default_timezone_set("UTC");
ini_set('memory_limit','256M');

require_once __DIR__ . "/../vendor/autoload.php";

$data = array();

for ($i = 0; $i < 999999; $i++) {
    $data[] = $i;
}

$str = json_encode($data);

$encode = \Serverx\Protocol\TCPProtocol::encode($str);

$decode = \Serverx\Protocol\TCPProtocol::decode($encode);

echo $decode == $str;