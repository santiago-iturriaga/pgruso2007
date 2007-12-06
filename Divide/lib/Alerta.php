<?php

class Alerta{
	var $id;
	var $idTrabajo;
	var $idUsuario;
	var $asunto;
	var $body;
	var $leida;

	function Alerta($id,$idTrabajo,$idUsuario, $asunto,$body,$leida  ){
		this->id = $id;
		this->$idTrabajo = $idTrabajo;
		this->$idUsuario = $idUsuario;
		this->$asunto = $asunto;
		this->$body = $body;
		this->$leida = $leida;
	}
}

?>