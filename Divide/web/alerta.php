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
	$tabalerta		= $plantilla->load("plantillas/alertas/tabalerta.html");
	$msj = null;
	$msjerror = null;
	$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);


	$interfaz = new Interfaz($conexion,$plantilla,$s);
	$menu		=	$plantilla->replace($plantilla->load("plantillas/menu.html"),
										array("CLASE_ALERTAS"=>'id="actual"'));
	$menuvert		=	$plantilla->load("plantillas/menu_vertical.html");

	$resConsulta = null;



	$i = new Interfaz($conexion,$plantilla,$s);
	$alertas = new Alertas($conexion);

	if(isset($_POST["form_eliminar"])){
	 	$resConsulta = $alertas->deleteAlerta($s->sesion->Usuario->id,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
	 	if($resConsulta["error"]){
	 		$msjerror = $interfaz->getError($resConsulta);
	 	}else{
	 		$msj = $interfaz->getMensaje("A001");
	 	}
	 	$ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$i->getMenuVertical(),"TABALERTA"=>""));
	}else{
		$resConsulta = $alertas->getAlerta($s->sesion->Usuario->id,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
		if($resConsulta["error"]){
		 $msjerror = $interfaz->getError($resConsulta);
		 $ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$i->getMenuVertical(),"TABALERTA"=>""));

		}else{

			foreach ($resConsulta["alerta"] as $id=>$rowAlerta){
				$tabalerta = $plantilla->replace($tabalerta,array("FECHA"=>$rowAlerta["fecha"],"ASUNTO"=>$rowAlerta["asunto"],"BODY"=>$rowAlerta["body"]));
			}
			// MARCAR LAS ALERTAS COMO LEIDAS
			$res = $alertas->marcarAlertaLeida($s->sesion->Usuario->id,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
			if($res["error"]){
	 			$msjerror = $interfaz->getError($res);
	 		}else{
	 			$msj = $interfaz->getMensaje("A002");
	 		}

			//Enviar por mail SACAR
			//echo "antes enviar - ";
			//$res = $alertas->enviarAlerta($s->sesion->Usuario->id,$s->sesion->TrabajoActual,$s->sesion->alerta_actual);
			//echo "des enviar";
			//asignar alerta
			//$res = $alertas->asignarAlerta($s->sesion->Usuario->id,$s->sesion->TrabajoActual,3, array("TRABAJO" => "BOLUDO"));

			$ppal = $plantilla->replace($ppal,array("TABALERTA"=>$tabalerta));

		}
	}
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,"MENU"=>$menu,"MENSAJE"=>$msj,"ERROR"=>$msjerror,"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login));
	$s->salvar();
	echo $base;


?>