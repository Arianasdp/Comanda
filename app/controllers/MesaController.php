<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];

        $mesa = new Mesa();
        $mesa->codigo = $codigo;

        try 
        {
            $id_nuevo_mesa = $mesa->crearMesa();
            $payload = json_encode(array("mensaje" => "Mesa creada con exito", "id_mesa" => $id_nuevo_mesa));
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

        if(isset($args['id']))
        {
            $id = $args['id'];
        
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
        else
        {
            try 
            {
                $mesa = Mesa::mesaLibre();
                
                if ($mesa) 
                {
                    $payload = json_encode($mesa);
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                } else 
                {
                    $payload = json_encode(array("mensaje" => "Mesa libre no encontrada"));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->write($payload);
                }
            } catch (Exception $e) 
            {
                $payload = json_encode(array("mensaje" => "Error al obtener la mesa: " . $e->getMessage()));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
            }

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
        if(isset($args['id']))
        {
            $id = $args['id'];

            $estadoActual = Mesa::traerEstado($id);

            if($estadoActual == "cerrada")
            {
                try 
                {
                    Mesa::modificarEstado($id, "con clientes esperando pedido");
                    $payload = json_encode(array("mensaje" => "Mesa modificada correctamente"));
                    
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                } catch (Exception $e) 
                {
                    $payload = json_encode(array("mensaje" => "Error al modificar la mesa: " . $e->getMessage()));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
                } 
            }
            else
            {
                if($estadoActual == "con clientes comiendo")
                {
                    $ganancias = $request->getAttribute('ganancias');

                    try
                    {
                        Mesa::modificarEstado($id, "con clientes pagando");
                        Mesa::modificarGanancias($id, $ganancias);
                        
                        $payload = json_encode(array("mensaje" => "Mesa pagada correctamente"));
                            
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                    } catch (Exception $e) 
                    {
                        $payload = json_encode(array("mensaje" => "Error al querer cobrar la mesa: " . $e->getMessage()));
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                    }  
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "No se puede cobrar la mesa"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Error con la ruta"));
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

    public static function CerrarMesa($request, $response, $args)
    {
        $id = $args['id'];

        try 
        {
            Mesa::modificarEstado($id, "cerrada");
            $payload = json_encode(array("mensaje" => "Mesa cerrada correctamente"));
                    
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al cerrar la mesa: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        } 
    }

    public static function TraerEstadoDeMesas($request, $response)
    {
        $lista = Mesa::obtenerEstados();
        $payload = json_encode(array("lista estado de mesas" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}