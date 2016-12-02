<?php

/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午5:51
 */
namespace App\Controller;

class Index extends \Serverx\Controller\BaseController
{
    public function index($params)
    {
        return array("hi" => "1");
    }
}