<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");	
	include_once("MiSesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	
	$sesion = new MiSesion();
	if($sesion==null or $sesion->Usuario==null) $sesion = new MiSesion(1);
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.htm");
	$ppal		= 	"";
	
	if(isset($_POST["login"])){
		$conexion= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
		$sesion->Usuario->setBD($conexion);
		$res=$sesion->Usuario->Login($_POST["login"],$_POST["password"]);
		print_r($res);
	}
	
	if(!$sesion->Usuario->Logueado()){
		$ppal	=	$plantilla->load("plantillas/login/login.html");
	}
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	$sesion->salvar();
	echo $base;												
?>
