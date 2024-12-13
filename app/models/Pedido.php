<?php

class Pedido
{
    public $id;
    public $id_mesa;
    public $estado;
    public $foto;
    public $fecha_inicio;
    public $tiempo_estimado;
    public $fecha_entrega;
    public $precio;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id_mesa, fecha_inicio, estado, foto) VALUES (:id_mesa, NOW(), 'pendiente', :foto)");
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public function marcarComoEntregado($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = 'servido', fecha_entrega = NOW() WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_mesa, estado, foto, fecha_inicio, tiempo_estimado, fecha_entrega, precio FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_mesa, estado, foto, fecha_inicio, tiempo_estimado, fecha_entrega, precio FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido($id, $nuevoEstado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    public static function modificarPedido2($id, $nuevoEstado, $tiempo_estimado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, tiempo_estimado = :tiempo_estimado WHERE id = :id");
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo_estimado', $tiempo_estimado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = 'cancelado' WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount() > 0;
    }

    public static function traerPrecio($id, $id_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT precio FROM pedidos WHERE id = :id AND id_mesa = :id_mesa");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $id_mesa, PDO::PARAM_INT);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado['precio'] ?? null;
    }

    public static function obtenerListosParaServir()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado = 'listo para servir'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerFueraDeTiempo()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT *, TIMESTAMPDIFF(MINUTE, fecha_inicio, fecha_entrega) AS tiempo_real, IF(fecha_entrega <= ADDTIME(fecha_inicio, SEC_TO_TIME(tiempo_estimado * 60)), 'A tiempo', 'Fuera de tiempo') AS estado_entrega FROM pedidos WHERE fecha_entrega > ADDTIME(fecha_inicio, SEC_TO_TIME(tiempo_estimado * 60))");
        $consulta->execute();
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $resultado ?? null;
    }

    public static function obtenerEstados()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_mesa, estado FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTiempoRestante($id_pedido, $codigo_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id AS id_pedido, mesas.codigo AS codigo_mesa, TIMESTAMPDIFF(MINUTE, NOW(), ADDTIME(pedidos.fecha_inicio, SEC_TO_TIME(pedidos.tiempo_estimado * 60))) AS tiempo_restante FROM pedidos INNER JOIN mesas ON pedidos.id_mesa = mesas.id WHERE mesas.codigo = :codigo_mesa AND pedidos.id = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return $resultado['tiempo_restante'] ?? null; 
    } 

    public static function chequearFechaEntrega($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT fecha_entrega FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id_pedido, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        return $resultado['fecha_entrega'] ?? null; 
    }

    public static function traerEstado($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT estado FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['estado'] ?? null;
    }

    public static function traerIdMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['id_mesa'] ?? null;
    }

    public static function obtenerMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, COUNT(*) AS cantidad_usos FROM pedidos GROUP BY id_mesa ORDER BY cantidad_usos DESC LIMIT 1");
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerEstadistica()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) AS total_pedidos FROM pedidos WHERE fecha_inicio >= CURDATE() - INTERVAL 30 DAY");
        $consulta->execute();

        $totales = $consulta->fetch(PDO::FETCH_ASSOC);

        $query = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) AS pedidos_entregados_a_tiempo FROM pedidos WHERE fecha_entrega <= ADDTIME(fecha_inicio, SEC_TO_TIME(tiempo_estimado * 60)) AND fecha_inicio >= CURDATE() - INTERVAL 30 DAY");
        $query->execute();

        $a_tiempo = $query->fetch(PDO::FETCH_ASSOC);

        $estadisticas = [$totales, $a_tiempo];

        return $estadisticas;
    }
}