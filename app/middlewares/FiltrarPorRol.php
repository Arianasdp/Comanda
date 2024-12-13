<?php
use Slim\Routing\RouteContext;
require_once './models/Pedido_producto.php';
class FiltrarPorRolMiddleware
{
    public function __invoke($request, $handler)
    {
        $token = $request->getAttribute('token');
        $rol = $token->data->rol;
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        if($rol == "cocinero")
        {
            $query = "SELECT pp.id AS id_pedido_producto, pp.id_pedido, p.nombre AS producto, p.sector, pp.estado, pp.fecha_inicio
            FROM pedido_productos pp
            JOIN productos p ON pp.id_producto = p.id
            WHERE pp.id_empleado IS NULL AND pp.estado = 'pendiente'
            AND (p.sector = 'Cocina' OR p.sector = 'Candy Bar')";
        }
        else
        {
            if($rol == "cervecero")
            {
                $query = "SELECT pp.id AS id_pedido_producto, pp.id_pedido, p.nombre AS producto, p.sector, pp.estado, pp.fecha_inicio
                FROM pedido_productos pp
                JOIN productos p ON pp.id_producto = p.id
                WHERE pp.id_empleado IS NULL AND pp.estado = 'pendiente'
                AND p.sector = 'Barra de choperas'";
            }
            else
            {
                if($rol == "bartender")
                {
                    $query = "SELECT pp.id AS id_pedido_producto, pp.id_pedido, p.nombre AS producto, p.sector, pp.estado, pp.fecha_inicio
                    FROM pedido_productos pp
                    JOIN productos p ON pp.id_producto = p.id
                    WHERE pp.id_empleado IS NULL AND pp.estado = 'pendiente'
                    AND p.sector = 'Barra de tragos y vinos'";
                }
            }
        }
        
        $consulta = $objAccesoDatos->prepararConsulta($query);
        $consulta->execute();
        
        $result = $consulta->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["mensaje" => "No hay pedidos pendientes para el rol: $rol."]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } else {
            $request = $request->withAttribute('pedidos_filtrados', $result);
        }

        return $handler->handle($request);
    }

    public function AsignarPorRol($request, $handler)
    {
        $token = $request->getAttribute('token');

        if (!isset($token->data->id)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["mensaje" => "Acceso denegado: falta el id del empleado en el token"]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        $rol = $token->data->rol;
        $idEmpleado = $token->data->id;

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $routeArguments = $route->getArguments();
        $idProducto = $routeArguments['id'] ?? null;
    
        if (!$idProducto) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["mensaje" => "Debe especificar un ID de producto."]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto FROM pedido_productos WHERE id = :id");
        $consulta->bindValue(':id', $idProducto, PDO::PARAM_INT);
        $consulta->execute();
        $idDelProducto = $consulta->fetch(PDO::FETCH_ASSOC);

        $consultaProductos = $objAccesoDatos->prepararConsulta("SELECT sector FROM productos WHERE id = :id_producto");
        $consultaProductos->bindValue(':id_producto', $idDelProducto["id_producto"], PDO::PARAM_INT);
        $consultaProductos->execute();
        $sectorProducto = $consultaProductos->fetch(PDO::FETCH_ASSOC)['sector'] ?? null;
        
    
        if (!$sectorProducto) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(["mensaje" => "Producto no encontrado."]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    
        $rolesPorSector = [
            'Cocina' => 'cocinero',
            'Candy Bar' => 'cocinero',
            'Barra de choperas' => 'cervecero',
            'Barra de tragos y vinos' => 'bartender',
        ];
    
        if (!isset($rolesPorSector[$sectorProducto]) || $rolesPorSector[$sectorProducto] !== $rol) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                "mensaje" => "Acceso denegado: el rol '$rol' no tiene permisos para asignar productos en el sector '$sectorProducto'."
            ]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        $request = $request->withAttribute('id_empleado', $idEmpleado);

        return $handler->handle($request);
    }
}