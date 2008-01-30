<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Tabla/Tabla.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit;
		}
	$s->sesion->TrabajoActual	= null;
	$s->sesion->ClienteActual	= null;
	$s->sesion->Directorio		= null;
	if(isset($_GET["trabajo"]) and isset($s->sesion->Usuario->trabajos[$_GET["trabajo"]])){
		$s->sesion->TrabajoActual	=	$_GET["trabajo"];
		$s->sesion->ClienteActual	=	$s->sesion->Usuario->trabajos[$s->sesion->TrabajoActual]["id_cliente"];
		$s->salvar();
		header("Location: archivos.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/trabajos/trabajos.html");
	$link		= 	$plantilla->load("plantillas/trabajos/link.html");
	$msj = null;
	$msjerror = null;
	$menu	=	$plantilla->load("plantillas/menu2.html");

	$trabajos	= "";
	$tabla = new Tabla();
	$tabla->addColumna(2,"cliente_trabajo","Cliente - Trabajo");
	$tabla->addColumna(3,"link","&nbsp;");
	foreach ($s->sesion->Usuario->trabajos as $id=>$trabajo){
			$elemento=array("cliente_trabajo"=>$trabajo["cliente"]." - ".$trabajo["trabajo"],
							"link"=>$plantilla->replace($link,array("ID"=>$id)));
			$tabla->addRenglon($elemento);
	}

	$ppal	=	$plantilla->replace($ppal,array("TRABAJOS"=>$tabla->getTabla()));

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu,"MENSAJE"=>$msj,"ERROR"=>$msjerror));
	$s->salvar();
	echo $base;
?>