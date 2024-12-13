<?php

require_once './models/Producto.php';

class ActualizarStockMiddleware
{
    public function __invoke($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros["id_producto"];

        if($id != null)
        {
            Producto::modificarStock($id);
        }
        else
        {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["error" => "No se pueden retribuir los datos necesarios para restar el stock de producto."]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        return $handler->handle($request);
    }
}