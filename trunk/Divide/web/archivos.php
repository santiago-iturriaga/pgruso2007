<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");	
	include_once("MiSesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Directorio.php");
	
	$sesion = new MiSesion();
	if($sesion==null or !$sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit; 
		}
	
	if($sesion->TrabajoActual==null){
		header("Location: trabajos.php");
		exit; 
	}
	
	if(isset($_GET["descargar"])){
		$archivo	=	$sesion->Directorio->getContenido($_GET["descargar"]);
		header ("Content-Disposition: attachment; filename=".$_GET["descargar"]."\n\n");
		header ("Content-Type: application/text");
		header ("Content-Length: ".strlen($archivo));
		echo $archivo; 
		exit;
	}
	if(isset($_GET["eliminar"])){
		$res	=	$sesion->Directorio->eliminar($_GET["eliminar"]);
		error_log(print_r($res,1));
	}
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/archivos/archivos.html");
	$p_directorio	=	$plantilla->load("plantillas/archivos/directorio.html");
	$p_archivo	=	$plantilla->load("plantillas/archivos/archivo.html");
	$p_ruta		=	$plantilla->load("plantillas/archivos/ruta.html");
	$menu		=	$sesion->getMenuVertical($plantilla->load("plantillas/menu_vertical.html"),$plantilla);
	
	if($sesion->Directorio == null)
		{
		$sesion->Directorio	= new Directorio(RAIZ."/".$sesion->Usuario->cliente["id"]."/".$sesion->TrabajoActual);
		}
	
	
	if(isset($_GET["directorio"]) and $_GET["directorio"]!=""){
		$sesion->Directorio->mover($_GET["directorio"]);
		}
	if(isset($_GET["retroceder"]) and $_GET["retroceder"]!=""){
		$sesion->Directorio->retroceder($_GET["retroceder"]);
		}
	$res		=	$sesion->Directorio->getArchivos();
	$archivos	=	$res["archivos"];
	$directorios=	$res["directorios"];
	//asort($archivos);
	//asort($directorios);
	$p_archivos	=	"";
	foreach($directorios as $d=>$valores){
		$p_archivos	.=	$plantilla->replace($p_directorio,array("NOMBRE"=>$d));
	}
	foreach($archivos as $archivo=>$valores){
		$p_archivos	.=	$plantilla->replace($p_archivo,array("NOMBRE"=>$archivo,"TAMANIO"=>$valores["size"]));
	}
	
	$camino		=	$sesion->Directorio->camino;
	$camino[0]	=	"~";
	$ruta	= "";	
	
	while($carpeta=array_shift($camino)){
		$ruta	.= $plantilla->replace($p_ruta,array("DIRECTORIO"=>$carpeta,
													 "PASOS"=>count($camino)));
	}
	
	
	$ppal	=	$plantilla->replace($ppal,array("MENU_VERTICAL"=>$menu,
												"ARCHIVOS"=>$p_archivos,
												"RUTA"=>$ruta));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	$sesion->salvar();
	echo $base;												
?>