<?php

require_once './models/Pedido_producto.php';
require_once './interfaces/IApiUsable.php';

class Pedido_productoController extends Pedido_producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        $id_producto = $parametros['id_producto'];

        $producto = new Pedido_producto();
        $producto->id_pedido = $id_pedido;
        $producto->id_producto = $id_producto;

        try 
        {
            if(isset($parametros['id_pedido'], $parametros['id_producto'])
            && !empty($parametros['id_pedido'])
            && !empty($parametros['id_producto']))
            $id_nuevo_producto = $producto->crearProducto();
            $payload = json_encode(array("mensaje" => "Producto creado con éxito", "id_producto" => $id_nuevo_producto));
            $response = $response->withStatus(201);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al crear el producto", "error" => $e->getMessage()));
            $response = $response->withStatus(500);
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $id = $args['id'];
        
        try 
        {
            $producto = Pedido_producto::obtenerProducto($id);
            
            if ($producto) 
            {
                $payload = json_encode($producto);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                $payload = json_encode(array("mensaje" => "Producto no encontrado"));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
            }
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al obtener el producto: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $pedidosFiltrados = $request->getAttribute('pedidos_filtrados');

        if ($pedidosFiltrados) {
            $payload = json_encode($pedidosFiltrados);
        } else {
            $payload = json_encode(["mensaje" => "No se encontraron pedidos filtrados."]);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
       
    }

    public function TraerTodos2($request, $response, $args)
    {
        $lista = Pedido_producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    
    public function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
        $parametros = $request->getParsedBody();
        if(isset($parametros['tiempo_estimado']))
        {
           $tiempo_estimado = $parametros['tiempo_estimado']; 
        }
        $idEmpleado = $request->getAttribute('id_empleado');
        
        try 
        {
            $resultado = Pedido_producto::traerEstado($id);

            if($resultado && $resultado == 'pendiente')
            {
                Pedido_producto::modificarPedido($id, $idEmpleado, $tiempo_estimado);
                $payload = json_encode(array("mensaje" => "Estado del producto modificado"));
            }
            else
            {
                Pedido_producto::marcarComoEntregado($id);
                $payload = json_encode(array("mensaje" => "Producto marcado como entregado"));
            }
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar el producto: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function PedidoListo($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $pedido_id = $parametros['pedido_id'];
        $resultado = Pedido_producto::todosLosProductosListos($pedido_id);

        if($resultado)
        {
            $payload = json_encode(array("mensaje" => "Pedido listo para servir"));  
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Faltan productos por preparar"));
        }
    }

    public static function obtenerPorId($idProducto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(" SELECT id_pedido FROM pedido_productos WHERE id = :id_producto");
        $consulta->bindValue(':id_producto', $idProducto, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        try
        {
            $resultado = Pedido_producto::borrarProducto($id);

            if($resultado)
            {
              $payload = json_encode(array("mensaje" => "Producto borrado con exito"));  
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se encontro producto con ese id"));  
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');   
        } catch(Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar borrar el producto: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }   
    }

    public static function TraerProductosEntregadosTarde($request, $response)
    {
        $lista = Pedido_producto::obtenerFueraDeTiempo();
        $payload = json_encode(array("lista Productos entregados fuera de tiempo" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}