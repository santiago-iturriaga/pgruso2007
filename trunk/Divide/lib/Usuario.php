<?php
include_once("Conexion.php");
include_once("Usuarios.php");
class Usuario{
	var $conexion	=	null;
	var $login		=	null;
	var $trabajos	=	null;
	var $id			= 	null;
	var $administrador = false;
	function Usuario(){
	}

	function setBD($bd){
		$this->conexion	=	$bd;
	}
	function Logueado(){
		return $this->login != null;
	}

	function Login($login,$clave){
		$Usuarios = new Usuarios($this->conexion);
		$res = $Usuarios->validarUsuario($login,$clave);
		if($res["error"]) return $res;
		$this->login	=	$login;
		$this->id		= $res["id"];
		$this->administrador	= ($res["administrador"] == 'S');
		$this->login	=	$login;
		$this->setTrabajos();
		return array("error"=>0);
	}

	function setTrabajos(){
		$Usuarios = new Usuarios($this->conexion);
		$res = $Usuarios->getTrabajosUsuario($this->id);
		if($res["error"]) return $res;
		$this->trabajos	=	$res["trabajos"];
		return array("error"=>0);
	}

}
?>