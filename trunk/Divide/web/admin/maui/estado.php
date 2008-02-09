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
	$ppal		= 	$plantilla->load("plantillas/estado/estado.html");
	$mensaje = "";
	$error = "";


	$command = SSH." -l ".USERNAME." ".HOST." \"".STATUS_CMD."; exit\" 2>&1";
	$maui = `$comando`;

	$command = SSH." -l ".USERNAME." ".HOST." \"".SHOWSTATE_CMD."; exit\" 2>&1";
	$sistema = `$comando`;
	$ppal	=	$plantilla->replace($ppal,array("MAUI"=>$maui,
												"SISTEMA"=>$sistema));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;?>