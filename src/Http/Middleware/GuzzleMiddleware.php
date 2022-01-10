<?php

namespace Yannice92\LumenInterceptor\Http\Middleware;

use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Yannice92\LumenInterceptor\Logging\MessageFormatter;

class GuzzleMiddleware
{
    /**
     * Middleware that logs requests, responses, and errors using a message
     * formatter.
     *
     * @param LoggerInterface $logger Logs messages.
     * @param MessageFormatter $formatter Formatter used to create message strings.
     * @param string $logLevel Level at which to log requests.
     *
     * @return callable Returns a function that accepts the next handler.
     */
    public static function log(LoggerInterface $logger, MessageFormatter $formatter, $logLevel = 'info' /* \Psr\Log\LogLevel::INFO */)
    {
        return function (callable $handler) use ($logger, $formatter, $logLevel) {
            return function ($request, array $options) use ($handler, $logger, $formatter, $logLevel) {
                return $handler($request, $options)->then(
                    function ($response) use ($logger, $request, $formatter, $logLevel) {
                        $message = $formatter->format($request, $response);
                        $logger->log($logLevel, '', $message);

                        return $response;
                    },
                    function ($reason) use ($logger, $request, $formatter) {
                        $response = $reason instanceof RequestException
                            ? $reason->getResponse()
                            : null;
                        $message = $formatter->format($request, $response, $reason);
                        $logger->notice('', $message);
                        return \GuzzleHttp\Promise\rejection_for($reason);
                    }
                );
            };
        };
    }
}
