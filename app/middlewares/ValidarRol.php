<?php

use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

require_once './models/Usuario.php';

class ValidarRolMiddleware
{
    private $rolesValidos;
    public function __construct($rolesValidos)
    {
        $this->rolesValidos = is_array($rolesValidos) ? $rolesValidos : [$rolesValidos];
    }

    public function __invoke($request, $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $routeArguments = $route->getArguments();
        $idempleado = $routeArguments['id'] ?? null;

        $empleado = Usuario::obtenerUsuarioPorId($idempleado);

        if(!$empleado || !in_array($empleado->rol, $this->rolesValidos))
        {
            $response = new Response();
            $response->getBody()->write(json_encode(["mensaje" => "No se puede dar de baja a un usuario que no es uno de nuestros empleados"]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}