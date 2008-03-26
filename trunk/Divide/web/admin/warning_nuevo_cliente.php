<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("TPL.php");
	include_once("Usuarios.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Interfaz.php");
	include_once("Tabla/Tabla.php");
	include_once("Servidor.php");

	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../index.php");
		exit;
	}


	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/warning_nuevo_cliente.html");

	$menu=$plantilla->replace($plantilla->load("plantillas/menu_usuarios.html"),
							  array("SMENU_CT"=>" id='smactual' "));

	$ppal = $plantilla->replace($ppal,array("RAIZ"=>RAIZ, "GRUPOFENTON"=>GRUPOFENTON));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENU_USUARIOS"=>" id='actual' ",
												"MENU_GANGLIA"=>" class='menu_tab'",
												"MENU_MAUI"=>" class='menu_tab'",
												"MENU_TORQUE"=>" class='menu_tab'",
												"MENU_ALERTAS"=>" class='menu_tab'",
												"MENSAJE"=>null,
												"ERROR"=>null,
												"MENU_USUARIOS"=>" id='actual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"MENU"=>$menu));
	$s->salvar();

	echo $base;

?>