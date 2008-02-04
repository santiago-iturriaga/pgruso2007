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
	$ppal		= 	$plantilla->load("plantillas/colas/colas.html");
	$mensaje = "";
	$error = "";



	if (ISSET($_REQUEST["detener"])) {
		$id = $_REQUEST["detener"];
		$qstop_result = `ssh -l $username $host "$qstop_cmd $id; exit" 2>&1`;

		if ($qstop_result=="")
			$mensaje = "La cola fue detenida.";
		else {
			$mensaje = "No fue posible detener la cola.";
			error_log($qstop_result);
		}
	}
	if (ISSET($_REQUEST["iniciar"])) {
		$id = $_REQUEST["iniciar"];
		$qstart_result = `ssh -l $username $host "$qstart_cmd $id; exit" 2>&1`;

		if ($qstart_result=="")
			$mensaje = "La cola fue iniciada.";
		else {
			$mensaje = "No fue posible iniciar la cola.";
			error_log($qstart_result);
		}
	}

	$qstatQ = `ssh -l $username $host "$qstat_cmd -Qf; exit" 2>&1`;

	$pagina = getTablasColas($qstatQ);

	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;?>
