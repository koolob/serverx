<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/11/22
 * Time: 下午1:58
 */

namespace Serverx\Cli;


use Serverx\Protocol\TCPProtocol;

class TCP
{
    private $swoole_client = null;

    private $key = null;

    public static $_instances = array();

    function __construct($serverHost, $serverPort)
    {
        $key = $serverHost . ':' . $serverPort;
        $this->swoole_client = new \swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        try {
            $this->swoole_client->connect($serverHost, $serverPort);
        } catch (\Exception $e) {
            var_dump($e);
        }
        $this->swoole_client->set(array(
            'open_length_check' => true,
            'package_length_type' => 'N',
            'package_max_length' => 2097152,
            'package_length_offset' => 0,
            'package_body_offset' => 4,
        ));
        $this->key = $key;
    }

    private function close()
    {
        $this->swoole_client->close();
    }

    private function getErrorCode()
    {
        return $this->swoole_client->errCode;
    }

    private function getErrorMessage()
    {
        return $this->key . ' ' . socket_strerror($this->getErrorCode());
    }

    public static function getInstance($serverHost, $serverPort)
    {
        $key = $serverHost . ':' . $serverPort;
        if (empty(self::$_instances[$key])) {
            $obj = new TCP($serverHost, $serverPort);
            self::$_instances[$key] = $obj;
        } else {
            $obj = self::$_instances[$key];
        }
        return $obj;
    }

    private static function release($key)
    {
        if (!empty(self::$_instances[$key])) {
            $obj = self::$_instances[$key];
            $obj->close();
            unset(self::$_instances[$key]);
        }
    }

    function getResult($data)
    {
        $result = array(
            'code' => 0,
            'msg' => '',
            'data' => null,
        );
        $success = $this->swoole_client->send(TCPProtocol::encode($data));
        if ($success) {
            $rev = $this->swoole_client->recv();
            if ($rev === false) {
                $result['code'] = $this->getErrorCode();
                $result['msg'] = $this->getErrorMessage();
            } elseif (empty($rev)) {
                self::release($this->key);
                $result['code'] = $this->getErrorCode();
                $result['msg'] = $this->getErrorMessage();
            } else {
                $result['data'] = TCPProtocol::decode($rev);
            }
        } else {
            self::release($this->key);
            $result['code'] = $this->getErrorCode();
            $result['msg'] = $this->getErrorMessage();
        }
        return $result;
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