<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("TPL.php");
	include_once("Usuarios.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/usuarios_alertas/usuarios_alertas.html");
	$opcion_select	=	$plantilla->load("plantillas/usuarios_alertas/usuario.html");
	$link		=$plantilla->load("plantillas/usuarios_alertas/link.html");
	$menu=$plantilla->replace($plantilla->load("plantillas/menu_usuarios.html"),
							  array("SMENU_ALERTAS"=>" id='smactual' "));


	$mensaje = "";
	$error = "";

	$conexion = new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$Usuarios	= new Usuarios($conexion);

	$mensaje	= "";
	$form_nuevo	= "";

	$opciones_usuarios	= "";


	if(isset($_GET["eliminar"])){
		$res=$Usuarios->eliminarUsuarioAlertas($_GET["eliminar"]);
		if($res["error"]) error_log(print_r($res,1));
	}


	if(isset($_POST["usuario"])){
		$res=$Usuarios->agregarUsuarioAlertas($_POST["usuario"]);
		if($res["error"]) error_log(print_r($res,1));
	}

	$res=$Usuarios->getUsuariosSinAlertas();
	if($res["error"]) error_log(print_r($res,1));
	else{
		$opciones_usuarios.=$plantilla->replace($opcion_select,array("ID"=>"","NOMBRE"=>"","SELECTED"=>""));
		foreach ($res["usuarios"] as $id=>$usuario_){
			$selected ="";
			$opciones_usuarios.=$plantilla->replace($opcion_select,array("ID"=>$id,"NOMBRE"=>$usuario_["login"],"SELECTED"=>""));
			}
		}
	$tabla_usuarios = "";
	$res=$Usuarios->getUsuariosConAlertas();
	$usrs = array();
	if($res["error"]) error_log(print_r($res,1));
	else $usrs = $res["usuarios"];
	$tabla = new Tabla("","","../");
	$tabla->addColumna(0,"login","Login");
	$tabla->addColumna(2,"email","email");
	$tabla->addColumna(3,"link","");
	foreach ($res["usuarios"] as $id=>$usuario){
		$usu= array("login"=>$usuario["login"],
					"email"=>$usuario["email"],
					"link"=>$plantilla->replace($link,array("ID"=>$id)));
		$tabla->addRenglon($usu);
		}
	$tabla_usuarios=$tabla->getTabla();

	$ppal  = $plantilla->replace($ppal,array("USUARIOS"=>$opciones_usuarios,
											"TABLA_USUARIOS"=>$tabla_usuarios));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error,
												"MENU_USUARIOS"=>" id='actual' ",
												"MENU_GANGLIA"=>" class='menu_tab'",
												"MENU_MAUI"=>" class='menu_tab'",
												"MENU_TORQUE"=>" class='menu_tab'",
												"MENU_ALERTAS"=>" class='menu_tab'",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"MENU"=>$menu));
	$s->salvar();

	echo $base;
?>