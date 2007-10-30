<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("Ruso/lib/TPL.php");
	$logueado 	=	false;
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.htm");
	$ppal		= 	"";
	
	if(!$sesion->Usuario->Logueado()){
		$ppal	=	$plantilla->load("plantillas/login/login.html");
	}
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	echo $base;												
?>
