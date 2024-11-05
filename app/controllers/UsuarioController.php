<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $sector = $parametros['sector'];

      // Creamos el usuario
      $usr = new Usuario();
      $usr->usuario = $usuario;
      $usr->clave = $clave;
      $usr->sector = $sector;
      $usr->crearUsuario();

      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos usuario por nombre
      $usr = $args['usuario'];
      $usuario = Usuario::obtenerUsuario($usr);
      $payload = json_encode($usuario);

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Usuario::obtenerTodos();
      $payload = json_encode(array("listaUsuario" => $lista));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $id = $parametros['id'];
      $nuevoNombre = $parametros['nombre'];
      $nuevaClave = $parametros['clave'];
      Usuario::modificarUsuario($id, $nuevoNombre, $nuevaClave);

      $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {

      $usuarioId = $args['id'];

      try
      {
        $resultado = Usuario::borrarUsuario($usuarioId);

        if($resultado)
        {
          $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "No se encontro un usuario con ese id"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
      } catch(Exception $e) 
      {
        $payload = json_encode(array("mensaje" => "Error al intentar borrar el usuario: " . $e->getMessage()));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
      } 
          
    }
}
