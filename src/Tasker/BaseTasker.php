<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 17/1/6
 * Time: 下午5:05
 */

namespace Serverx\Tasker;


use Serverx\Serv\BaseServ;

abstract class BaseTasker
{
    protected $serv;

    function __construct(BaseServ $baseServ)
    {
        $this->serv = $baseServ;
    }

    public function run(array $params = array())
    {
        $name = get_class($this);
        $taskData = array(
            'class' => $name,
            'params' => $params,
        );
        $this->serv->run_task($taskData);
    }

    public abstract function exec($params);

    public abstract function after($params, $result);
}