<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	include_once("const.inc.php");
	include_once("lib.inc.php");

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
	if (ISSET($_REQUEST["eliminar"])) {
		$id = $_REQUEST["eliminar"];
		$qdel_result = `ssh -l $username $host "$qdel_cmd $id; exit" 2>&1`;

		if ($qdel_result=="") {
			$mensaje = "El trabajo fue eliminado.";
		} else {
			$mensaje = "No fue posible eliminar el trabajo.";
			error_log($qdel_result);
		}
	}
	if (ISSET($_REQUEST["detener"])) {
		$id = $_REQUEST["detener"];
		$qhold_result = `ssh -l $username $host "$qhold_cmd -h $held_type $id; exit" 2>&1`;
		if ($qhold_result=="") {
			$mensaje = "El trabajo fue detenido.";
		} else {
			$mensaje = "No fue posible detener el trabajo.";
			error_log($qhold_result);
		}
	}
	if (ISSET($_REQUEST["reiniciar"])) {
		$id = $_REQUEST["reiniciar"];
		$qrls_result = `ssh -l $username $host "$qrls_cmd -h $held_type $id; exit" 2>&1`;
		if ($qrls_result=="") {
			$mensaje = "El trabajo fue reiniciado.";
		} else {
			$mensaje = "No fue posible reiniciar el trabajo.";
			error_log($qrls_result);
		}

	}
	// Listado de todos los trabajos
	$qstat = `ssh -l $username $host "$qstat_cmd; exit" 2>&1`;


	if ($qstat == "") {
		$pagina	.= "<pre>Empty.</pre>";
	} else {
		$tabla = getTablaTrabajos($qstat);
		$pagina =  $tabla->getTabla();
		}
	if (ISSET($_REQUEST["id"])) {
		// Detalle de un trabajo
		$id = $_REQUEST["id"];
		$qstat_job = `ssh -l $username $host "$qstat_cmd -f $id; exit" 2>&1`;
		$pagina.=getStatusJob($qstat_job);


	}
	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
	?>