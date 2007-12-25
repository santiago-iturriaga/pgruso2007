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
	$ppal		= 	$plantilla->load("plantillas/cliente_trabajo/cliente_trabajo.html");
	$opcion		= 	$plantilla->load("plantillas/cliente_trabajo/cliente.html");
	$opciones	=	"";

	$conexion = new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);

	$usuarios	= new Usuarios($conexion);

	$res	= $usuarios->getClientes();
	if($res["error"]) error_log(print_r($res,1));
	else{
		foreach ($res["clientes"] as $id=>$cliente)
			$opciones.=	$plantilla->replace($opcion,array("ID"=>$id,"NOMBRE"=>$cliente["nombre"]));
	}


	$ppal	=	$plantilla->replace($ppal,array("OPCIONES"=>$opciones));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal));
	$s->salvar();

	echo $base;

?>
