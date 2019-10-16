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

    /**
     * 当前微妙时间戳
     * @return int
     */
    public static function microTimestamp()
    {
        list($usec, $sec) = explode(" ", microtime());
        return $sec . sprintf("%06d", $usec * 1000000);
    }
}