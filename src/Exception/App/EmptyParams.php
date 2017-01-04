<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/1/4
 * Time: 上午11:22
 */

namespace Serverx\Exception\App;


use Serverx\Exception\AppException;

class EmptyParams extends AppException
{
    protected $code = "-1102";
    protected $message = "empty params error";
}