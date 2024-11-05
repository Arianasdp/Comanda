<?php
class Pedido
{
    public $id;
    public $mesa;
    public $restaurante;
    public $mozo;
    public $cocinero;
    public $experiencia;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (mesa, restaurante, mozo, cocinero, experiencia) VALUES (:mesa, :restaurante, :mozo, :cocinero, :experiencia)");
        $consulta->bindValue(':mesa', $this->mesa, PDO::PARAM_INT);
        $consulta->bindValue(':restaurante', $this->restaurante, PDO::PARAM_STR);
        $consulta->bindValue(':mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':cocinero', $this->cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':experiencia', $this->experiencia, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesa, restaurante, mozo, cocinero, experiencia FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function obtenerEncuesta($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, mesa, restaurante, mozo, cocinero, experiencia FROM encuestas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }

    public static function modificarEncuesta($id, $mesa, $restaurante, $mozo, $cocinero, $experiencia)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encuestas SET mesa = :mesa, restaurante = :restaurante, mozo = :mozo, cocinero = :cocinero, experiencia = :experiencia WHERE id = :id");
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

}