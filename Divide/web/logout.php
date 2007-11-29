<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");

	$manejador = new ManejadorSesion();
	$sesion = $manejador->getSesion();
	$manejador->cerrar();
	header("Location: index.php");
	exit;


	?>