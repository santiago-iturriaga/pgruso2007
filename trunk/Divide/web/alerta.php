<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Directorio.php");
	include_once("Alertas.php");
	include_once("Tabla/Tabla.php");
	$s = new Sesion();

if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit;
		}

	if($s->sesion->TrabajoActual==null){
		header("Location: trabajos.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/alertas/veralerta.html");
	$tabalerta		= 	$plantilla->load("plantillas/alertas/tabalerta.html");


	$menu		=	$plantilla->load("plantillas/menu.html");
	$menuvert		=	$plantilla->load("plantillas/menu_vertical.html");

	//obtengo la alerta
	$conexion= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$alertas = new Alertas($conexion);
	$resConsulta = $alertas->getAlerta($s->sesion->ClienteActual,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);


	if($resConsulta["error"]){
		 error_log(print_r($resConsulta,1));
		 $ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$menuvert,"TABALERTA"=>""));
	}
	else{

		foreach ($resConsulta["alerta"] as $id=>$rowAlerta){

			$tabalerta = $plantilla->replace($tabalerta,array("FECHA"=>$rowAlerta["fecha"],"ASUNTO"=>$rowAlerta["asunto"],"BODY"=>$rowAlerta["body"]));
		}
		$ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$menuvert,"TABALERTA"=>$tabalerta));
		//DESCOMENTAR PARA MARCAR LAS ALERTAS COMO LEIDAS
		//$res = $alertas->marcarAlertaLeida($s->sesion->ClienteActual,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
	}

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,"MENU"=>$menu));
	$s->salvar();
	echo $base;


?>