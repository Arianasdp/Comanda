<?php

require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        try 
        {
            if(isset($parametros['id_mesa']) 
            && !empty($parametros['id_mesa']))
            {
                $id_mesa = $parametros['id_mesa'];

                $pedido = new Pedido();
                $pedido->id_mesa = $id_mesa;
                
                if(isset($uploadedFiles['foto']))
                {
                    $foto = $uploadedFiles['foto'];
                    if($foto->getError() === UPLOAD_ERR_OK)
                    {
                        date_default_timezone_set('America/Argentina/Buenos_Aires');
                        $fechaHoraActual = str_replace([':', ' '], ['-', '_'], date('Y-m-d H:i:s'));
                        $imagenExtension = pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION);
                        $imagenPath = "ImagenesDeMesas/{$id_mesa}.{$fechaHoraActual}.{$imagenExtension}";
                        $foto->moveTo($imagenPath); 
                        $pedido->foto = $imagenPath;
                    }
                    else
                    {
                        error_log("No se pudo cargar la imagen");
                    }
                }
                else
                {
                    $pedido->foto = "-";
                }
                
                $id_nuevo_pedido = $pedido->crearPedido();
                $payload = json_encode(array("mensaje" => "Pedido creado con exito", "id_pedido" => $id_nuevo_pedido));
                $response = $response->withStatus(201);   
            }
            else
            {
                $payload = json_encode(array("mensaje" => "Faltan datos requeridos para crear el pedido."));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
            
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
        $id = $args['id'];

        try 
        {
            $estadoActual = Pedido::traerEstado($id);
            if ($estadoActual == "pendiente") 
            {    
                Pedido::modificarPedido($id, "en preparacion");
                $payload = json_encode(array("mensaje" => "Estado del pedido {$id} actualizado"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else 
            {
                if($estadoActual == "en preparacion")
                {
                    Pedido::modificarPedido($id, "listo para servir");
                    $payload = json_encode(array("mensaje" => "Pedido {$id} marcado como entregado al mozo"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "El pedido {$id} ya esta marcado como entregado al mozo o ya ha sido servido a la mesa"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }

        } catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al modificar el pedido: " . $e->getMessage()));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500)->write($payload);
        }
    }

    public static function TraerListos ($request, $response, $args)
    {
        $lista = Pedido::obtenerListosParaServir();

        if($lista != null)
        {
           $payload = json_encode(array("lista Pedidos listos para servir" => $lista)); 
           $response->getBody()->write($payload);
           return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
        else
        {
            $payload = json_encode(array("No hay pedidos listos para servir"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); 
        }
    }

    public function Servido($request, $response, $args)
    {
        $id = $args['id'];
        
        try
        {
            $estadoActual = Pedido::traerEstado($id);
            if($estadoActual == "listo para servir")
            {
                Pedido::marcarComoEntregado($id);
                $resultado = Pedido::obtenerPedido($id); 
                $mensaje = "{$resultado->id_mesa}, Pedido: {$resultado->id}, Estado: {$resultado->estado}";
                $payload = json_encode(array("mensaje" => "Pedido marcado como servido a la mesa: " . $mensaje));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            }
            else
            {
                if($estadoActual == "servido")
                {
                    $payload = json_encode(array("mensaje" => "El pedido ya fue servido"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "El pedido no puede ser servido en este momento"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);  
                }
                
            }
            
        }catch (Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al tratar de entregar el pedido: " . $e->getMessage()));
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

    public static function TraerPedidosEntregadosTarde($request, $response)
    {
        $lista = Pedido::obtenerFueraDeTiempo();
        $payload = json_encode(array("lista Pedidos entregados fuera de tiempo" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerEstadoDePedidos($request, $response)
    {
        $lista = Pedido::obtenerEstados();
        $payload = json_encode(array("lista estado de pedidos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerTiempoRestante($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        $id = $args['id'];
        
        try
        {
            if(isset($parametros["codigo_mesa"]))
            {
                $codigo = $parametros["codigo_mesa"];

                $entregado = Pedido::chequearFechaEntrega($id);

                if($entregado == null)
                {
                    $tiempo = Pedido::obtenerTiempoRestante($id, $codigo);

                    if($tiempo != null)
                    {
                        $payload = json_encode("Tiempo restante estimado: " . $tiempo);
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                    }
                    else
                    {
                        $payload = json_encode(array("mensaje" => "Pedido no encontrado"));
                        $response->getBody()->write($payload);
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                    }  
                }
                else
                {
                    $payload = json_encode(array("mensaje" => "Ese pedido ya fue entregado"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
                
            }

        } catch(Exception $e) 
        {
            $payload = json_encode(array("mensaje" => "Error al intentar obtener detalles del pedido: " . $e->getMessage()));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }  
    }

    public function TraerMesaMasUsada($request, $response)
    {
        $mesa = Pedido::obtenerMesaMasUsada();
        $payload = json_encode(array("Mesa mas usada " => $mesa));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerEstadisticas($request, $response)
    {
        $estadisticas = Pedido::obtenerEstadistica();
        $payload = json_encode(array("Estadísticas ultimos 30 dias" => $estadisticas));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
