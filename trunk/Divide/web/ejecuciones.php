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
		$s->sesion->archivo_actual = $archivo_salida = RAIZ_SISTEMA.'/'.$s->sesion->ClienteActual.'/'.$s->sesion->TrabajoActual.'/'.OUTPUT.'_'.$_GET["salida"];

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
		$tabla = new Tabla("","tabla");
		$tabla->addColumna(1,"archivo","Archivo");
		$tabla->addColumna(2,"fecha_ini","Inicio");
		$tabla->addColumna(3,"fecha_ejecucion","Ejecuci&oacute;n");
		$tabla->addColumna(4,"fecha_fin","Fin");
		$tabla->addColumna(5,"tiempo_ejecucion","Tiempo");
		$tabla->addColumna(6,"link","");
		foreach ($res["salida"] as $id=>$ejecucion){
			$ejecucion["fecha_ini"]=array_shift(explode('.',$ejecucion["fecha_ini"],2));
			$ejecucion["fecha_fin"]=array_shift(explode('.',$ejecucion["fecha_fin"],2));
			$ejecucion["fecha_ejecucion"]=array_shift(explode('.',$ejecucion["fecha_ejecucion"],2));
			if($ejecucion["fecha_fin"]!="")
				$ejecucion["link"]=$plantilla->replace($link,array("ID"=>$id,"URL"=>""));
			else
				$ejecucion["link"]=$plantilla->replace($link,array("ID"=>$id,"URL"=>"&terminado=1"));
			if($ejecucion["fecha_ejecucion"]=="") $ejecucion["fecha_ejecucion"] = "<b> - En espera - </b>";
			elseif($ejecucion["fecha_fin"]=="") $ejecucion["fecha_fin"] = "<b> - En ejecuci&oacute;n - </b>";
			$tabla->addRenglon($ejecucion);
		}
		$ejecuciones = $tabla->getTabla();
	}

	$ppal	=	$plantilla->replace($ppal,array("EJECUCION"=>$ejecuciones));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu,"MENSAJE"=>$msj,"ERROR"=>$msjerror,"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
							"HEAD"=>""
							));
	$s->salvar();
	echo $base;
?>