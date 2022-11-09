<?php

namespace App\Application\Controllers;

use App\Application\Models\UserModel;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends Controller
{
    private $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new UserModel($pdo);
    }

    public function doLogin(Request $request, Response $response): Response
    {
        return $this->model->doLogin($request, $response);
    }

    public function doLogout(Request $request, Response $response): Response
    {
        unset($_SESSION['sessions'][substr(session_id(), 0, 6)]["user_id"]);
        unset($_SESSION['sessions'][substr(session_id(), 0, 6)]["user_name"]);

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    }

    public function doRegister(Request $request, Response $response): Response
    {
        return $this->model->doRegister($request, $response);
    }

    public function sendMail(Request $request, Response $response): Response
    {
        return $this->model->sendMail($request, $response);
    }

    public function acceptCode(Request $request, Response $response): Response
    {
        return $this->model->acceptCode($request, $response);
    }

    public function resetPassword(Request $request, Response $response): Response
    {
        return $this->model->resetPassword($request, $response);
    }
}