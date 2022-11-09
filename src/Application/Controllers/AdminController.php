<?php

namespace App\Application\Controllers;

use App\Application\Models\AdminModel;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class AdminController extends Controller
{
    private $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new AdminModel($pdo);
    }

    public function adminLogin(Request $request, Response $response): Response
    {
        $loggedIn = empty($_SESSION['admin_name']);

        $data = [
            'name' => empty($_SESSION['admin_name']) ? '' : $_SESSION['admin_name'],
            'title' => 'Admin - Login',
            'script' => '../js/adminLogin.js',
            'page' => 'admin',
            'logged' => $loggedIn
        ];
        return $this->renderPage($request, $response, 'adminLogin.twig', $data);
    }

    public function adminSessions(Request $request, Response $response): Response
    {
        $session = $request->getAttribute('session_list');

        $file = scandir(__DIR__ . '/../../../sessions');
        $sessionsList = [];
        foreach ($file as $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }

            $sessionsList[] = unserialize(
                substr(file_get_contents(__DIR__ . '/../../../sessions/' . $value), 9)
            );
        }


        $newSessionList = [];
        foreach ($sessionsList as $value){
            $newSessionList[array_key_first($value)] = $value[array_key_first($value)];
        }

        $data = [
            'name' => $session['admin_name'],
            'title' => 'Admin - Sessions',
            'script' => '../js/admin.js',
            'sessions' => $newSessionList
        ];

        return $this->renderPage($request, $response, 'adminSessions.twig', $data);
    }

    public function adminUsers(Request $request, Response $response): Response
    {
        $session = $request->getAttribute('session_list');
        $users = $this->getUsers();
        $data = [
            'title' => 'Admin - Users',
            'name' => $session['admin_name'],
            'script' => '../js/admin.js',
            'users' => isset($users[0]) ? $users : ''
        ];


        return $this->renderPage($request, $response, 'adminUsers.twig', $data);
    }

    public function doLogin(Request $request, Response $response): Response
    {
        return $this->model->doLogin($request, $response);
    }

    public function doLogout(Request $request, Response $response): Response
    {
        unset($_SESSION["admin_id"]);
        unset($_SESSION["admin_name"]);


        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    }

    public function getUsers()
    {
        return $this->model->getUsers();
    }

    public function deleteUser(Request $request, Response $response): Response
    {
        return $this->model->deleteUser($request, $response);
    }
}
