<?php
require_once './models/Asistencia.php';
require_once './interfaces/IApiUsable.php';

class AsistenciaController extends Asistencia implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_usuario = $parametros['id_usuario'];
        $fecha = $parametros['fecha'];

        $asistencia = new Asistencia();
        $asistencia->id_usuario = $id_usuario;
        $asistencia->fecha = $fecha;

        try 
        {
            $id_nuevo_asistencia = $asistencia->crearAsistencia();
            $payload = json_encode(array("mensaje" => "Asistencia creada con éxito", "id_asistencia" => $id_nuevo_asistencia));
            $response = $response->withStatus(201);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al crear la asistencia", "error" => $e->getMessage()));
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
            $asistencia = Asistencia::obtenerAsistencia($id);
            
            if ($asistencia) 
            {
                $payload = json_encode($asistencia);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                $payload = json_encode(array("mensaje" => "Asistencia no encontrada"));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
            }
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al obtener la asistencia: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Asistencia::obtenerTodos();
        $payload = json_encode(array("listaAsistencias" => $lista));

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
            Asistencia::modificarAsistencia($id, $id_usuario, $fecha);
            $payload = json_encode(array("mensaje" => "Asistencia modificada correctamente"));
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar la asistencia: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];

        try
        {
            $resultado = Asistencia::borrarAsistencia($id);

            if($resultado)
            {
              $payload = json_encode(array("mensaje" => "Asistencia borrada con exito"));  
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se encontro asistencia con ese id"));  
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');   
        } catch(Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar borrar la asistencia: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }   
    }
}