<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        $nombre = $parametros['nombre'];
        $sector = $parametros['sector'];

        $producto = new Producto();
        $producto->id_pedido = $id_pedido;
        $producto->nombre = $nombre;
        $producto->sector = $sector;

        try 
        {
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
            $producto = Producto::obtenerProducto($id);
            
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
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        try 
        {
            Producto::marcarComoEntregado($id);
            $payload = json_encode(array("mensaje" => "Producto marcado como entregado"));
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar el producto: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        try
        {
            $resultado = Producto::borrarProducto($id);

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
}