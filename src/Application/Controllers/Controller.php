<?php

namespace App\Application\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Controller
{
    public function renderPage(Request $request, Response $response, string $page, array $data): Response
    {
        $view = Twig::fromRequest($request);
        return $view->render($response, $page, $data);
    }
}
