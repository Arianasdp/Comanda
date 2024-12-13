<?php

class Asistencia
{
    public $id;
    public $id_usuario;
    public $fecha;
    public $cancelada;

    public function crearAsistencia()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO asistencias (id_usuario, fecha) VALUES (:id_usuario, NOW())");
        $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_usuario, fecha, cancelada FROM asistencias");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Asistencia');
    }

    public static function obtenerAsistencia($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_usuario, fecha, cancelada FROM asistencias WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Asistencia');
    }

    public static function modificarAsistencia($id, $id_usuario, $fecha)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE asistencias SET id_usuario = :id_usuario, fecha = :fecha WHERE id = :id");
        $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarAsistencia($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE asistencias SET cancelada = 1 WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount() > 0;
    }

    public function existeAsistenciaHoy($idUsuario)
    {
        $hoy = date('Y-m-d');
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("SELECT * FROM asistencias WHERE id_usuario = :id_usuario AND DATE(fecha) = :hoy");
        $consulta->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':hoy', $hoy, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

}