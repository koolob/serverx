<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/11/30
 * Time: 下午1:33
 */

namespace Serverx\Protocol;


interface BaseProtocol
{
    public function encode($data);

    public function decode($data);
}