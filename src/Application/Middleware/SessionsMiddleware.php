<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SessionsMiddleware implements Middleware
{
    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        setcookie('session_id', session_id(), time() + 1800);
        setcookie('session_ip', json_encode($request->getAttributes()), time() + 1800);

//        session_start();
//        setcookie('id', SID, time() + 1800);
//
        $request = $request->withAttribute('session_custom', $_COOKIE);
//        $request = $request->withAttribute('sessionID', $_COOKIE['id']);
        return $handler->handle($request);
    }
}
