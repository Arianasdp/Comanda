<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ValidarEncuestaMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $parametros = $request->getParsedBody();

        $id_pedido = $parametros['id_pedido'];
        $codigo_mesa = $parametros['codigo_mesa'];

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta =  $objAccesoDatos->prepararConsulta("SELECT p.id AS pedido_id, m.codigo AS mesa_codigo FROM pedidos p JOIN mesas m ON p.id_mesa = m.id WHERE p.id = :id_pedido AND m.codigo = :codigo_mesa");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado)
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["error" => "No se puede llenar una encuesta para un pedido atado a esa mesa, ya que no existe pedido alguno."]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        return $response;
    }

}

/**SELECT p.id AS pedido_id, m.codigo AS mesa_codigo
FROM pedidos p
JOIN mesas m ON p.id_mesa = m.id
WHERE p.id = 1 AND m.codigo = 'A3K8Z'; */