<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
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

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/alertas/veralerta.html");
	$tabalerta		= $plantilla->load("plantillas/alertas/tabalerta.html");
	$msj = null;
	$msjerror = null;

	$interfaz = new Interfaz($conexion,$plantilla,$s);
	$menu		=null;//	$plantilla->replace($plantilla->load("plantillas/menu.html"),
						//				array("CLASE_ALERTAS"=>'id="actual"'));
	//$menuvert		=	$plantilla->load("plantillas/menu_vertical.html");

	$resConsulta = null;


	$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$i = new Interfaz($conexion,$plantilla,$s);
	$alertas = new Alertas($conexion);

	if(isset($_POST["form_eliminar"])){
	 	$resConsulta = $alertas->deleteAlerta($s->sesion->Usuario->id,$s->sesion->trabajo,$s->sesion->alerta_actual);
	 	if($resConsulta["error"]){
	 		$msjerror = $interfaz->getError($resConsulta);
	 	}else{
	 		$msj = $interfaz->getMensaje("A001");
	 	}
	 	$ppal = $plantilla->replace($ppal,array("TABALERTA"=>""));
	}else{
		$resConsulta = $alertas->getAlerta($s->sesion->Usuario->id,$s->sesion->trabajo,$s->sesion->alerta_actual);
		if($resConsulta["error"]){
		 $msjerror = $interfaz->getError($resConsulta);
		 $ppal = $plantilla->replace($ppal,array("TABALERTA"=>""));

		}else{
			$trabajo = 0;
			$idAlert = 0;
			foreach ($resConsulta["alerta"] as $id=>$rowAlerta){
				$tabalerta = $plantilla->replace($tabalerta,array("CLIENTE"=>$rowAlerta["cliente"],"TRABAJO"=>$rowAlerta["trabajo"],"FECHA"=>$rowAlerta["fecha"],"ASUNTO"=>$rowAlerta["asunto"],"BODY"=>$rowAlerta["body"]));
				$trabajo = $rowAlerta["trabajo"];
			}
			// MARCAR LAS ALERTAS COMO LEIDAS
			$res = $alertas->marcarAlertaLeida($s->sesion->Usuario->id,$trabajo,$s->sesion->alerta_actual);
			if($res["error"]){
	 			$msjerror = $interfaz->getError($res);
	 		}else{
	 			$msj = $interfaz->getMensaje("A002");
	 		}

			//Enviar por mail SACAR
			//echo "antes enviar - ";
			//$res = $alertas->enviarAlerta($s->sesion->ClienteActual,$s->sesion->trabajo,$s->sesion->alerta_actual);
			//echo "des enviar";
			//asignar alerta
			//$res = $alertas->asignarAlerta($s->sesion->ClienteActual,$s->sesion->TrabajoActual,3, array("TRABAJO" => "BOLUDO"));

			$ppal = $plantilla->replace($ppal,array("TABALERTA"=>$tabalerta));

		}
	}
	//$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,"MENU"=>$menu,"MENSAJE"=>$msj,"ERROR"=>$msjerror));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$msj,
												"ERROR"=>$msjerror,
												"MENU_USUARIOS"=>" class='menu_tab' ",
												"MENU_GANGLIA"=>" class='menu_tab'",
												"MENU_MAUI"=>" class='menu_tab'",
												"MENU_TORQUE"=>" class='menu_tab'",
												"MENU_ALERTAS"=>" id='actual'",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"MENU"=>""));

	$s->salvar();
	echo $base;


?>