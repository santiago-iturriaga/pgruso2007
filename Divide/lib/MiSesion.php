<?php
include_once("Sesion.php");
include_once("Directorio.php");
include_once("Constantes.php");
include_once("Conexion.php");
include_once("Usuario.php");

class MiSesion extends Sesion {
	
	var $codigoError	=	"";
	var $mensajeError	=	"";
	
	var $Directorio;
	var $Conexion;
	var $Usuario;
	
	function MiSesion($nueva = false) {
		$this->Sesion($nueva);
		if($this!=NULL)
			{
			$this->setCodigoError("");
			$this->setMensajeError("");
			}
		if($nueva){
			$this->Directorio	=	new Directorio(RAIZ);
			$this->Conexion		=	new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
			$this->Usuario		=	new Usuario($this->Conexion); 
		}
	}
	
	function getCodigoError(){
		return $this->codigoError;
	}
	function setCodigoError($var){
		$this->codigoError=$var;
	}
	
	function getMensajeError(){
		return $this->mensajeError;
	}
	function setMensajeError($var){
		$this->mensajeError=$var;
	}

}
?>
