<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Tabla/Tabla.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Momento.php");

	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit;
		}

	if($s->sesion->TrabajoActual==null){
		header("Location: trabajos.php");
		exit;
	}

	if(isset($_GET["salida"])){
		$s->sesion->archivo_actual = $archivo_salida = RAIZ.'/'.$s->sesion->ClienteActual.'/'.$s->sesion->TrabajoActual.'/'.'salida_extra_'.$_GET["salida"];
		$s->sesion->ejecucion_actual = $_GET["salida"];
		$s->sesion->bytes_leidos = 0;
		$s->salvar();
		header("Location: resultados.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/ejecuciones/ejecuciones.html");
	$link		= 	$plantilla->load("plantillas/ejecuciones/link.html");
	$menu		=	$plantilla->replace($plantilla->load("plantillas/menu.html"),
										array("CLASE_RESULTADOS"=>'id="actual"'));//$s->sesion->getMenuVertical($plantilla->load("plantillas/menu_vertical.html"),$plantilla);
	$msj = null;
	$msjerror = null;


	$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento = new Momento($conexion);

	$res	= $momento->getEjecuciones($s->sesion->TrabajoActual);
	$ejecuciones= "";
	if($res["error"]) error_log(print_r($res,1));
	else{
		$tabla = new Tabla();
		$tabla->addColumna(1,"archivo","Archivo");
		$tabla->addColumna(2,"fecha_ini","Inicio");
		$tabla->addColumna(3,"fecha_ejecucion","Ejecuci&oacute;n");
		$tabla->addColumna(4,"fecha_fin","Fin");
		$tabla->addColumna(5,"link","");
		foreach ($res["salida"] as $id=>$ejecucion){
			$ejecucion["link"]=$plantilla->replace($link,array("ID"=>$id));
			$tabla->addRenglon($ejecucion);
		}
		$ejecuciones = $tabla->getTabla();
	}

	$ppal	=	$plantilla->replace($ppal,array("EJECUCION"=>$ejecuciones));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu,"MENSAJE"=>$msj,"ERROR"=>$msjerror));
	$s->salvar();
	echo $base;
?>