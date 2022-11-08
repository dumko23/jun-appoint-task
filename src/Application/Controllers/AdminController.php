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
        session_start();

        $data = [
            'name' => '',
            'title' => 'Admin - Login',
            'script' => '../js/adminLogin.js',
            'page' => 'admin'
        ];
        return $this->renderPage($request, $response, 'adminLogin.twig', $data);
    }

    public function adminSessions(Request $request, Response $response): Response
    {
        $data = [
            'name' => 'stranger',
            'title' => 'Admin - Sessions',
            'script' => '../js/admin.js'
        ];

        session_start();
        if (!empty($_SESSION['admin_name'])) {
            $data['name'] = $_SESSION['admin_name'];
        }
        return $this->renderPage($request, $response, 'adminSessions.twig', $data);
    }

    public function adminUsers(Request $request, Response $response): Response
    {
        $data = [
            'title' => 'Admin - Users',
            'name' => 'stranger',
            'script' => '../js/admin.js',
            'users' => $this->getUsers()
        ];
        session_start();
        if (!empty($_SESSION['admin_name'])) {
            $data['name'] = $_SESSION['admin_name'];
        }

        return $this->renderPage($request, $response, 'adminUsers.twig', $data);
    }

    public function doLogin(Request $request, Response $response): Response
    {
        return $this->model->doLogin($request, $response);
    }

    public function doLogout(Request $request, Response $response): Response
    {
        session_start();
        $_SESSION = [];
        print_r($_COOKIE);
        unset($_COOKIE['name']);
        unset($_COOKIE['PHPSESSID']);
        print_r($_COOKIE);
        setcookie('PHPSESSID', null, -1, '/');
        setcookie('name', null, -1, '/');
        session_destroy();
//        header('Location: /admin');
        return $response;
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
