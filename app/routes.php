<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Views\Twig;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'index.twig', [
            'name' => 'john',
            'id' => session_id()
        ]);
    });

    $app->get('/admin/sessions', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'adminSessions.twig', [
            'sessions' => [
                '1' => [
                    'id' => 1,
                    'ip' => 2,
                    'agent' => 'test',
                    'status' => true,
                    'userID' => 12
                ],
                '2' => [
                    'id' => 3,
                    'ip' => 4,
                    'agent' => 'test1',
                    'status' => false,
                ],
                '3' => [
                    'id' => 5,
                    'ip' => 6,
                    'agent' => 'test2',
                    'status' => true,
                    'userID' => 13
                ],
            ]

        ]);
    });

    $app->get('/admin/users', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'adminUsers.twig', [
            'users' => [
                '1' => [
                    'name' => 1,
                    'id' => 2,
                    'agent' => 'test',
                    'status' => true
                ]
            ]

        ]);
    });

    $app->get('/admin', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'adminLogin.twig', [

        ]);
    });

    $app->get('/auth', function (Request $request, Response $response) {
        $view = Twig::fromRequest($request);
        return $view->render($response, 'authenticationPage.twig', [

        ]);
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
