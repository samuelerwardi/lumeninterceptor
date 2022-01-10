<?php

namespace Yannice92\LumenInterceptor;

use GuzzleHttp\HandlerStack;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Yannice92\LumenInterceptor\Http\Middleware\GuzzleMiddleware;
use Yannice92\LumenInterceptor\Logging\JsonFormatter;
use Yannice92\LumenInterceptor\Logging\MessageFormatter;

class LoggerHandlerStack
{
    /**
     * @param $logName
     * @return HandlerStack
     */
    public function log($logName): HandlerStack
    {
        $logger = new Logger($logName);
        $streamHandler = new StreamHandler('php://stdout');
        $streamHandler->setFormatter(new JsonFormatter());
        $logger->pushHandler($streamHandler);
        $stack = HandlerStack::create();
        $dataFormatter = ['url', 'req_headers', 'req_body', 'res_body', 'error', 'code', 'correlation_id'];
        $guzzleLog = GuzzleMiddleware::log(
            $logger,
            new MessageFormatter($dataFormatter)
        );
        $stack->push($guzzleLog);
        return $stack;
    }
}
