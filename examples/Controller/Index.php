<?php

/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午5:51
 */
namespace App\Controller;

use App\Tasker\SlowTasker;

class Index extends \Serverx\Controller\BaseController
{
    public function index($params)
    {
        $tasker = new SlowTasker($this->serv);
        $tasker->run(array('hi' => 1));
        return array("hi" => "1");
    }
}