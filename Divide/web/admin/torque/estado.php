<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	include_once("lib.inc.php");

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/colas/colas.html");
	$mensaje = "";
	$error = "";

	$command = SSH." -l ".USERNAME." ".HOST." \"".QSTAT_CMD." -Bf; exit\" 2>&1";
	$qstatB = `$command`;

	$lineas= explode("\n",$qstatB);
	$head = explode(':',array_shift($lineas));
	$renglones = array();
	foreach($lineas as $linea){
		array_push($renglones,implode('</td><td>',explode('=',$linea)));
	}
	$pagina = '<table><thead><tr><td>'.array_shift($head).
			  '</td><td>'.array_shift($head).'</td></tr>'.
			  '</thead><tbody><tr><td>'.implode('</td></tr><tr><td>',$renglones).
			  '</tbody></table>';

	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"SMENU_ESTADO"=>" id='smactual' ",
												"ERROR"=>$error));
	$s->salvar();

	echo $base;?>