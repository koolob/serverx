<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 下午2:56
 */

namespace App\Controller;


use Serverx\Controller\BaseController;

class System extends BaseController
{
    private static $rpc = null;

    public function health($params)
    {
        if (self::$rpc == null) {
            self::$rpc = new \Serverx\Cli\RPC('127.0.0.1', '9797', 0.1);
        }
        $request = \Serverx\Rpc\Request::build('index.index')->setParams(array(
            'a' => 1,
            'b' => 'b'
        ));
        $response = self::$rpc->getResponse($request, 1);
        if (!$response->isSuccess()) {
            echo "failed\n";
        }
        return $response->getResult();
//        return "";
    }

    public function status($params)
    {
        return $this->serv->status();
    }
}