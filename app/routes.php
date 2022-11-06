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

    $app->get('/', '\App\Application\Controllers\HomeController:home')
        ->setName('mainPage');
    $app->get('/auth', '\App\Application\Controllers\HomeController:loginPage')
        ->setName('userAuth');
    $app->get('/reset', '\App\Application\Controllers\HomeController:resetPage')
        ->setName('resetPass');

    $app->get('/admin', '\App\Application\Controllers\AdminController:adminLogin')
        ->setName('adminAuth');

    $app->get('/admin/sessions', '\App\Application\Controllers\AdminController:adminSessions');

//    $app->get('/admin/sessions', function (Request $request, Response $response) {
//        $view = Twig::fromRequest($request);
//        return $view->render($response, 'adminSessions.twig', [
//            'title' => 'Admin - Sessions',
//            'sessions' => [
//                '1' => [
//                    'id' => 1,
//                    'ip' => 2,
//                    'agent' => 'test',
//                    'status' => true,
//                    'userID' => 12
//                ],
//                '2' => [
//                    'id' => 3,
//                    'ip' => 4,
//                    'agent' => 'test1',
//                    'status' => false,
//                ],
//                '3' => [
//                    'id' => 5,
//                    'ip' => 6,
//                    'agent' => 'test2',
//                    'status' => true,
//                    'userID' => 13
//                ],
//            ]
//
//        ]);
//    });



    $app->get('/admin/users', '\App\Application\Controllers\AdminController:adminUsers');


    $app->group('/api', function (Group $group) {
        $group->post('/loginUser', 'App\Application\Controllers\LoginController:doLogin')
            ->setName('apiLogin');
//        $group->post('/records', 'App\Application\Controllers\RecordsController:sendData')
//            ->setName('apiSend');
        $group->post('/adminLogin', '\App\Application\Controllers\AdminController:doLogin')
            ->setName('adminLogin');
        $group->post('/adminLogout', '\App\Application\Controllers\AdminController:doLogout')
            ->setName('adminLogout');
    });


    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
