<?php

namespace App\Application\Models;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserModel extends Model
{
    public function doLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody()['request'];

        $user = $this->getData('password, id, name', 'Users.users', 'where email = ', $data['email']);

        if (password_verify($data['password'], $user['data'][0]['password'])) {
            session_start();
            $_SESSION["user_id"] = $user['data'][0]['id'];
            $_SESSION["user_name"] = $user['data'][0]['name'];
//            setcookie('name', $admin['data'][0]['name'], time() + 1800);
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        }
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(403);
    }

    public function doRegister(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody()['request'];
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = $this->add($data, 'Users.users');
        if (in_array('data', $user)) {
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        }
        $response->getBody()->write(json_encode($user));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(403);
    }
}
