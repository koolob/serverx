<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午2:42
 */

namespace Serverx\Protocol;


class TCPProtocol
{

    public static function encode($data)
    {
        $body = gzcompress($data, 3);
        return pack('N', strlen($body)) . $body;
    }

    public static function decode($data)
    {
        return gzuncompress(substr($data, 4));
    }
}