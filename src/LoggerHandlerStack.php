<?php

namespace Yannice92\LumenInterceptor;

use App\Logger\Formatter\JsonFormatter;
use App\Logger\Formatter\MessageFormatter;
use App\Logger\Middleware\Guzzle\GuzzleMiddleware;
use GuzzleHttp\HandlerStack;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
