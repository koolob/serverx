<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午4:38
 */

namespace Serverx\Cli;


use Serverx\Protocol\RPCProtocol;

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
        return RPCProtocol::decodeResponse($data);
    }
}