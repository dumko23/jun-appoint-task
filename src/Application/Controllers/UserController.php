<?php

namespace App\Application\Controllers;

use App\Application\Models\AdminModel;
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

    public function doRegister(Request $request, Response $response): Response
    {
        return $this->model->doRegister($request, $response);
    }
}