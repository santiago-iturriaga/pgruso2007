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
	$ppal		= 	$plantilla->load("plantillas/nodos/nodos.html");
	$mensaje = "";
	$error = "";

	$command = SSH." -l ".USERNAME." ".HOST." \"".DIAGNOSE_CMD." -n; exit\" 2>&1";
	$nodes = `$command`;
	$nodes = trim($nodes);
	$nodos = explode("\n",$nodes);
	array_shift($nodos);
	$cabezal = split  ( "[\n\r\t ]+", array_shift($nodos));
	$pie	= array_pop($nodos);

	$table = new Tabla("","","../../");
	$i=0;
	foreach ($cabezal as $c){
		$table->addColumna($i,$c,$c);
		$i++;
	}
	foreach ($nodos as $nodo){
		$nodo = split  ( "[\n\r\t ]+", $nodo);
		$ar_nodo = array();
		$i=0;
		foreach ($nodo as $n){
			if($cabezal[$i] == 'Name')
				$n = array_shift(explode('@',$n,2));
			$ar_nodo[$cabezal[$i]] = $n;
			$i++;
		}
		$table->addRenglon($ar_nodo);
	}
	$tabla	=	$table->getTabla();

	if ($mensaje != "") {
		$mensajepl = $plantilla->load("plantillas/mensaje.html");
		$mensajepl = $plantilla->replace($mensajepl, array (
			"MENSAJE" => $mensaje
		));
	} else {
		$mensajepl = "";
	}

	if ($error != "") {
		$errorpl = $plantilla->load("plantillas/error.html");
		$errorpl = $plantilla->replace($errorpl, array (
			"ERROR" => $error
		));
	} else {
		$errorpl = "";
	}

	$ppal	=	$plantilla->replace($ppal,array("TABLA"=>$tabla, "PIE"=>$pie));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensajepl,
												"SMENU_NODOS"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$errorpl));
	$s->salvar();

	echo $base;?>