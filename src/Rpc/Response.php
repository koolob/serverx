<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/2
 * Time: 下午4:23
 */

namespace Serverx\Rpc;


use Serverx\Util\Timeu;

class Response
{
    const ERR_TIMEOUT = 1001;
    const ERR_RECEIVE = 1002;
    const ERR_SERVER = 1009;
    const ERR_DATA = 1010;

    const SUCCESS = 1;
    const ERR_NOTFOUND = -1;
    const ERR_WRONGPARAMS = -2;
    const ERR_SIGN = -3;

    private $id;
    private $sendTime;
    private $method;
    private $params;

    private $serverTime;
    private $code = 0;
    private $message = null;
    private $result;

    private $retTime;

    function __construct()
    {
        $this->retTime = Timeu::mTimestamp();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * @param mixed $sendTime
     */
    public function setSendTime($sendTime)
    {
        $this->sendTime = $sendTime;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
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
    }

    /**
     * @return mixed
     */
    public function getServerTime()
    {
        return $this->serverTime;
    }

    /**
     * @param mixed $serverTime
     */
    public function setServerTime($serverTime)
    {
        $this->serverTime = $serverTime;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getCostTime()
    {
        return $this->retTime - $this->sendTime;
    }

    public function isSuccess()
    {
        return $this->getCode() === self::SUCCESS;
    }

    function __toString()
    {
        return $this->method . '|' . $this->getCostTime() . '|' . $this->getCode() . '|' . $this->getMessage() . '|' . json_encode($this->getParams()) . '|' . json_encode($this->getResult());
    }


    static function createErrorResponse($code, $message)
    {
        $response = new Response();
        $response->setCode($code);
        $response->setMessage($message);
        return $response;
    }
}