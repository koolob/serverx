<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午7:20
 */

namespace Serverx\Exception\Server;


use Serverx\Exception\ServerException;

class ConfigError extends ServerException
{
    protected $code = "-1001";
    protected $message = "config is error";
}