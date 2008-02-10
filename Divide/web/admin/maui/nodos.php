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
			$ar_nodo[$cabezal[$i]] = $n;
			$i++;
		}
		$table->addRenglon($ar_nodo);
	}
	$pagina	=	$table->getTabla().
				$pie;
	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"SMENU_NODOS"=>" id='smactual' ",
												"ERROR"=>$error));
	$s->salvar();

	echo $base;?>