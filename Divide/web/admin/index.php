<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");

	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	"";
	$mensaje = "";
	$error = "";

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENU"=>"",
												"MENSAJE"=>$mensaje,
												"MENU_USUARIOS"=>" class='menu_tab'",
												"MENU_GANGLIA"=>" class='menu_tab'",
												"MENU_MAUI"=>" class='menu_tab'",
												"MENU_TORQUE"=>" class='menu_tab'",
												"MENU_ALERTAS"=>" class='menu_tab'",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"HEAD"=>"",
												"ERROR"=>$error));
	$s->salvar();
	echo $base;
?>