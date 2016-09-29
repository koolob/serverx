<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午6:58
 */

namespace Serverx\Exception;


class AppException extends \Exception
{
    protected $code = "-1100";
    protected $message = "app error";
}

class AppNotFound extends AppException
{
    protected $code = "-1101";
    protected $message = "not found error";
}