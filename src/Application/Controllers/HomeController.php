<?php

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController extends Controller
{
    public function home(Request $request, Response $response): Response
    {
        $sessions = $request->getAttribute('session_list')['sessions'][substr(session_id(), 0, 6)];

        $data = [
            'script' => '../js/index.js',
            'name' => empty($sessions['user_name']) ? 'stranger' : $sessions['user_name'],
            'title' => 'Home',
            'session' => $sessions,
            'data' => json_encode($sessions),
            'logged_in' => empty($sessions['user_name']) ? false : $sessions['user_name']
        ];
        return $this->renderPage($request, $response, 'index.twig', $data);
    }

    public function loginPage(Request $request, Response $response): Response
    {
        $sessions = $request->getAttribute('session_list')['sessions'][substr(session_id(), 0, 6)];
        if (empty($sessions['user_name'])) {
            $data = [
                'title' => 'Home - Auth',
                'script' => '../js/login.js',
                'page' => 'user'
            ];
            return $this->renderPage($request, $response, 'authenticationPage.twig', $data);
        } else {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

    }

    public function resetPage(Request $request, Response $response): Response
    {
        $data = [
            'title' => 'Home - Password reset',
            'script' => '../js/passwordReset.js'
        ];
        return $this->renderPage($request, $response, 'resetPass.twig', $data);
    }

    public function notFound(Request $request, Response $response): Response
    {
        $data = [
            'title' => '404 Page',
        ];
        return $this->renderPage($request, $response, '_404.twig', $data);
    }
}
