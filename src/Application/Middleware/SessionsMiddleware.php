<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class SessionsMiddleware implements Middleware
{
    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $file = scandir(__DIR__ . '/../../../sessions');
        foreach ($file as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            $session = unserialize(
                substr(file_get_contents(__DIR__ . '/../../../sessions/' . $value), 9)
            );
            if (isset($session[array_key_first($session)]['LAST_ACTIVITY']) && (time() - $session[array_key_first($session)]['LAST_ACTIVITY'] > 1800)) {
                unlink(__DIR__ . '/../../../sessions/' . $value);
            }
        }

        session_start();

        if (isset($_SESSION['sessions'][substr(session_id(), 0, 6)]['LAST_ACTIVITY']) && (time() - $_SESSION['sessions'][substr(session_id(), 0, 6)]['LAST_ACTIVITY'] > 1800)) {
            session_unset();
            session_destroy();
        }
        $_SESSION['sessions'][substr(session_id(), 0, 6)]['LAST_ACTIVITY'] = time();

        if (empty($_COOKIE['PHPSESSID'])) {
            $_SESSION['sessions'][substr(session_id(), 0, 6)]['session_name'] = substr(session_id(), 0, 6);
            $_SESSION['sessions'][substr(session_id(), 0, 6)]['session_id'] = session_id();

            setcookie('session_name', substr(session_id(), 0, 6), time() + 1800);
            setcookie('session_id', session_id(), time() + 1800);
        }


        $_SESSION['sessions'][substr(session_id(), 0, 6)]['ip'] = $request->getAttribute('ip_address');
        $_SESSION['sessions'][substr(session_id(), 0, 6)]['user_agent'] = $request->getHeader('User-Agent')[0];
        setcookie('session_id', session_id(), time() + 1800);
        setcookie('session_name', substr(session_id(), 0, 6), time() + 1800);

        $request = $request->withAttribute('session_list', $_SESSION);

        return $handler->handle($request);
    }
}
