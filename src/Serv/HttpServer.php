<?php
/**
 * Created by PhpStorm.
 * User: dong
 * Date: 16/12/26
 * Time: 上午11:03
 */

namespace Serverx\Serv;


use Serverx\Conf\ServerConfig;
use Serverx\Exception\App\NotFound;

class HttpServer extends BaseServ
{
    const HANDLE_TYPE_HTTP = 1;

    protected function initSwooleServer(ServerConfig $config)
    {
        $server = new \swoole_http_server($config->getHost(), $config->getPort());
        $server->set($config->toSwooleConfigArray());
        $server->on('Start', array($this, 'onSwooleStart'));
        $server->on('Shutdown', array($this, 'onSwooleShutdown'));
        $server->on('WorkerStart', array($this, 'onWorkerStart'));

        $server->on('request', array($this, 'onRequest'));

        if ($config->isTaskEnable()) {
            $server->on('Task', array($this, 'onTask'));
            $server->on('Finish', array($this, 'onFinish'));
        }
        return $server;
    }

    protected function getPidFile()
    {
        $appDir = $this->getServerConfig()->getRunDir();
        $runDir = $appDir . DIRECTORY_SEPARATOR . 'run';
        if (!file_exists($runDir)) {
            mkdir($runDir);
        }
        return $runDir . DIRECTORY_SEPARATOR . 'serverx_http.pid';
    }

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        HttpServer::handlerHttp($this, $request, $response);
    }

    public static function handlerHttp(BaseServ $baseServ, \swoole_http_request $request, \swoole_http_response $response)
    {
        $path = $request->server['path_info'];
        $pathInfos = explode("/", $path);
        if (sizeof($pathInfos) != 3) {
            //not /xxx/xxx
            $response->status(404);
            $response->end('');
        } else {
            $controller = $pathInfos[1];
            $action = $pathInfos[2];

            $get = array();
            $post = array();

            if (!empty($request->post)) {
                $post = $request->post;
            }
            if (!empty($request->get)) {
                $get = $request->get;
            }

            $params = array_merge($get, $post);

            $extras = array(
                'GET' => $get,
                'POST' => $post,
                'HEADER' => $request->header,
                'SERVER' => $request->server,
            );

            if (isset($request->header['accept-encoding'])) {
                $accept_encoding = $request->header['accept-encoding'];
                if (strpos($accept_encoding, 'gzip') !== false) {
                    $response->gzip(3);
                }
            }

            try {
                $result = $baseServ->handle($controller, $action, $params, $extras, self::HANDLE_TYPE_HTTP);
                if (is_array($result)) {
                    $result = json_encode($result);
                }
                if (isset($params['callback'])) {
                    $callback = $params['callback'];
                    $result = $callback . '(' . $result . ')';
                }
                $response->header('Content-type', 'application/json');
                $response->status(200);
                $response->end($result);
            } catch (NotFound $e) {
                $response->status(404);
                $response->end('');
            } catch (\Exception $e) {
                $response->status(500);
                $response->end('');
            }
        }
    }
}