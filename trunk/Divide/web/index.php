<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("TPL.php");
	include_once("MiSesion.php");
	$sesion = new MiSesion();
	if($sesion==null) $sesion = new MiSesion(1);
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.htm");
	$ppal		= 	"";
	
	
	if(!$sesion->logueado){
		$ppal	=	$plantilla->load("plantillas/login/login.html");
	}
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	echo $base;												
?>
