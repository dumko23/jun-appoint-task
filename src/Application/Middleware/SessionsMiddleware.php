<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionsMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        session_start();
        setcookie('id', SID, time() + 1800);


        $request = $request->withAttribute('sessionID', $_COOKIE['id']);
        return $handler->handle($request);
    }
}
