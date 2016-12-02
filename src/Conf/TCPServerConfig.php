<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/11/30
 * Time: 下午1:57
 */

namespace Serverx\Conf;


class TCPServerConfig extends ServerConfig
{
    public function toSwooleConfigArray()
    {
        $config = parent::toSwooleConfigArray();
        $config['open_length_check'] = 1;
        $config['package_length_type'] = 'N';
        $config['package_max_length'] = 2097152;
        $config['package_length_offset'] = 0;
        $config['package_body_offset'] = 4;
        return $config;
    }
}