<?php

namespace App\Application\Models;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminModel extends Model
{
    public function doLogin(Request $request, Response $response)
    {
        $data = $request->getParsedBody()['request'];

        $admin = $this->getData('password, id, name', 'Users.admin_table', 'where email = ', $data['email']);

        if (password_verify($data['password'], $admin['data'][0]['password'])) {
            session_start();
            $_SESSION["admin_id"] = $admin['data'][0]['id'];
            $_SESSION["admin_name"] = $admin['data'][0]['name'];
//            setcookie('name', $admin['data'][0]['name'], time() + 1800);
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        }
        unset($_SESSION["admin"]);
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(403);
    }
}
