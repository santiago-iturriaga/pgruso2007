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
	include_once("Interfaz.php");

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
	$msj = null;
	$msjerror = null;


	$menu		=	$plantilla->load("plantillas/menu.html");
	$menuvert		=	$plantilla->load("plantillas/menu_vertical.html");

	$resConsulta = null;


	$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$i = new Interfaz($conexion,$plantilla,$s);
	$alertas = new Alertas($conexion);

	if(isset($_POST["form_eliminar"])){
	 	$resConsulta = $alertas->deleteAlerta($s->sesion->ClienteActual,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
	 	if($resConsulta["error"]){
	 		error_log(print_r($resConsulta,1));
	 		$msjerror = "Error al eliminar la alertas";
	 	}else{
	 		$msj = "La alerta fue eliminada";
	 	}
	 	$ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$i->getMenuVertical(),"TABALERTA"=>""));
	}else{
		$resConsulta = $alertas->getAlerta($s->sesion->ClienteActual,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
		if($resConsulta["error"]){
		 error_log(print_r($resConsulta,1));
		 $ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$i->getMenuVertical(),"TABALERTA"=>""));
		 $msjerror = "Error al obtener la alertas";
		}else{

			foreach ($resConsulta["alerta"] as $id=>$rowAlerta){
				$tabalerta = $plantilla->replace($tabalerta,array("FECHA"=>$rowAlerta["fecha"],"ASUNTO"=>$rowAlerta["asunto"],"BODY"=>$rowAlerta["body"]));
			}
			//DESCOMENTAR PARA MARCAR LAS ALERTAS COMO LEIDAS
			$res = $alertas->marcarAlertaLeida($s->sesion->ClienteActual,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
			$ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$i->getMenuVertical(),"TABALERTA"=>$tabalerta));
			$msj = "La alerta fue marcada como leida";
		}
	}


	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,"MENU"=>$menu,"MENSAJE"=>$msj,"ERROR"=>$msjerror));
	$s->salvar();
	echo $base;


?>