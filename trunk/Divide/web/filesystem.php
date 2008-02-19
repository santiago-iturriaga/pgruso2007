<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("MiSesion.php");
	$sesion	=	new MiSesion();
	if ($sesion ==NULL)
		{
		header("Location: index.php");
		exit;
		}


	if(isset($_GET["archivo"])){
		if($_GET["accion"]=="descargar"){
			$archivo	=	$sesion->Directorio->getContenido($_GET["archivo"]);
			header ("Content-Disposition: attachment; filename=".$_GET["archivo"]."\n\n");
			header ("Content-Type: application/text");
			header ("Content-Length: ".strlen($archivo));
			echo $archivo;
			exit;
		}
		if($_GET["accion"]=="eliminar"){
			$res	=	$sesion->Directorio->eliminar($_GET["archivo"]);
			error_log(print_r($res,1));
		}
	}
	if(isset($_GET["ruta"])){
		$sesion->Directorio->mover($_GET["ruta"]);
	}
	include_once("TPL.php");

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.htm");
	$pagina		=	$plantilla->load("plantillas/filesystem/filesystem.html");
	$ruta		=	$plantilla->load("plantillas/filesystem/ruta.html");
	$directorio	=	$plantilla->load("plantillas/filesystem/directorio.html");
	$archivo_	=	$plantilla->load("plantillas/filesystem/archivo.html");

	$res		=	$sesion->Directorio->getArchivos();
	if($res["error"]){print_r($res); exit;}
	$archivos	=	$res["archivos"];
	$directorios=	$res["directorios"];
	//asort($archivos);
	//asort($directorios);
	$mostrar	=	"";
	foreach($directorios as $archivo=>$valores){
		$mostrar	.=	$plantilla->replace($directorio,array("RUTA"=>$archivo,
																  "RUTA_ABSOLUTA"=>$archivo));
	}
	foreach($archivos as $archivo=>$valores){
		$mostrar	.=	$plantilla->replace($archivo_,array("RUTA"=>$archivo));
	}

	$camino	=	$sesion->Directorio->camino;
	$ruta_completa	= array();

	foreach($camino as $c){
		array_push($ruta_completa,$plantilla->replace($ruta,array("CARPETA"=>$c)));
	}
	$ruta	=	implode(">",$ruta_completa);
	$pagina	=	$plantilla->replace($pagina,array("ARCHIVOS"=>$mostrar,
												  "RUTA"=>$ruta));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$pagina,
												"MENU"=>"","USUARIO_LOGUEADO"=>$s->sesion->Usuario->login));
	$sesion->salvar();
	echo $base;
?>
