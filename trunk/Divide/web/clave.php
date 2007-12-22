<?php
set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Directorio.php");
	include_once("Alertas.php");
	include_once("Tabla/Tabla.php");
	include_once("Interfaz.php");
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
	$ppal		= 	$plantilla->load("plantillas/clave.html");
	$menu		=	$plantilla->replace($plantilla->load("plantillas/menu.html"),
										array("CLASE_ALERTAS"=>'id="actual"'));
$conexion= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$i = new Interfaz($conexion,$plantilla,$s);

	//$ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$i->getMenuVertical()));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,"MENU"=>$menu));
	$s->salvar();
	echo $base;
?>