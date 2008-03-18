<?php
/*
 * Created on Feb 5, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	include_once("lib.inc.php");
	include_once("Servidor.php");

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}

	$plantilla	= new TPL();
	$base		= $plantilla->load("plantillas/base.html");
	$ppal		= $plantilla->load("plantillas/index.html");
	$mensaje 	= "";
	$error 		= "";

	$procesos = estado_procesos();
	$procesos_maui = $procesos["scheduler"];

	$mensaje_proceso = "";
	if (count($procesos_maui)>0) {
		$mensaje_proceso = "Servicio Maui en ejecuci&oacute;n<br />";
		$ppal= $plantilla->uncomment($ppal,array("UP"));
	} else {
		$mensaje_proceso = "El servicio no se encuentra en ejecuci&oacute;n.";
		$ppal= $plantilla->uncomment($ppal,array("DOWN"));
	}

	$pid = "";
	$cmd = "";
	$uname = "";

	while (count($procesos_maui)>0) {
		$proceso_maui_parseado = parsear_proceso(array_shift($procesos_maui));
		$pid = $proceso_maui_parseado["pid"];
		$uname = $proceso_maui_parseado["uname"];
		$cmd = $proceso_maui_parseado["cmd"];
	}

	$ppal	=	$plantilla->replace($ppal,array("ESTADO_PROCESO"=>$mensaje_proceso,
												"PID"=>$pid,
												"UNAME"=>$uname,
												"CMD"=>$cmd));

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>