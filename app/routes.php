<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

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

    $app->get('/admin/sessions', '\App\Application\Controllers\AdminController:adminSessions')
        ->setName('adminSessions');
    $app->get('/admin/users', '\App\Application\Controllers\AdminController:adminUsers')
        ->setName('adminUsers');

    $app->group('/api', function (Group $group) {
        $group->post('/userLogin', '\App\Application\Controllers\UserController:doLogin')
            ->setName('userLogin');
        $group->post('/userRegister', '\App\Application\Controllers\UserController:doRegister')
            ->setName('userRegister');
        $group->post('/sendMail', '\App\Application\Controllers\UserController:sendMail')
            ->setName('sendMail');
        $group->post('/acceptCode', '\App\Application\Controllers\UserController:acceptCode')
            ->setName('acceptCode');
        $group->post('/resetPass', '\App\Application\Controllers\UserController:resetPassword')
            ->setName('resetPass');
        $group->post('/userLogout', '\App\Application\Controllers\UserController:doLogout')
            ->setName('userLogout');
    });

    $app->group('/admin', function (Group $group) {
        $group->post('/adminLogin', '\App\Application\Controllers\AdminController:doLogin')
            ->setName('adminLogin');
        $group->post('/adminLogout', '\App\Application\Controllers\AdminController:doLogout')
            ->setName('adminLogout');
        $group->post('/deleteUser', '\App\Application\Controllers\AdminController:deleteUser')
            ->setName('deleteUser');
    });

    $app->get('/notFound', '\App\Application\Controllers\HomeController:notFound')
        ->setName('notFound');
};
