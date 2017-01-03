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
    public static function encodeRequest(\Serverx\Rpc\Request $request, $secret = '')
    {
        $data = array(
            'id' => $request->getId(),
            'time' => $request->getTime(),
            'method' => $request->getMethod(),
            'params' => $request->getParams(),
        );
        if (!empty($secret)) {
            $data['sign'] = md5($data['id'] . $data['method'] . $data['time'] . $secret);
        }
        return json_encode($data);
    }

    public static function decodeRequest($data, $secret = '')
    {
        $json = json_decode($data, true);
        $id = $json['id'];
        $time = $json['time'];
        $method = $json['method'];
        $params = $json['params'];

        if ($params == null) {
            $params = array();
        }

        $request = Request::build($method, $id);
        $request->setTime($time);
        $request->setParams($params);

        if (!empty($secret)) {
            if (isset($json['sign'])) {
                if (md5($id . $method . $time . $secret) != $json['sign']) {
                    $request->setLegal(false);
                }
            } else {
                $request->setLegal(false);
            }
        }

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