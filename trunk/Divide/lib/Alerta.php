<?php
include_once("Conexion.php");
class Alerta{
	var $id;
	var $idTrabajo;
	var $idUsuario;
	var $asunto;
	var $body;

	function Alerta($id,$idTrabajo,$idUsuario, $asunto,$body  ){
		this->id = $id;
		this->$idTrabajo = $idTrabajo;
		this->$idUsuario = $idUsuario;
		this->$asunto = $asunto;
		this->$body = $body;

	}
}

?>