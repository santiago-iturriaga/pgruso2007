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
	$ppal		= 	$plantilla->load("plantillas/jobs/jobs.html");
	$mensaje = "";
	$error = "";
	/*        u - USER
               o - OTHER
               s - SYSTEM
               n - None */
    $held_type = "u";
    if (ISSET($_REQUEST["iniciar"])) {
		$mensaje = torque_ejecutar_trabajo($_REQUEST["iniciar"]);
	}
	if (ISSET($_REQUEST["eliminar"])) {
		$mensaje = torque_eliminar_trabajo($_REQUEST["eliminar"]);
	}
	if (ISSET($_REQUEST["detener"])) {
		$mensaje = torque_detener_trabajo($_REQUEST["detener"],$held_type);
	}
	if (ISSET($_REQUEST["reiniciar"])) {
		$mensaje = troque_liberar_trabajo($_REQUEST["reiniciar"],$held_type);
	}

	// Listado de todos los trabajos
	$command = SSH." -l ".USERNAME." ".HOST." \"".QSTAT_CMD."; exit\" 2>&1";
	$qstat = `$command`;

	if ($qstat == "") {
		$pagina	.= "<pre>No hay trabajos.</pre>";
	} else {
		$tabla = getTablaTrabajos($qstat);
		$pagina =  $tabla->getTabla();
	}

	if (ISSET($_REQUEST["id"])) {
		// Detalle de un trabajo
		$id = $_REQUEST["id"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".QSTAT_CMD." -f $id; exit\" 2>&1";
		$qstat_job = `$command`;
		$pagina.= "<br />".getStatusJob($qstat_job);
	}
	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"SMENU_JOBS"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>