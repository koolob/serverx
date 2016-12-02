<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/11/22
 * Time: 下午1:58
 */

namespace Serverx\Cli;


use Serverx\Protocol\TCPProtocol;
use Serverx\Util\Timeu;

class TCP
{
    private $swoole_client = null;

    protected static $_instances = array();

    function __construct($serverHost, $serverPort)
    {
        $key = $serverHost . ':' . $serverPort;
        $this->swoole_client = new \swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        $this->swoole_client->connect($serverHost, $serverPort);
        $this->swoole_client->set(array(
            'open_length_check' => true,
            'package_length_type' => 'N',
            'package_max_length' => 2097152,
            'package_length_offset' => 0,
            'package_body_offset' => 4,
        ));
        self::$_instances[$key] = $this;
    }

    public static function getInstance($serverHost, $serverPort)
    {
        $key = $serverHost . ':' . $serverPort;
        if (empty(self::$_instances[$key])) {
            $obj = new TCP($serverHost, $serverPort);
        } else {
            $obj = self::$_instances[$key];
        }
        return $obj;
    }

    function getResult($data)
    {
        $this->swoole_client->send(TCPProtocol::encode($data));
        $rev = $this->swoole_client->recv();
        return TCPProtocol::decode($rev);
    }

    public function ping()
    {
        $pong = $this->getResult('PING');
        if ($pong == 'PONG') {
            return true;
        }
        return false;
    }
}