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
	if(isset($_POST["cliente"])) $s->sesion->admin_cliente_actual=$_POST["cliente"];
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/cliente_trabajo/cliente_trabajo.html");
	$links		= 	$plantilla->load("plantillas/cliente_trabajo/links.html");
	$nuevo		= 	$plantilla->load("plantillas/cliente_trabajo/nuevo.html");
	$opcion		= 	$plantilla->load("plantillas/cliente_trabajo/cliente.html");
	$opciones	=	"";

	$conexion = new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);

	$usuarios	= new Usuarios($conexion);

	$res	= $usuarios->getClientes();
	if($res["error"]) error_log(print_r($res,1));
	else{
		foreach ($res["clientes"] as $id=>$cliente){
			if($s->sesion->admin_cliente_actual==null) $s->sesion->admin_cliente_actual=$id;
			if($s->sesion->admin_cliente_actual==$id)
				$opciones.=	$plantilla->replace($opcion,array("ID"=>$id,"NOMBRE"=>$cliente["nombre"],"SELECTED"=>"SELECTED"));
			else
				$opciones.=	$plantilla->replace($opcion,array("ID"=>$id,"NOMBRE"=>$cliente["nombre"],"SELECTED"=>""));
		}
	}
	$t="";
	$res	= $usuarios->getTrabajosCliente($s->sesion->admin_cliente_actual);
	if($res["error"]) error_log(print_r($res,1));
	else{
		$tabla = new Tabla("","","../");
		$tabla->addColumna(0,"trabajo","Trabajo");
		$tabla->addColumna(1,"links",$nuevo);
		foreach($res["trabajos"] as $id=>$trabajo){
			$row = array();
			$row["trabajo"]=$trabajo["nombre"];
			$row["links"]=$plantilla->replace($links,array("ID"=>$id));
			$tabla->addRenglon($row);
		}
		$t=$tabla->getTabla();
	}
	$ppal	=	$plantilla->replace($ppal,array("OPCIONES"=>$opciones,"TABLA"=>$t));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal));
	$s->salvar();

	echo $base;

?>
