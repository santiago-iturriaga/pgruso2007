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
	$ppal		= 	$plantilla->load("plantillas/colas/colas.html");
	$mensaje = "";
	$error = "";

	if (ISSET($_REQUEST["detener"])) {
		$id = $_REQUEST["detener"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".QSTOP_CMD." $id; exit\" 2>&1";
		$qstop_result = `$command`;

		if ($qstop_result=="")
			$mensaje = "La cola fue detenida.";
		else {
			$mensaje = "No fue posible detener la cola.<br/>".$qstop_result;
			error_log($qstop_result);
		}
	}
	if (ISSET($_REQUEST["iniciar"])) {
		$id = $_REQUEST["iniciar"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".QSTART_CMD." $id; exit\" 2>&1";
		$qstart_result = `$command`;

		if ($qstart_result=="")
			$mensaje = "La cola fue iniciada.";
		else {
			$mensaje = "No fue posible iniciar la cola.<br/>".$qstart_result;
			error_log($qstart_result);
		}
	}

	$command = SSH." -l ".USERNAME." ".HOST." \"".QSTAT_CMD." -Qf; exit\" 2>&1";
	$qstatQ = `$command`;

	$pagina = getTablasColas($qstatQ);

	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"SMENU_COLAS"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;?>
