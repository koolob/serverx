<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午4:38
 */

namespace Serverx\Cli;


use Serverx\Protocol\RPCProtocol;
use Serverx\Rpc\Response;

class RPC
{
    private $tcp;

    function __construct($host, $port)
    {
        $this->tcp = TCP::getInstance($host, $port);
    }

    function getResponse(\Serverx\Rpc\Request $request)
    {
        $data = $this->tcp->getResult(RPCProtocol::encodeRequest($request));
        if ($data['code'] != 0) {
            $response = Response::createErrorResponse($data['code'], $data['msg']);
            $response->setMethod($request->getMethod());
            $response->setParams($request->getParams());
            $response->setSendTime($request->getTime());
            return $response;
        } else {
            return RPCProtocol::decodeResponse($data['data']);
        }
    }
}