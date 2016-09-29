<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/9/29
 * Time: 下午7:02
 */

namespace Serverx\Controller;


use Serverx\Serv\BaseServ;

class BaseController
{
    protected $serv;

    private $alias = array();

    function __construct(BaseServ $baseServ)
    {
        $this->serv = $baseServ;
    }

    public function getActionMethod($actionName)
    {
        if (isset($this->alias[$actionName])) {
            return $this->alias[$actionName];
        } else {
            return $actionName;
        }
    }

    protected function registerAlias($aliseName, $actionMethod)
    {
        $this->alias[$aliseName] = $actionMethod;
    }
}