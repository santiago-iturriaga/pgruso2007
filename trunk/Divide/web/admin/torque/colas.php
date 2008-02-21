<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	include_once("lib.inc.php");
	include_once("Torque.php");

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/colas/colas.html");
	$mensaje = "";
	$error = "";

	if (ISSET($_REQUEST["detener"])) {
		$id = $_REQUEST["detener"];
		list($mensaje,$error) =  torque_detener_cola($id);
	}
	if (ISSET($_REQUEST["iniciar"])) {
		$id = $_REQUEST["iniciar"];
		list($mensaje,$error) =  torque_iniciar_cola($id);
	}

	$command = SSH." -l ".USERNAME." ".HOST." \"".QSTAT_CMD." -Qf; exit\" 2>&1";
	$qstatQ = `$command`;

	$pagina = getTablasColas($qstatQ);

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

	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensajepl,
												"SMENU_COLAS"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$errorpl));
	$s->salvar();

	echo $base;?>
