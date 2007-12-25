<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");

	$s = new Sesion(0);
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/trabajos/trabajos.html");;

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal));
	$s->salvar();
	echo $base;
?>