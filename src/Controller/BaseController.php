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

    private $extras = array();

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

    public function setExtras($extras = array())
    {
        $this->extras = $extras;
    }

    protected function getExtra($key)
    {
        if (isset($this->extras[$key])) {
            return $this->extras[$key];
        } else {
            return null;
        }
    }

    public function checkParams($controller, $action, $paramsNeedKey)
    {
        return true;
    }

    public function beforeMethod($controller, $action, $params)
    {
        return null;
    }
}