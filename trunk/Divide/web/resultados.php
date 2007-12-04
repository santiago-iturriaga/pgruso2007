<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Directorio.php");

	$s = new Sesion();

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit;
		}

	if($s->sesion->TrabajoActual==null){
		header("Location: trabajos.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/resultados/resultados.html");
	$menu		=	$plantilla->load("plantillas/menu.html");



	$ppal	=	$plantilla->replace($ppal,array());
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu));
	$s->salvar();
	echo $base;
?>