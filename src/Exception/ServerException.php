<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午5:32
 */

namespace Serverx\Exception;


class ServerException extends \Exception
{
    protected $code = "-1000";
    protected $message = "server error";
}