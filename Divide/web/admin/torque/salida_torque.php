<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Momento.php");
	include_once("Tabla/Tabla.php");

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/salida_torque/salida_torque.html");
	$opcion		= 	$plantilla->load("plantillas/salida_torque/opcion.html");
	$link		= 	$plantilla->load("plantillas/salida_torque/link.html");
	$mensaje = "";
	$error = "";

	$trabajos="";
	//echo '<pre>';print_r($s->sesion);echo '</pre>';exit;

	foreach ($s->sesion->Usuario->trabajos as $tid=>$trabajo){
		$selected = "";
		if($tid == $s->sesion->form_trabajo) $selected = "SELECTED";
		$trabajos.=$plantilla->replace($opcion,array("ID"=>$tid,
											   "SELECTED" => $selected,
											   "NOMBRE"=>$trabajo["trabajo"]." - ".$trabajo["cliente"]));
	}

	$tabla  = "";
	if(isset($_POST["trabajo"])){
		$s->sesion->form_desde = $_POST["desde"];
		$s->sesion->form_hasta = $_POST["hasta"];
		$s->sesion->form_trabajo = $_POST["trabajo"];
		$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
		$momento = new Momento($conexion);
		$res = $momento->getEjecucionesAdmin($_POST["trabajo"], $_POST["desde"], $_POST["hasta"]);
		if($res["error"]){
		}else{
			$tabla = new Tabla("","tabla","../../");
			$tabla->addColumna(1,"archivo","Archivo");
			$tabla->addColumna(2,"fecha_ini","Inicio");
			$tabla->addColumna(3,"fecha_ejecucion","Ejecuci&oacute;n");
			$tabla->addColumna(4,"fecha_fin","Fin");
			$tabla->addColumna(5,"tiempo_ejecucion","Tiempo");
			$tabla->addColumna(6,"link","");
			foreach ($res["salida"] as $id=>$ejecucion){
				$ejecucion["fecha_ini"]=array_shift(explode('.',$ejecucion["fecha_ini"],2));
				$ejecucion["fecha_fin"]=array_shift(explode('.',$ejecucion["fecha_fin"],2));
				$ejecucion["fecha_ejecucion"]=array_shift(explode('.',$ejecucion["fecha_ejecucion"],2));
				$ejecucion["link"]=$plantilla->replace($link,array("ID"=>$id));
				if($ejecucion["fecha_ejecucion"]=="") $ejecucion["fecha_ejecucion"] = "<b> - En espera - </b>";
				elseif($ejecucion["fecha_fin"]=="") $ejecucion["fecha_fin"] = "<b> - En ejecuci&oacute;n - </b>";
				$tabla->addRenglon($ejecucion);
			}
			$tabla = $tabla->getTabla();
		}
	}


	if ($mensaje != "") {
		$mensajepl = $plantilla->load("plantillas/mensaje.html");
		$mensajepl = $plantilla->replace($mensajepl, array (
			"MENSAJE" => $mensaje
		));
	} else {
		$mensajepl = "";
	}

	if ($error != "") {
		$errorpl = $plantilla->load("plantillas/error.html");
		$errorpl = $plantilla->replace($errorpl, array (
			"ERROR" => $error
		));
	} else {
		$errorpl = "";
	}


	$ppal	=	$plantilla->replace($ppal,array("TRABAJOS"=>$trabajos,
												"TABLA"=>$tabla,
												"DESDE"=>$s->sesion->form_desde,
												"HASTA"=>$s->sesion->form_hasta));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensajepl,
												"SMENU_SALIDA"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$errorpl));
	$s->salvar();

	echo $base;?>
