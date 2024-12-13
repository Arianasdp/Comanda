<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

require_once './models/Pedido.php';
require_once './models/Pedido_producto.php';
class VerificarPedidoListoMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $routeArguments = $route->getArguments();
        $idPedido = $routeArguments['id'] ?? null;

        $estadoActual = Pedido::traerEstado($idPedido);

        if($estadoActual == "en preparacion")
        {
            if(Pedido_producto::todosLosProductosListos($idPedido))
            {
                return $handler->handle($request);
            }
            else
            {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(["error" => "No se puede actualizar el estado del pedido porque no todos los productos del mismo estan listos."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }  
        }

        return $handler->handle($request);
        
    }
}