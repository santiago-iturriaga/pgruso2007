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
	
	var $Directorio	= null;
	var $Conexion;
	var $Usuario;
	var $TrabajoActual	=	null;
	function MiSesion($nueva = false) {
		$this->Sesion($nueva);
		if($this!=NULL)
			{
			$this->setCodigoError("");
			$this->setMensajeError("");
			}
		if($nueva){
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
	
	function getMenuVertical($html,$tpl){
		return $tpl->replace($html,array("USUARIO"=>$this->Usuario->login,
										 "CLIENTE"=>$this->Usuario->cliente["nombre"],
										 "TRABAJO"=>$this->Usuario->trabajos[$this->TrabajoActual]["nombre"],
										 "USO_DISCO"=>"0",
										 "TRABAJOS_EJECUCION"=>"0",
										 "ALERTAS"=>"0"));
	}
}
?>
