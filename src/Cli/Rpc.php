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
    private $host;
    private $port;
    private $timeout;

    function __construct($host, $port, $timeout = 0.1)
    {
        $this->tcp = TCP::getInstance($host, $port, $timeout);
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    function getResponse(\Serverx\Rpc\Request $request, $retry = 0)
    {
        $data = $this->tcp->getResult(RPCProtocol::encodeRequest($request));
        while ($data['code'] != 0 && $retry > 0) {
            $this->tcp = TCP::getInstance($this->host, $this->port, $this->timeout);
            $data = $this->tcp->getResult(RPCProtocol::encodeRequest($request));
            $retry--;
        }
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