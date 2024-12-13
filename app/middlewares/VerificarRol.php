<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class VerificarRolMiddleware
{
    private $rolesValidos;
    public function __construct($rolesValidos)
    {
        $this->rolesValidos = is_array($rolesValidos) ? $rolesValidos : [$rolesValidos];
    }
    public function __invoke($request, $handler)
    {
        $token = $request->getAttribute('token');

        if (!$token) {
            $response = new Response();
            $response->getBody()->write(json_encode(["mensaje" => "Acceso denegado: token no válido"]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        if (!isset($token->data->rol) || !in_array($token->data->rol, $this->rolesValidos)) {
            $response = new Response();
            $response->getBody()->write(json_encode(["mensaje" => "Acceso denegado: rol insuficiente"]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}