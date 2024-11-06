<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_mesa = $parametros['id_mesa'];
        $tiempo_estimado = $parametros['tiempo_estimado'];

        $mesa = new Mesa();
        $mesa->id_mesa = $id_mesa;
        $mesa->tiempo_estimado = $tiempo_estimado;

        try 
        {
            $id_nuevo_mesa = $mesa->crearMesa();
            $payload = json_encode(array("mensaje" => "mesa creada con éxito", "id_mesa" => $id_nuevo_mesa));
            $response = $response->withStatus(201);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al crear la mesa", "error" => $e->getMessage()));
            $response = $response->withStatus(500);
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $id = $args['id'];
        $mesa = Mesa::obtenerMesa($id);
        
        try 
        {
            $mesa = Mesa::obtenerMesa($id);
            
            if ($mesa) 
            {
                $payload = json_encode($mesa);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                $payload = json_encode(array("mensaje" => "Mesa no encontrada"));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
            }
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al obtener la mesa: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $estado = $parametros['estado'];

        try 
        {
            Mesa::modificarEstado($id, $estado);
            $payload = json_encode(array("mensaje" => "Mesa modificada correctamente"));
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar la mesa: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];

        try
        {
            $resultado = Mesa::borrarMesa($id);

            if($resultado)
            {
              $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));  
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se encontro mesa con ese id"));  
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');   
        } catch(Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar borrar la mesa: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }   
    }
}