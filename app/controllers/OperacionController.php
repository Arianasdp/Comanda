<?php
require_once './models/Operacion.php';
require_once './interfaces/IApiUsable.php';

class OperacionController extends Operacion implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_usuario = $parametros['id_usuario'];
        $fecha = $parametros['fecha'];

        $operacion = new Operacion();
        $operacion->id_usuario = $id_usuario;
        $operacion->fecha = $fecha;

        try 
        {
            $id_nuevo_operacion = $operacion->crearOperacion();
            $payload = json_encode(array("mensaje" => "Operacion creada con éxito", "id_asistencia" => $id_nuevo_operacion));
            $response = $response->withStatus(201);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al crear la operacion", "error" => $e->getMessage()));
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
            $operacion = Operacion::obtenerOperacion($id);
            
            if ($operacion) 
            {
                $payload = json_encode($operacion);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                $payload = json_encode(array("mensaje" => "Operacion no encontrada"));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
            }
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al obtener la operacion: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Operacion::obtenerTodos();
        $payload = json_encode(array("listaOperaciones" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $id_usuario = $parametros['id_usuario'];
        $fecha = $parametros['fecha'];

        try 
        {
            Operacion::modificarOperacion($id, $id_usuario, $fecha);
            $payload = json_encode(array("mensaje" => "Operacion modificada correctamente"));
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar la operacion: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];

        try
        {
            $resultado = Operacion::borrarOperacion($id);

            if($resultado)
            {
              $payload = json_encode(array("mensaje" => "Operacion borrada con exito"));  
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se encontro operacion con ese id"));  
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');   
        } catch(Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar borrar la operacion: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }   
    }
}