<?php

use Slim\Routing\RouteContext;

require_once './controllers/Pedido_productoController.php';

class ActualizarTiempoPedidoMiddleware
{
    public function __invoke($request, $handler)
    {
        $response = $handler->handle($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        if ($route) 
        {
            $routeArguments = $route->getArguments();
            $idProducto = $routeArguments['id'] ?? null;

            if ($idProducto) 
            {
                try 
                {
                    $producto = Pedido_productoController::obtenerPorId($idProducto);

                    if (!$producto) 
                    {
                        error_log("Producto con ID $idProducto no encontrado.");
                    }

                    $idPedido = $producto['id_pedido'];
                    $actualizado = true;

                    $objAccesoDatos = AccesoDatos::obtenerInstancia();

                    $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) as pendientes FROM pedido_productos WHERE id_pedido = :id_pedido AND id_empleado IS NULL");
                    $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
                    $consulta->execute();
                    $pendientes = $consulta->fetch(PDO::FETCH_ASSOC)['pendientes'];

                    if ($pendientes > 0) 
                    {
                        $actualizado = false;
                    }

                    if ($actualizado) 
                    {
                        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(tiempo_estimado) as tiempo_maximo FROM pedido_productos WHERE id_pedido = :id_pedido");
                        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
                        $consulta->execute();
                        $tiempoMaximo = $consulta->fetch(PDO::FETCH_ASSOC)['tiempo_maximo'];

                        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET tiempo_estimado = :tiempo_estimado WHERE id = :id_pedido");
                        $consulta->bindValue(':tiempo_estimado', $tiempoMaximo, PDO::PARAM_INT);
                        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_INT);
                        $consulta->execute();

                        error_log("Tiempo estimado del pedido $idPedido actualizado.");
                    } else 
                    {
                        error_log("Pendientes aun por procesar");
                    }
                } catch (Exception $e) 
                {
                    error_log("Error al actualizar el tiempo estimado del pedido asociado al producto $idProducto: " . $e->getMessage());
                }
            }
        }

        return $response;
    }
}