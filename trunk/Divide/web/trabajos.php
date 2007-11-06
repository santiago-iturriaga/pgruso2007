<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");	
	include_once("MiSesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	
	$sesion = new MiSesion();
	if($sesion==null or !$sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit; 
		}
	
	if(isset($_GET["trabajo"]) and isset($sesion->Usuario->trabajos[$_GET["trabajo"]])){
		$sesion->TrabajoActual	=	$_GET["trabajo"];
		header("Location: archivos.php");
		$sesion->salvar();
		exit; 
	}
	
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/trabajos/trabajos.html");
	$p_trabajo	= 	$plantilla->load("plantillas/trabajos/trabajo.html");
	
	$trabajos	= "";
	foreach($sesion->Usuario->trabajos as $id=>$trabajo)
		$trabajos.=	$plantilla->replace($p_trabajo,array("ID_TRABAJO"=>$id,
													"TRABAJO"=>$trabajo["nombre"]));
	$ppal	=	$plantilla->replace($ppal,array("TRABAJOS"=>$trabajos));
		
	
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	$sesion->salvar();
	echo $base;												
?>