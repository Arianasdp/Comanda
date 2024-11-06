<?php
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $mesa = $parametros['mesa'];
        $restaurante = $parametros['restaurante'];
        $mozo = $parametros['mozo'];
        $cocinero = $parametros['cocinero'];
        $experiencia = $parametros['experiencia'];

        $encuesta = new Encuesta();
        $encuesta->mesa = $mesa;
        $encuesta->restaurante = $restaurante;
        $encuesta->mozo = $mozo;
        $encuesta->cocinero = $cocinero;
        $encuesta->experiencia = $experiencia;

        try 
        {
            $id_nuevo_encuesta = $encuesta->crearEncuesta();
            $payload = json_encode(array("mensaje" => "Encuesta creada con éxito", "id_mesa" => $id_nuevo_encuesta));
            $response = $response->withStatus(201);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al crear la encuesta", "error" => $e->getMessage()));
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
            $encuesta = Encuesta::obtenerEncuesta($id);
            
            if ($encuesta) 
            {
                $payload = json_encode($encuesta);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                $payload = json_encode(array("mensaje" => "Encuesta no encontrada"));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
            }
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al obtener la encuesta: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();
        $payload = json_encode(array("listaEncuestas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $mesa = $parametros['mesa'];
        $restaurante = $parametros['restaurante'];
        $mozo = $parametros['mozo'];
        $cocinero = $parametros['cocinero'];
        $experiencia = $parametros['experiencia'];

        try 
        {
            Encuesta::modificarEncuesta($id, $mesa, $restaurante, $mozo, $cocinero, $experiencia);
            $payload = json_encode(array("mensaje" => "Encuesta modificada correctamente"));
            
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar la encuesta: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];

        try
        {
            $resultado = Encuesta::borrarEncuesta($id);

            if($resultado)
            {
              $payload = json_encode(array("mensaje" => "Encuesta borrada con exito"));  
            }
            else
            {
                $payload = json_encode(array("mensaje" => "No se encontro encuesta con ese id"));  
            }

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');   
        } catch(Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar borrar la encuesta: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }   
    }
}