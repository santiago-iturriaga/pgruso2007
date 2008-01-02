<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");

	$s = new Sesion();

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	"";
	$mensaje = "";
	$error = "";

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>"",
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();
	echo $base;
?>