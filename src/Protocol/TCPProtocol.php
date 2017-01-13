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

    public static function encode($data, $gzip = false)
    {
        if ($gzip) {
            $body = gzcompress($data, 3);
        } else {
            $body = $data;
        }
        return pack('N', strlen($body)) . $body;
    }

    public static function decode($data, $gzip = false)
    {
        if ($gzip) {
            return gzuncompress(substr($data, 4));
        } else {
            return substr($data, 4);
        }
    }
}