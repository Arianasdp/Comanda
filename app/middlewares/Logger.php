<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once './models/Usuario.php';

class LoggerMiddleware
{
    private $key = "supermegahipersegura_clave_secreta";
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $authHeader = $request->getHeader('Authorization');
        
        if (!$authHeader) {
            $response = new Response();
            $response->getBody()->write(json_encode(["mensaje" => "Token no proporcionado"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader[0]);
        

        try 
        {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            $request = $request->withAttribute('token', $decoded);
            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException $e) {

            $response = new Response();
            $response->getBody()->write(json_encode(["mensaje" => "Token expirado"]));

            $tokenParts = explode('.', $token);
            if (count($tokenParts) === 3) 
            {
                $payload = json_decode(base64_decode($tokenParts[1]));

                if (isset($payload->data->id)) {
                    Usuario::modificarEstado($payload->data->id, "inactivo");
                }
            }
        
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(["mensaje" => "Token con firma invalida"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(["mensaje" => "Token invalido"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}