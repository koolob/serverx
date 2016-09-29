<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午7:19
 */

namespace Serverx\Exception\App;


use Serverx\Exception\AppException;

class NotFound extends AppException
{
    protected $code = "-1101";
    protected $message = "not found error";
}