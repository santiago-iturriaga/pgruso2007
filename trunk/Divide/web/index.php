<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("MiSesion.php");
	$sesion	=	new MiSesion();
	if ($sesion ==NULL)
		$sesion = new MiSesion(1);
	include_once("TPL.php");
	
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.htm");
	$ppal		= 	"";
	
	if(isset($_POST["login"])){
		$res	=	$sesion->Usuario->Login($_POST["login"],$_POST["password"]);
		error_log(print_r($res));
	}
	
	if(!$sesion->Usuario->Logueado()){
		$ppal	=	$plantilla->load("plantillas/login/login.html");
	}
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENU"=>""));
	$sesion->salvar();
	echo $base;												
?>
