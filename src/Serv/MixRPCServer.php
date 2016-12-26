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
        $resuest = RPCProtocol::decodeRequest($data);

//        $method = $resuest->getMethod();
        $response = new Response();
        $params = $resuest->getParams();
        $response->setId($resuest->getId());
        $response->setSendTime($resuest->getTime());
        $response->setParams($resuest->getParams());
        $response->setMethod($resuest->getMethod());

        try {
            $result = $this->handle($resuest->getController(), $resuest->getAction(), $params);
            $response->setCode(\Serverx\Rpc\Response::SUCCESS);
            $response->setResult($result);
        } catch (\Exception $e) {

        }
        $response->setServerTime(Timeu::mTimestamp());
        return RPCProtocol::encodeResponse($response);
    }
}