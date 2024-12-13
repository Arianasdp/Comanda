<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once './models/Asistencia.php';
class ManejarAsistenciasMiddleware
{
    private $key = "supermegahipersegura_clave_secreta";
    private $rolesValidos;
    public function __construct($rolesValidos)
    {
        $this->rolesValidos = is_array($rolesValidos) ? $rolesValidos : [$rolesValidos];
    }

    public function __invoke($request, $handler)
    {
        $response = $handler->handle($request);

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (isset($data['token'])) {
            $decoded = JWT::decode($data['token'], new Key($this->key, 'HS256'));
            if (!in_array($decoded->data->rol, $this->rolesValidos)) {
                error_log("La asistencia no fue cargada ya que el usuario ingresado no es uno de nuestros empleados");
            }
            else
            {
                $asistencia = new Asistencia();
                $asistencia->id_usuario = $decoded->data->id;

                if (!$asistencia->existeAsistenciaHoy($decoded->data->id)) 
                {
                    $asistencia->crearAsistencia();
                    error_log("La asistencia de nuestro empleado {$decoded->data->usuario} fue correctamente subida");
                } else 
                {
                    error_log("Ya se registro una asistencia para este usuario hoy");
                }
                
            }
        }
        else
        {
            error_log("La asistencia no se pudo cargar debido a token invalido");
        } 

        return $response;

    }
}