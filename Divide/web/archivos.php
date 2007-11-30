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

	if(isset($_GET["descargar"])){
		$archivo	=	$s->sesion->Directorio->getContenido($_GET["descargar"]);
		header ("Content-Disposition: attachment; filename=".$_GET["descargar"]."\n\n");
		header ("Content-Type: application/text");
		header ("Content-Length: ".strlen($archivo));
		echo $archivo;
		exit;
	}
	if(isset($_GET["eliminar"])){
		$res	=	$s->sesion->Directorio->eliminar($_GET["eliminar"]);
		error_log(print_r($res,1));
	}
	if(isset($_GET["ejecutar"])){
		$res	=	$s->sesion->Directorio->ejecutar($_GET["ejecutar"]);
		error_log(print_r($res,1));
	}


	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/archivos/archivos.html");
	$p_directorio	=	$plantilla->load("plantillas/archivos/directorio.html");
	$p_archivo	=	$plantilla->load("plantillas/archivos/archivo.html");
	$p_ruta		=	$plantilla->load("plantillas/archivos/ruta.html");
	$menu		=	$plantilla->load("plantillas/menu.html");//$s->sesion->getMenuVertical($plantilla->load("plantillas/menu_vertical.html"),$plantilla);
	if($s->esion->Directorio == null)
		{
		$s->sesion->Directorio	= new Directorio(RAIZ."/".$s->sesion->ClienteActual."/".$s->sesion->TrabajoActual);
		}


	if(isset($_GET["directorio"]) and $_GET["directorio"]!=""){
		$s->sesion->Directorio->mover($_GET["directorio"]);
		}
	if(isset($_GET["retroceder"]) and $_GET["retroceder"]!=""){
		$s->sesion->Directorio->retroceder($_GET["retroceder"]);
		}
	$res		=	$s->sesion->Directorio->getArchivos();
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

	$camino		=	$s->sesion->Directorio->camino;
	$camino[0]	=	"~";
	$ruta	= "";

	while($carpeta=array_shift($camino)){
		$ruta	.= $plantilla->replace($p_ruta,array("DIRECTORIO"=>$carpeta,
													 "PASOS"=>count($camino)));
	}


	$ppal	=	$plantilla->replace($ppal,array("MENU_VERTICAL"=>"",
												"ARCHIVOS"=>$p_archivos,
												"RUTA"=>$ruta));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu));
	$s->salvar();
	echo $base;
?>