<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午5:04
 */
date_default_timezone_set("UTC");
error_reporting(0);
require_once __DIR__ . "/../vendor/autoload.php";
$begin = Serverx\Util\Timeu::mTimestamp();
for ($i = 0; $i < 1; $i++) {
    $rpc = new \Serverx\Cli\RPC('127.0.0.1', '9797', 1);
    $request = \Serverx\Rpc\Request::build('index.index')->setParams(array(
        'a' => 1,
        'b' => 'b'
    ));
    $response = $rpc->getResponse($request);
    echo "get response:" . $response . "\n";
}

$end = Serverx\Util\Timeu::mTimestamp();
echo "cost:" . ($end - $begin);