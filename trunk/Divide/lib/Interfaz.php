<?php
include_once("Conexion.php");
include_once("TPL.php");
include_once("Alertas.php");
include_once("Sesion.php");

class Interfaz{

	var $conexion	=	null;
	var $plantilla	=	null;
	var $sesion	=	null;

	function Interfaz($bd, $pl, $s){
			$this->conexion=$bd;
			$this->plantilla=$pl;
			$this->sesion=$s;
		}

	function getMenuVertical(){
		$menuvert		=	$this->plantilla->load("plantillas/menu_vertical.html");
		$alertas = new Alertas($this->conexion);
		$resCantAlertas = $alertas->cantidadAlertasNoLeida($this->sesion->sesion->ClienteActual,$this->sesion->sesion->TrabajoActual);
		$cantAlertas = "0";
		if($resCantAlertas["error"]) error_log(print_r($resCantAlertas,1));
		else{
			$tempArray = $resCantAlertas["alerta"];
			$cantAlertasArr = array_shift ($tempArray);
			$cantAlertas = $cantAlertasArr["cantAlertas"];
		}
		$menuvert = $this->plantilla->replace($menuvert,array("CANTALERTAS"=>$cantAlertas,"USUARIO"=>$this->sesion->sesion->Usuario->login,"CLIENTE"=>$this->sesion->sesion->Usuario->trabajos{$this->sesion->sesion->TrabajoActual}["cliente"],"TRABAJO"=>$this->sesion->sesion->Usuario->trabajos{$this->sesion->sesion->TrabajoActual}["trabajo"],"IDTRABAJO"=>$this->sesion->sesion->TrabajoActual));
		return $menuvert;
	}

	function getMensaje($mensaje){
		include("Mensajes.php");
		return $this->plantilla->replace($this->plantilla->load("plantillas/mensaje.html"),array("MENSAJE"=>$MENSAJES[$mensaje]));
	}
	function getError($error){
		error_log(print_r($error,1));
		return $this->plantilla->replace($this->plantilla->load("plantillas/mensaje.html"),array("MENSAJE"=>print_r($error,1)));
	}
}
?>