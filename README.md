# serverx
a php server framework

a http server:

    $config = new \Serverx\Conf\ServerConfig();
    $config->setHost('0.0.0.0');
    $config->setPort('8080');
    $config->setDaemonize(0);
    $config->setRunDir(__DIR__);
    $config->setLogDir(__DIR__ . '/logs');
    $config->setLogLevel();
    $config->setMaxRequest(10000);
    $config->setDispatchMode(3);
    $config->registerApp('App', __DIR__);
    $server = new \Serverx\Serv\HttpServer($config);
    //$server->addHandleTypes(\Serverx\Serv\HttpServer::HANDLE_TYPE_HTTP, array(
    //    'system.health',
    //    'system.status',
    //));
    $server->run();