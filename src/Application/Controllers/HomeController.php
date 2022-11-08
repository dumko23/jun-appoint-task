<?php

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController extends Controller
{
    public function home(Request $request, Response $response): Response
    {
        $cookie = $request->getAttribute('session_custom');
        $ip = $request->getAttribute('ip_address');
        $data = [
            'name' => '',
            'id' => $_COOKIE['session_id'],
            'title' => 'Home',
            'attr' => $ip
        ];
        return $this->renderPage($request, $response, 'index.twig', $data);
    }

    public function loginPage(Request $request, Response $response): Response
    {
        $data = [
            'title' => 'Home - Auth',
            'script' => '../js/login.js',
            'page' => 'user'
        ];
        return $this->renderPage($request, $response, 'authenticationPage.twig', $data);
    }

    public function resetPage(Request $request, Response $response): Response
    {
        $data = [
            'title' => 'Home - Password reset',
            'script' => '../js/passwordReset.js'
        ];
        return $this->renderPage($request, $response, 'resetPass.twig', $data);
    }
}
