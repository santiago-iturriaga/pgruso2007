<?php
	set_include_path(get_include_path().';'.
					 '../../lib');
	include_once("TPL.php");
	include_once("Usuarios.php");
	include_once("MiSesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	$sesion = new MiSesion(0);
	if($sesion == null or !$sesion->Usuario->Logueado() or !$sesion->Usuario->administrador){
		header("Location: ../index.php");
		exit;
	}
	
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/usuarios/usuarios.html");
	$opcion_select=$plantilla->load("plantillas/usuarios/grupo.html");
	$link_nuevo_usuario=$plantilla->load("plantillas/usuarios/link_nuevo.html");
	$link_eliminar_usuario=$plantilla->load("plantillas/usuarios/link_eliminar.html");
	
	$conexion = new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$Usuarios	= new Usuarios($conexion);
	
	$mensaje	= "";
	$form_nuevo	= "";
	
	$opciones_grupos	= "";
	
	if(isset($_POST["grupo"])) $sesion->grupo_actual  = $_POST["grupo"];
	
	if(isset($_GET["nuevo"])){
		$form_nuevo=$plantilla->load("plantillas/usuarios/nuevo.html");
	}
	if(isset($_GET["nuevogrupo"])){
		$form_nuevo=$plantilla->load("plantillas/usuarios/nuevo_grupo.html");
	}
	
	if(isset($_GET["eliminar"])){
		$res=$Usuarios->bajaUsuario($_GET["eliminar"]);
		if($res["error"]) error_log(print_r($res,1));
	}
	
	
	if(isset($_POST["login"])){
		$administrador = 'N';
		if(isset($_POST["administrador"])) $administrador='S'; 
		
		$res=$Usuarios->altaUsuario($_POST["login"],$_POST["clave"],$administrador,$_POST["email"],array($sesion->grupo_actual));
		if($res["error"]) error_log(print_r($res,1));
	}	
	
	if(isset($_POST["nombre_grupo"])){
		$res=$Usuarios->altaGrupo($_POST["nombre_grupo"]);
		if($res["error"]) error_log(print_r($res,1));
		else $sesion->grupo_actual=$res["id"];
	}	
	
	$res=$Usuarios->getGrupos();
	if($res["error"]) error_log(print_r($res,1));
	else{
		$opciones_grupos.=$plantilla->replace($opcion_select,array("ID"=>"","NOMBRE"=>"","SELECTED"=>""));
		foreach ($res["grupos"] as $id=>$nombre){
			$selected ="";
			if($sesion->grupo_actual==$id) $selected="SELECTED";
			$opciones_grupos.=$plantilla->replace($opcion_select,array("ID"=>$id,"NOMBRE"=>$nombre,"SELECTED"=>$selected));
			}
		}
	$tabla_usuarios = "";
	if($sesion->grupo_actual != null){
		$res=$Usuarios->getUsuarios(array($sesion->grupo_actual));
		$usrs = array();
		if($res["error"]) error_log(print_r($res,1));
		else $usrs = $res["usuarios"];
		$tabla = new Tabla("","","../");
		$tabla->addColumna(0,"login","Login");
		$tabla->addColumna(2,"email","email");
		$tabla->addColumna(3,"link",$link_nuevo_usuario);
		foreach ($res["usuarios"] as $id=>$usuario){
			$usu= array("login"=>$usuario["login"],
						"email"=>$usuario["email"],
						"link"=>$plantilla->replace($link_eliminar_usuario,array("ID"=>$id)));
			$tabla->addRenglon($usu);
		}
		$tabla_usuarios=$tabla->getTabla();
	}
	
	$ppal  = $plantilla->replace($ppal,array("GRUPOS"=>$opciones_grupos,
												"TABLA_USUARIOS"=>$tabla_usuarios,
												"NUEVO"=>$form_nuevo));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal));
	$sesion->salvar();
	
	echo $base;												
?>