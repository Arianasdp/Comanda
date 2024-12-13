<?php
class Encuesta
{
    public $id;
    public $id_pedido;
    public $codigo_mesa;
    public $mesa;
    public $restaurante;
    public $mozo;
    public $cocinero;
    public $experiencia;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (id_pedido, codigo_mesa, mesa, restaurante, mozo, cocinero, experiencia) VALUES (:id_pedido, :codigo_mesa, :mesa, :restaurante, :mozo, :cocinero, :experiencia)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':codigo_mesa', $this->codigo_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consulta->bindValue(':restaurante', $this->restaurante, PDO::PARAM_STR);
        $consulta->bindValue(':mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':cocinero', $this->cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':experiencia', $this->experiencia, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_pedido, codigo_mesa, mesa, restaurante, mozo, cocinero, experiencia FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function obtenerEncuesta($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_pedido, codigo_mesa, mesa, restaurante, mozo, cocinero, experiencia FROM encuestas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }

    public static function modificarEncuesta($id, $codigo_mesa, $mesa, $restaurante, $mozo, $cocinero, $experiencia)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encuestas SET codigo_mesa = :codigo_mesa, mesa = :mesa, restaurante = :restaurante, mozo = :mozo, cocinero = :cocinero, experiencia = :experiencia WHERE id = :id");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':mesa', $mesa, PDO::PARAM_STR);
        $consulta->bindValue(':restaurante', $restaurante, PDO::PARAM_STR);
        $consulta->bindValue(':mozo', $mozo, PDO::PARAM_STR);
        $consulta->bindValue(':cocinero', $cocinero, PDO::PARAM_STR);
        $consulta->bindValue(':experiencia', $experiencia, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarEncuesta($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encuestas SET experiencia = 'cancelado' WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount() > 0;
    }

    public static function obtenerMejoresComentarios()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT experiencia, (mesa + restaurante + mozo + cocinero) AS puntuacion_total FROM encuestas WHERE experiencia IS NOT NULL AND experiencia != '' ORDER BY puntuacion_total DESC LIMIT 5");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

}