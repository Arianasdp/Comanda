<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;

require_once './models/Operacion.php';

class RegistrarOperacionMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $response = $handler->handle($request);

        $responseBody = (string)$response->getBody();
        
        $data = json_decode($responseBody, true);

        $ruta = $request->getUri()->getPath();
        $metodo = $request->getMethod();
        $token = $request->getAttribute('token');
        $accion = $this->determinarAccion($ruta, $metodo, $request, $data);

        if ($accion) 
        {
            $operacion = new Operacion();
            $operacion->id_usuario = $token->data->id;
            $operacion->accion = $accion;
            $operacion->crearOperacion();
        }
        else
        {
            error_log("Ocurrio un problema al querer registrar la operacion");
        }

        return $response;
    }

    private function determinarAccion(string $ruta, string $metodo, Request $request, $data): ?string
    {
        //mozo
        if ($ruta === '/mozo/mesalibre' && $metodo === 'GET') {
            return 'Buscar mesa libre';
        }

        if (preg_match('/^\/mozo\/mesa\/\d+$/', $ruta) && $metodo === 'PUT') 
        {
            return 'Asignar mesa';
        }

        if ($ruta === '/mozo/pedido' && $metodo === 'POST') {
            return 'Registrar un nuevo pedido';
        }
    
        if ($ruta === '/mozo/productos' && $metodo === 'GET') {
            return 'Mostrar menu con productos disponibles';
        }
    
        if ($ruta === '/mozo/producto' && $metodo === 'POST') {
            return 'Registrar un nuevo producto en un pedido';
        }

        if ($ruta === '/mozo/pedidos/listos' && $metodo === 'GET') {
            return 'Chequear pedidos listos para servir';
        }
    
        if (preg_match('/^\/mozo\/pedido\/\d+$/', $ruta) && $metodo === 'PUT') {
            return 'Servir pedido';
        }
    
        if (preg_match('/^\/mozo\/cobro\/mesa\/\d+$/', $ruta) && $metodo === 'PUT') {
            return 'Registrar el cobro de una mesa';
        }
    
        //empleados
        if ($ruta === '/empleado/' && $metodo === 'GET') {
            return 'Chequear los pedidos pendientes de su sector';
        }
    
        if (preg_match('/^\/empleado\/pedido\/\d+$/', $ruta) && $metodo === 'PUT') {
        
            if (isset($data['mensaje'])) {
                if (str_contains($data['mensaje'], 'Pedido marcado como entregado al mozo')) {
                    return 'Marcar pedido como entregado al mozo';
                } elseif (str_contains($data['mensaje'], 'Estado del pedido actualizado')) {
                    return 'Asignarse el pedido';
                }
            }
        
            return 'Actualizar el estado de un pedido';
        }
    
        if (preg_match('/^\/empleado\/producto\/\d+$/', $ruta) && $metodo === 'PUT') {
            if (isset($data['mensaje'])) {
                if (str_contains($data['mensaje'], 'Estado del producto modificado')) {
                    return 'Asignarse el producto';
                } elseif(str_contains($data['mensaje'], 'Producto marcado como entregado')) {
                    return 'Marcar producto como listo para servir';
                }
            }
            return 'Actualizar el estado de un producto';
        }
    
        return null;
    }
}