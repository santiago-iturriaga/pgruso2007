<?php 
include_once("Sesion.php");
include_once("Directorio.php");
include_once("Constantes.php");
include_once("Conexion.php");
include_once("Usuario.php");

class Sesion {
	var $codigoError	=	"";
	var $mensajeError	=	"";
	
	var $logueado	= false;
	
	var $Directorio	= null;
	var $Conexion;
	var $Usuario;
	var $TrabajoActual	=	null;
	
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

class ManejadorSesion{
	var $sesion = null;
	function getSesion($nueva=false){
		if (session_id()) {

			return $this->sesion;	
		}
		if (!$nueva) {
			$this->sesion = null;
			session_start();
			if (!isset($_SESSION['PHPSesion'])) {
				return null;
			}
			$this->sesion = unserialize($_SESSION['PHPSesion']);
			if (!is_object($this)) {
				$this->cerrar();
				return null;
			}
		}
		else {
			list($usec, $sec) = explode(' ', microtime());
	    		mt_srand( (float) $sec + ((float) $usec * 100000) );
			if (function_exists("posix_getpid")){
				#Linux
				session_id( md5( uniqid(mt_rand().posix_getpid(),true) ) );
			}
			else {
				#Windows
				session_id( md5( uniqid(mt_rand(),true) ) );
			}
			session_start();
			session_unset();
			$this->salvar();


		}
	}		

	function salvar() {
		$_SESSION['PHPSesion'] = serialize($this->sesion);
		return true;
	}

	function cerrar(){
		if (isset($_COOKIE[session_name()])) {
		   setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		$this->sesion = null;
	}
	
}

?>
