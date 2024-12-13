<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

require_once './models/Pedido.php';

class CalcularGananciasMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $routeArguments = $route->getArguments();
        $idMesa = $routeArguments['id'] ?? null;
        $parametros = $request->getParsedBody();
        $id_pedido = $parametros["id_pedido"];

        if ($idMesa != null && $id_pedido != null) 
        {
            $ganancias = Pedido::traerPrecio($id_pedido, $idMesa);

            if($ganancias != null)
            {
                $request = $request->withAttribute('ganancias', $ganancias);
            }
            else
            {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(["error" => "No se encontro el precio del pedido asociado a ese id de pedido y id de mesa."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
 
        }
        else
        {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["error" => "No se pueden retribuir los datos necesarios para realizar el cobro de la mesa."]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}