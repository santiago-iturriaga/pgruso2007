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

	if(isset($_GET["alerta"])){

		$s->sesion->alerta_actual = $_GET["alerta"];
		$s->sesion->bytes_leidos = 0;
		$s->salvar();
		header("Location: alerta.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/alertas/alertas.html");
	$link		= 	$plantilla->load("plantillas/alertas/link.html");
	$checkbox = 	$plantilla->load("plantillas/alertas/checkbox.html");
	$menu		=	$plantilla->load("plantillas/menu.html");
	$menuvert		=	$plantilla->load("plantillas/menu_vertical.html");

	//obtengo las alertas
	$conexion= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$alertas = new Alertas($conexion);
	$resConsulta = $alertas->getAlertasUsuarioTrabajo($s->sesion->ClienteActual,$s->sesion->TrabajoActual);

	$tabAlertas= "";
	if($resConsulta["error"]) error_log(print_r($resConsulta,1));
	else{
		$tabla = new Tabla();
		$tabla->addColumna(1,"fecha","Fecha");
		$tabla->addColumna(2,"asunto","Asunto");
		$tabla->addColumna(3,"link","");
		$tabla->addColumna(4,"eliminar","");

		foreach ($resConsulta["alertas"] as $id=>$rowAlertas){
			$rowAlertas["link"]=$plantilla->replace($link,array("ID"=>$id));
			$rowAlertas["eliminar"]=$checkbox;
			$rowAlertas{"fecha"} = "<b>" . $rowAlertas{"fecha"} . "</>";
			$rowAlertas{"asunto"} = "<b>" . $rowAlertas{"asunto"} . "</>";
			$tabla->addRenglon($rowAlertas);

		}

		$tabla->clase = "";
		$tabAlertas = $tabla->getTabla();
	}


	$ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$menuvert,"ALERTAS"=>$tabAlertas));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,"MENU"=>$menu));
	$s->salvar();
	echo $base;
?>