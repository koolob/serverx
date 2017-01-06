<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/1/6
 * Time: 下午5:27
 */

namespace App\Tasker;


use Serverx\Tasker\BaseTasker;

class SlowTasker extends BaseTasker
{

    public function exec($params)
    {
        var_dump($params);
        sleep(5);
        return array("abc" => 123);
    }

    public function after($params, $result)
    {
        var_dump($params);
        var_dump($result);
    }
}