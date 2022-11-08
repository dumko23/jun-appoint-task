<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class AdminRoutesMiddleware implements Middleware
{
    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
//        session_start();

//        if (empty($_COOKIE['PHPSESSID'])) {
//            session_create_id();
//            $_SESSION[substr(session_id(), 0, 6)]['session_name'] = substr(session_id(), 0, 6);
//            setcookie('session_id', session_id(), time() + 1800);
//        }
//        setcookie('session_id', session_id(), time() + 1800);
//        $request = $request->withAttribute('session_custom', $_COOKIE);
//        $request = $request->withAttribute('session_list', $_SESSION);



        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $routeName = $route->getName();
        $publicRoutesArray = [
            'mainPage',
            'userAuth',
            'resetPass',
            'adminAuth',
            'userLogin',
            'userRegister',
            'sendMail',
            'acceptCode',
            'resetPass',
            'adminLogin'
        ];

        $session = $request->getAttribute('session_list');


        if (empty($session['admin_id']) && (!in_array($routeName, $publicRoutesArray))) {
            // Create a redirect for a named route
            $routeParser = $routeContext->getRouteParser();
            $url = $routeParser->urlFor('adminAuth');

            $response = new \Slim\Psr7\Response();
            return $response->withStatus(401);
        } else {
            $request = $request->withAttribute('session_list', $session);

            return $handler->handle($request);
        }
    }
}