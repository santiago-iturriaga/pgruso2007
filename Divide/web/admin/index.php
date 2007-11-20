<?php
	set_include_path(get_include_path().';'.
					 '../../lib');
	include_once("TPL.php");
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal	=	$plantilla->load("plantillas/login/login.html");

	
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	
	echo $base;												
?>