<?php
use Firebase\JWT\JWT;
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function login($request, $response, $args)
  {
    $parsedBody = $request->getParsedBody();
    $usuario = $parsedBody['usuario'] ?? '';
    $clave = $parsedBody['clave'] ?? '';

    $usuarioEncontrado = Usuario::obtenerUsuario($usuario);

    if($usuarioEncontrado != null)
    {
      if(password_verify($clave, $usuarioEncontrado->clave))
      {
        $key = "supermegahipersegura_clave_secreta";
        $payload = [
            "iat" => time(),            
            "exp" => time() + 86400,     
            "data" => [
                "id" => $usuarioEncontrado->id,
                "usuario" => $usuario,
                "rol" => $usuarioEncontrado->rol
            ]
        ];

        $jwt = JWT::encode($payload, $key, 'HS256'); 
        $payload = json_encode(array("mensaje" => "Login exitoso, usuario: {$usuario}", "token" => $jwt));

        Usuario::modificarEstado($usuarioEncontrado->id, "activo");
      }
      else
      {
        $payload = json_encode(array("mensaje" => "Clave incorrecta"));
      }
    }
    else
    {
      $payload = json_encode(array("mensaje" => "Usuario invalido"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $rol = $parametros['rol'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    $usr->clave = $clave;
    $usr->rol = $rol;
    $ultimoId = $usr->crearUsuario();

    if(is_int($ultimoId))
    {
      $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    else
    {
      $payload = json_encode(array("mensaje" => "El usuario no pudo ser creado"));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
    
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
    $id = $args['id'];

    $parametros = $request->getParsedBody();

    $nuevoNombre = $parametros['usuario'];
    $nuevaClave = $parametros['clave'];
    Usuario::modificarUsuario($id, $nuevoNombre, $nuevaClave);

    $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {

    $usuarioId = $args['id'];

    try {
      $resultado = Usuario::borrarUsuario($usuarioId);

      if ($resultado) {
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "No se encontro un usuario con ese id"));
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
      $payload = json_encode(array("mensaje" => "Error al intentar borrar el usuario: " . $e->getMessage()));
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }

  }
}
