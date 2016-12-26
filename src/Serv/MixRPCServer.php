<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 上午11:59
 */

namespace Serverx\Serv;


use Serverx\Protocol\RPCProtocol;
use Serverx\Rpc\Response;
use Serverx\Util\Timeu;

class MixRPCServer extends MixServer
{
    protected function handlerReceive($data)
    {
        return RPCServer::handleRPC($this, $data);
    }

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        HttpServer::handlerHttp($this, $request, $response);
    }

}