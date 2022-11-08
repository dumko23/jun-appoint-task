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
            'adminLogin',
            'userLogout'
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