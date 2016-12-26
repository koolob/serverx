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
    public function health($params)
    {
        return array();
    }

    public function status($params)
    {
        return $this->serv->status();
    }
}