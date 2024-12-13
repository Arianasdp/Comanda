<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require_once './models/Pedido.php';
require_once './models/Producto.php';

class ValidarPedidoYProductoMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        $id_producto = $parametros['id_producto'];

        $estadoActual = Pedido::traerEstado($id_pedido);
        $producto = Producto::obtenerProducto($id_producto);

        if($estadoActual == "pendiente" && $producto)
        {
            $response = $handler->handle($request);
        }
        else
        {
            if(!$producto)
            {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(["error" => "No se puede agregar un producto inexistente al pedido."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');  
            }
            else
            {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(["error" => "No se pueden agregar productos a un pedido en estado {$estadoActual}."]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');  
            }   
        }

        return $response;
    }
}