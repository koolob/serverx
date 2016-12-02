<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午4:26
 */

namespace Serverx\Protocol;


use Serverx\Rpc\Request;
use Serverx\Rpc\Response;

class RPCProtocol
{
    public static function encodeRequest(\Serverx\Rpc\Request $request)
    {
        return json_encode(array(
            'id' => $request->getId(),
            'time' => $request->getTime(),
            'method' => $request->getMethod(),
            'params' => $request->getParams(),
        ));
    }

    public static function decodeRequest($data)
    {
        $json = json_decode($data, true);
        $id = $json['id'];
        $time = $json['time'];
        $method = $json['method'];
        $params = $json['params'];
        $request = Request::build($method, $id);
        $request->setTime($time);
        $request->setParams($params);
        return $request;
    }

    public static function encodeResponse(\Serverx\Rpc\Response $response)
    {
        return json_encode(array(
            'id' => $response->getId(),
            'sendTime' => $response->getSendTime(),
            'method' => $response->getMethod(),
            'params' => $response->getParams(),
            'serverTime' => $response->getServerTime(),
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
            'result' => $response->getResult(),
        ));
    }

    public static function decodeResponse($data)
    {
        $json = json_decode($data, true);
        $id = $json['id'];
        $sendTime = $json['sendTime'];
        $method = $json['method'];
        $params = $json['params'];
        $serverTime = $json['serverTime'];
        $code = $json['code'];
        $message = $json['message'];
        $result = $json['result'];
        $response = new Response();
        $response->setId($id);
        $response->setSendTime($sendTime);
        $response->setMethod($method);
        $response->setParams($params);
        $response->setServerTime($serverTime);
        $response->setCode($code);
        $response->setMessage($message);
        $response->setResult($result);
        return $response;
    }
}