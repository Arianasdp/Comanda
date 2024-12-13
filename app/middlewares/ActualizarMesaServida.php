<?php

use Slim\Routing\RouteContext;
require_once './models/Mesa.php';
require_once './models/Pedido.php';
class ActualizarMesaServidaMiddleware
{
    public function __invoke($request, $handler)
    {
        $response = $handler->handle($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        if ($route) 
        {
            $routeArguments = $route->getArguments();
            $idPedido = $routeArguments['id'] ?? null;

            if ($idPedido) 
            {
                $id_mesa = Pedido::traerIdMesa($idPedido);

                if($id_mesa)
                {
                    Mesa::modificarEstado($id_mesa, "con clientes comiendo");
                }
                else
                {
                    error_log("No se puede modificar el estado de la mesa, id no encontrado.");
                }
            }
            else
            {
                error_log("No se encuentra el id del pedido requerido.");
            }
        }

        return $response;
    }
}