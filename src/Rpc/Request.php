<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午4:23
 */

namespace Serverx\Rpc;


use Serverx\Util\Timeu;

class Request
{
    private $id;
    private $time;
    private $method;
    private $params;
    private $controller;
    private $action;

    private $legal = true;

    /**
     * Request constructor.
     * @param $id
     */
    public function __construct($method, $id = null)
    {
        $this->id = empty($id) ? uniqid() . rand(1000, 9999) : $id;
        $this->time = Timeu::mTimestamp();
        $this->method = $method;
        $methodInfo = explode('.', $method);
        $this->controller = $methodInfo[0];
        $this->action = $methodInfo[1];
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isLegal()
    {
        return $this->legal;
    }

    /**
     * @param boolean $legal
     */
    public function setLegal($legal)
    {
        $this->legal = $legal;
    }

    public static function build($method, $id = null)
    {
        return new Request($method, $id);
    }

    function __toString()
    {
        return $this->method . '|' . json_encode($this->getParams());
    }
}