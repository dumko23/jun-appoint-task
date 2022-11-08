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
        session_start();
        if (empty($_COOKIE['PHPSESSID'])) {
            session_create_id();
            $_SESSION[substr(session_id(), 0, 6)]['session_name'] = substr(session_id(), 0, 6);
            setcookie('session_id', session_id(), time() + 1800);
        }

        setcookie('session_id', session_id(), time() + 1800);

//        session_start();
//        setcookie('id', SID, time() + 1800);
//
        $request = $request->withAttribute('session_custom', $_COOKIE);
        $request = $request->withAttribute('session_list', $_SESSION);

//        $request = $request->withAttribute('sessionID', $_COOKIE['id']);
        return $handler->handle($request);
    }
}
