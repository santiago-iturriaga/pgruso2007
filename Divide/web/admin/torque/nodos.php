<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	include_once("XMLParser.php");
	include_once("const.inc.php");
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


	if (ISSET($_REQUEST["disable"])) {
		$id = $_REQUEST["disable"];
		$qnodes_result = `ssh -l $username $host "$qnodes_cmd -o $id; exit" 2>&1`;

		if ($qnodes_result=="") {
			$mensaje ="El nodo fue deshabilitado.";
		} else {
			$mensaje ="No fue posible deshabilitar el nodo.";
		}
	}

	if (ISSET($_REQUEST["enable"])) {
		$id = $_REQUEST["enable"];
		$qnodes_result = `ssh -l $username $host "$qnodes_cmd -c $id; exit" 2>&1`;

		if ($qnodes_result=="") {
			$mensaje ="El nodo fue habilitado.";
		} else {
			$mensaje ="No fue posible habilitar el nodo.";
		}
	}

	// Listado de todos los trabajos
	$qnodes = `ssh -l $username $host "$qnodes_cmd -x; exit" 2>&1`;


	if ($qnodes == "") {
		$pagina	.= "<pre>Empty.</pre>";
	} else {
		$xmlparser = new XMLParser();
		$xml	= $xmlparser->parsear($qnodes,'UTF-8');
		$xml 	= $xmlparser->getObjeto($xml,true,array("NODE"=>"NODE"));
		$nodos	= $xml["DATA"]["NODE"];
		$titulos	= array();
		$i = 0;
		$tabla = new Tabla("","","../../");
		foreach($nodos as $nodo){
			foreach(array_keys($nodo) as $k){
				if(!isset($titulos[$k])){
					$tabla->addColumna($i,$k,$k);
					$i++;
					$titulos[$k]=$k;
				}
				if($k=='STATUS'){
					$status = explode(",",$nodo[$k]);
					$status_ = array();
					foreach($status as $s_){
						$aux = explode("=",$s_,2);
						array_push($status_,
									array_shift($aux).
									'</td><td>'.
									array_shift($aux));
					}
					$nodo[$k] = '<table><tr><td>'.
								implode('</td></tr><tr><td>',$status_).
								'</td></tr></table>';
				}
			}

			if (trim(strtolower($nodo["STATE"]))=="offline")
				$nodo["STATE"] = "<a href='nodos.php?enable=".$nodo["NAME"]."'>".$nodo["STATE"]." [enable]</a>";
			elseif (trim(strtolower($nodo["STATE"]))!="down")
				$nodo["STATE"] = "<a href='nodos.php?disable=".$nodo["NAME"]."'>".$nodo["STATE"]." [disable]</a>";
			$tabla->addRenglon($nodo);
		}

		$pagina =  $tabla->getTabla();
		}
	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
	?>