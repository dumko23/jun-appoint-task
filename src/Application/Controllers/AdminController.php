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
            'name' => empty($_SESSION['admin_name'])? '': $_SESSION['admin_name'],
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

        $data = [
            'name' => $session['admin_name'],
            'title' => 'Admin - Sessions',
            'script' => '../js/admin.js',
            'sessions' => $request->getAttribute('session_list')
        ];

//        session_start();
//        if (!empty($_SESSION['admin_name'])) {
//            $data['name'] = $_SESSION['admin_name'];
//        }
        return $this->renderPage($request, $response, 'adminSessions.twig', $data);
    }

    public function adminUsers(Request $request, Response $response): Response
    {
        $session = $request->getAttribute('session_list');
        $data = [
            'title' => 'Admin - Users',
            'name' => $session['admin_name'],
            'script' => '../js/admin.js',
            'users' => $this->getUsers()
        ];


        return $this->renderPage($request, $response, 'adminUsers.twig', $data);
    }

    public function doLogin(Request $request, Response $response): Response
    {
        return $this->model->doLogin($request, $response);
    }

    public function doLogout(Request $request, Response $response): Response
    {
        $_SESSION = [];
        unset($_COOKIE['session_id']);
        unset($_COOKIE['PHPSESSID']);

        session_destroy();

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
