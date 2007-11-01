<?php
include_once("Sesion.php");
include_once("Directorio.php");
include_once("Constantes.php");
include_once("Conexion.php");
include_once("Usuario.php");

class MiSesion extends Sesion {
	
	var $codigoError	=	"";
	var $mensajeError	=	"";
	
	var $logueado	= false;
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
			echo "OPA";
			$this->Usuario=new Usuario(); 
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
