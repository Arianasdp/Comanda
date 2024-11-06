<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_mesa = $parametros['id_mesa'];
        $tiempo_estimado = $parametros['tiempo_estimado'];

        $pedido = new Pedido();
        $pedido->id_mesa = $id_mesa;
        $pedido->tiempo_estimado = $tiempo_estimado;

        try 
        {
            $id_nuevo_pedido = $pedido->crearPedido();
            $payload = json_encode(array("mensaje" => "Pedido creado con éxito", "id_pedido" => $id_nuevo_pedido));
            $response = $response->withStatus(201);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al crear el pedido", "error" => $e->getMessage()));
            $response = $response->withStatus(500);
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $id = $args['id'];
        $pedido = Pedido::obtenerPedido($id);
        
        try 
        {
            $pedido = Pedido::obtenerPedido($id);
            
            if ($pedido) 
            {
                $payload = json_encode($pedido);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
            }
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al obtener el pedido: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $args['id'];

        try 
        {
            if (isset($parametros['nuevoEstado'])) {
                $nuevoEstado = $parametros['nuevoEstado'];
                Pedido::modificarPedido($id, $nuevoEstado);
                $payload = json_encode(array("mensaje" => "Estado del pedido actualizado"));
            } else 
            {
                Pedido::marcarComoEntregado($id);
                $payload = json_encode(array("mensaje" => "Pedido marcado como entregado"));
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar el pedido: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];

        try
        {
            $resultado = Pedido::borrarPedido($id);

            if($resultado)
            {
              $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));  
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se encontro pedido con ese id"));  
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');   
        } catch(Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar borrar el pedido: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }   
    }
}
