<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("Constantes.php");
	include_once("Directorio.php");
	
	$ruta	=	RAIZ;
	if(isset($_GET["ruta"])) $ruta	=	$_GET["ruta"];	
	
	if(isset($_GET["archivo"])){
		$Directorio	=	new Directorio($ruta);
		echo $Directorio->getContenido($_GET["archivo"]);
		exit;
	}
	include_once("TPL.php");
	
	$plantilla	=	new TPL();
	$pagina		=	$plantilla->load("plantillas/filesystem/filesystem.html");
	$directorio	=	$plantilla->load("plantillas/filesystem/directorio.html");
	$archivo_	=	$plantilla->load("plantillas/filesystem/archivo.html");
	
	
	$Directorio	=	new Directorio($ruta);
	$res		=	$Directorio->getArchivos();
	if($res["error"]){print_r($res); exit;}
	$archivos	=	$res["archivos"];
	$mostrar	=	"";
	foreach($archivos as $archivo=>$valores){
		if($valores["tipo"]==DIRECTORIO)
			$mostrar	.=	$plantilla->replace($directorio,array("RUTA"=>$archivo,
																  "RUTA_ABSOLUTA"=>$ruta.
																  				   SEPARADOR_RUTA.
																  				   $archivo));
		else
			$mostrar	.=	$plantilla->replace($archivo_,array("RUTA"=>$archivo));
	}
	$pagina	=	$plantilla->replace($pagina,array("ARCHIVOS"=>$mostrar));
	echo $pagina;
?>
