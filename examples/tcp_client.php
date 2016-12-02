<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午1:53
 */
date_default_timezone_set("UTC");

require_once __DIR__ . "/../vendor/autoload.php";
$begin = Serverx\Util\Timeu::mTimestamp();
$cli = \Serverx\Cli\TCP::getInstance('127.0.0.1', '8080');
if (!$cli->ping()) {
    echo "server not ok\n";
}
$end = Serverx\Util\Timeu::mTimestamp();
echo "cost:" . ($end - $begin);
