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
	$ppal		= 	$plantilla->load("plantillas/configuracion/configuracion.html");
	$mensaje = "";
	$error = "";

	$command = SSH." -l ".USERNAME." ".HOST." \"".CONFIG_CMD."; exit\" 2>&1";
	$config = `$command`;
	$filas	= explode("\n",$config);
//	echo '<pre>';print_r($filas);echo '</pre>';exit;
	$pagina	= '<table><tr><td>';
	$renglones = array();
	foreach ($filas as $fila){
		$fila=trim($fila);
		if(!($fila[0]=='#' or $fila==""))
			{
			array_push($renglones,
						implode('</td><td>', split  ( "[\t ]+", $fila,3)));
			}
	}
	$pagina.=implode('</td></tr><tr><td>',$renglones).
			'</td></tr></table>';
	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"SMENU_CONF"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;?>