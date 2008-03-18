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

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/index.html");
	$mensaje 	= 	"";
	$error 		= 	"";

	$procesos = estado_procesos();
	$procesos_server = $procesos["server"];

	$mensaje_server = "";
	if (count($procesos_server)>0) {
		$mensaje_server = "Servicio Torque servidor en ejecuci&oacute;n<br />";
		$ppal= $plantilla->uncomment($ppal,array("UP_SERVER"));
	} else {
		$mensaje_server = "El servicio Torque servidor no se encuentra en ejecuci&oacute;n.";
		$ppal= $plantilla->uncomment($ppal,array("DOWN_SERVER"));
	}

	$pid_server = "";
	$cmd_server = "";
	$uname_server = "";

	while (count($procesos_server)>0) {
		$proceso_server_parseado = parsear_proceso(array_shift($procesos_server));
		$pid_server = $proceso_server_parseado["pid"];
		$uname_server = $proceso_server_parseado["uname"];
		$cmd_server = $proceso_server_parseado["cmd"];
	}

	$ppal	=	$plantilla->replace($ppal,array("ESTADO_PROCESO_SERVER"=>$mensaje_server,
												"PID_SERVER"=>$pid_server,
												"UNAME_SERVER"=>$uname_server,
												"CMD_SERVER"=>$cmd_server));

	$procesos_node = $procesos["node"];

	$mensaje_node = "";
	if (count($procesos_node)>0) {
		$mensaje_node = "Servicio Torque nodo en ejecuci&oacute;n<br />";
		$ppal= $plantilla->uncomment($ppal,array("UP_NODE"));
	} else {
		$mensaje_node = "El servicio Torque nodo no se encuentra en ejecuci&oacute;n.";
		$ppal= $plantilla->uncomment($ppal,array("DOWN_NODE"));
	}

	$pid_node = "";
	$cmd_node = "";
	$uname_node = "";

	while (count($procesos_node)>0) {
		$proceso_node_parseado = parsear_proceso(array_shift($procesos_node));
		$pid_node = $proceso_node_parseado["pid"];
		$uname_node = $proceso_node_parseado["uname"];
		$cmd_node = $proceso_node_parseado["cmd"];
	}

	$ppal	=	$plantilla->replace($ppal,array("ESTADO_PROCESO_NODE"=>$mensaje_node,
												"PID_NODE"=>$pid_node,
												"UNAME_NODE"=>$uname_node,
												"CMD_NODE"=>$cmd_node));

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>