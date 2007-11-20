<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("TPL.php");
	
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/usuarios/usuarios.html");
	$menu		=	$plantilla->load("plantillas/menu_vertical.html");
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu));
	
	echo $base;												
?>