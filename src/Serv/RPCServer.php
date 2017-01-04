<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午4:39
 */

namespace Serverx\Serv;


use Serverx\Exception\App\EmptyParams;
use Serverx\Exception\App\NotFound;
use Serverx\Protocol\RPCProtocol;
use Serverx\Rpc\Request;
use Serverx\Rpc\Response;
use Serverx\Util\Timeu;

class RPCServer extends TCPServer
{
    const HANDLE_TYPE_RPC = 2;

    protected function handlerReceive($data)
    {
        return RPCServer::handleRPC($this, $data);
    }

    public static function handleRPC(BaseServ $baseServ, $data)
    {
        $request = RPCProtocol::decodeRequest($data, $baseServ->getServerConfig()->getSecret());

        $response = new Response();
        $params = $request->getParams();
        $response->setId($request->getId());
        $response->setSendTime($request->getTime());
        $response->setParams($request->getParams());
        $response->setMethod($request->getMethod());

        if (!$request->isLegal()) {
            $response->setCode(\Serverx\Rpc\Response::ERR_SIGN);
        } else {
            try {
                $result = $baseServ->handle($request->getController(), $request->getAction(), $params, array(), self::HANDLE_TYPE_RPC);
                $response->setCode(\Serverx\Rpc\Response::SUCCESS);
                $response->setResult($result);
            } catch (NotFound $e) {
                $baseServ->warning("404:" . $e->getMessage());
                $response->setCode(\Serverx\Rpc\Response::ERR_NOTFOUND);
                $response->setMessage($e->getMessage());
            } catch (EmptyParams $e) {
                $baseServ->warning("403:" . $e->getMessage());
                $response->setCode(\Serverx\Rpc\Response::ERR_WRONGPARAMS);
                $response->setMessage($e->getMessage());
            } catch (\Exception $e) {
                $baseServ->error("500" . $e->getMessage());
                $response->setCode(\Serverx\Rpc\Response::ERR_SERVER);
                $response->setMessage($e->getMessage());
            }
        }
        $response->setServerTime(Timeu::mTimestamp());
        return RPCProtocol::encodeResponse($response);
    }
}