<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");	
	include_once("Tabla\Tabla.php");	
	include_once("MiSesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	
	$sesion = new MiSesion();
	if($sesion==null or !$sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit; 
		}
	$sesion->TrabajoActual	= null;	
	$sesion->ClienteActual	= null;
	$sesion->Directorio		= null;
	if(isset($_GET["trabajo"]) and isset($sesion->Usuario->trabajos[$_GET["trabajo"]])){
		$sesion->TrabajoActual	=	$_GET["trabajo"];
		$sesion->ClienteActual	=	$sesion->Usuario->trabajos[$sesion->TrabajoActual]["id_cliente"];
		$sesion->salvar();
		header("Location: archivos.php");
		exit; 
	}
	
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/trabajos/trabajos.html");
	$link		= 	$plantilla->load("plantillas/trabajos/link.html");
	
	$trabajos	= "";
	$tabla = new Tabla();
	$tabla->addColumna(2,"cliente_trabajo","Cliente - Trabajo");
	$tabla->addColumna(3,"link","&nbsp;");
	foreach ($sesion->Usuario->trabajos as $id=>$trabajo){
			$elemento=array("cliente_trabajo"=>$trabajo["cliente"]." - ".$trabajo["trabajo"],
							"link"=>$plantilla->replace($link,array("ID"=>$id)));
			$tabla->addRenglon($elemento);
	}
	
	$ppal	=	$plantilla->replace($ppal,array("TRABAJOS"=>$tabla->getTabla()));
	
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	$sesion->salvar();
	echo $base;												
?>