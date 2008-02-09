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

	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../index.php");
		exit;
	}
	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/papelera/papelera.html");
	$links		= 	$plantilla->load("plantillas/papelera/links.html");

	$conexion = new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);

	$usuarios	= new Usuarios($conexion);
	$interfaz = new Interfaz($conexion,$plantilla,$s);
	$mensaje = "";
	$error = "";

	if(isset($_GET["trabajo"])){
		$res	= $usuarios->resucitarTrabajo($_GET["trabajo"]);
		if($res["error"])
			$error	= $interfaz->getError($res);
		else
			$mensaje = $interfaz->getMensaje("CT005");
	}
	$res	= $usuarios->getTrabajosBorrados();
	if($res["error"]) $error	= $interfaz->getError($res);
	else{
		$tabla = new Tabla("","","../");
		$tabla->addColumna(0,"cliente","Cliente");
		$tabla->addColumna(1,"trabajo","Trabajo");
		$tabla->addColumna(2,"nodos","Nodos");
		$tabla->addColumna(3,"tiempo_maximo","Tiempo m&aacute;ximo");
		$tabla->addColumna(4,"cola","Cola");
		$tabla->addColumna(5,"quota","Quota");
		$tabla->addColumna(6,"links","");
		foreach($res["trabajos"] as $id=>$trabajo){
			$row = array();
			$row["cliente"]=$trabajo["cliente"];
			$row["trabajo"]=$trabajo["nombre"];
			$row["nodos"]=$trabajo["nodos"];
			$row["tiempo_maximo"]=$trabajo["tiempo_maximo"];
			$row["cola"]=$trabajo["cola"];
			$row["quota"]=$trabajo["quota"];
			$row["links"]=$plantilla->replace($links,array("ID"=>$id));
			$tabla->addRenglon($row);
		}
		$t=$tabla->getTabla();
	}
	$ppal	=	$plantilla->replace($ppal,array("TABLA"=>$t));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;

?>