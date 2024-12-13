<?php
class ActualizarPrecioPedidoMiddleware
{
    public function __invoke($request, $handler)
    {
        $response = $handler->handle($request);

        $id_pedido = $request->getParsedBody()['id_pedido'] ?? null;
        $id_producto = $request->getParsedBody()['id_producto'] ?? null;

        if (!$id_pedido || !$id_producto) 
        {
            error_log("Faltan parametros: id_pedido o id_producto.");
            return $response->withStatus(400)->write('Faltan parametros');
        }
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consultaPrecio = $objAccesoDatos->prepararConsulta("SELECT precio FROM productos WHERE id = :id_producto");
            $consultaPrecio->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
            $consultaPrecio->execute();
            $producto = $consultaPrecio->fetch(PDO::FETCH_ASSOC);
    
            if ($producto && isset($producto['precio'])) 
            {
                $precioProducto = $producto['precio'];
                $consultaActualizar = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET precio = COALESCE(precio, 0) + :precio WHERE id = :id_pedido");
                $consultaActualizar->bindValue(':precio', $precioProducto, PDO::PARAM_INT); 
                $consultaActualizar->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
                $consultaActualizar->execute();
            } else 
            {
                error_log("El producto con ID $id_producto no tiene un precio asociado o no existe.");
            }
        } catch (Exception $e) 
        {
            error_log("Error al agregar producto al pedido: " . $e->getMessage());
        }
       return $response; 
    }
}
