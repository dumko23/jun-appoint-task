<?php

namespace App\Application\Models;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminModel extends Model
{
    public function doLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody()['request'];
        $session = $request->getAttribute('session_list');

        $admin = $this->getData('password, id, name', $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_ADMIN_TABLE'], 'where email = ', $data['email']);

        if (password_verify($data['password'], $admin['data'][0]['password'])) {

            $_SESSION["admin_id"] = $admin['data'][0]['id'];
            $_SESSION["admin_name"] = $admin['data'][0]['name'];


            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        }
        unset($session["admin_id"]);
        unset($session['admin_name']);
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(206);
    }

    public function getUsers()
    {
        $result = $this->getData('id, name, email, fails_left, blocked', 'Users.users');
        if (isset($result['data'])) {
            return $result['data'];
        } else {
            return $result;
        }
    }

    public function deleteUser(Request $request, Response $response): Response
    {
        $id = $request->getParsedBody()['request'];
        if (intval($this->delete('id', $id, 'Users.users')['data']) > 0) {
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(400);
        }
    }
}
