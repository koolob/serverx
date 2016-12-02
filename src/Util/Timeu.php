<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午3:47
 */

namespace Serverx\Util;


class Timeu
{

    public static function mTimestamp()
    {
        return (int)(microtime(true) * 1000);
    }
}