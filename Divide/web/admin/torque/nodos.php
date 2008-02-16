<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	include_once("XMLParser.php");
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
		$command = SSH." -l ".USERNAME." ".HOST." \"".QNODES_CMD." -o $id; exit\" 2>&1";
		$qnodes_result = `$command`;

		if ($qnodes_result=="") {
			$mensaje ="El nodo fue deshabilitado.";
		} else {
			$mensaje ="No fue posible deshabilitar el nodo.<br/>".$qnodes_result;
		}
	}

	if (ISSET($_REQUEST["enable"])) {
		$id = $_REQUEST["enable"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".QNODES_CMD." -c $id; exit\" 2>&1";
		$qnodes_result = `$command`;

		if ($qnodes_result=="") {
			$mensaje ="El nodo fue habilitado.";
		} else {
			$mensaje ="No fue posible habilitar el nodo.<br/>".$qnodes_result;
		}
	}

	// Listado de todos los trabajos
	$command = SSH." -l ".USERNAME." ".HOST." \"".QNODES_CMD." -x; exit\" 2>&1";
	$qnodes = `$command`;


	if ($qnodes == "") {
		$pagina	.= "<pre>No se encontraron nodos disponibles.</pre>";
	} else {
		$xmlparser = new XMLParser();
		$xml	= $xmlparser->parsear($qnodes,'UTF-8');
		$xml 	= $xmlparser->getObjeto($xml,true,array("NODE"=>"NODE"));
		$nodos	= $xml["DATA"]["NODE"];
		$titulos	= array();
		$i = 0;
		$tabla = new Tabla("","","../../");
		if($nodos == "") $nodos=array();
		foreach($nodos as $nodo){
			foreach(array_keys($nodo) as $k){
				if(!isset($titulos[$k])){
					$nombre_titulo = "";
					switch (trim(strtolower($k)))
					{
						case "name":
							$nombre_titulo = "Nombre";
						    break;
						case "state":
							$nombre_titulo = "Estado";
						    break;
						case "np":
							$nombre_titulo = "#Procesadores";
						    break;
						case "ntype":
							$nombre_titulo = "Tipo";
						    break;
						case "status":
							$nombre_titulo = "Detalle";
						    break;
						default:
							$nombre_titulo = $k;
					}
					$tabla->addColumna($i,$k,$nombre_titulo);
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
					$nodo[$k] = "<table><tr><td>".
								implode("</td></tr><tr><td>",$status_).
								"</td></tr></table>";

				}
			}

			if (substr_count(trim(strtolower($nodo["STATE"])), "offline") > 0) {
				//$nodo["STATE"] = $nodo["STATE"]."&nbsp;<a href='nodos.php?enable=".$nodo["NAME"]."'><img src='../../imagenes/control_play_blue.png' title='Iniciar'/></a>";
				$nodo["NAME"] .= "&nbsp;<a href='nodos.php?enable=".$nodo["NAME"]."'><img src='../../imagenes/control_play_blue.png' title='Iniciar'/></a>";
			} else {
				$nodo["NAME"] .= "&nbsp;<a href='nodos.php?disable=".$nodo["NAME"]."'><img src='../../imagenes/control_pause_blue.png' title='Detener'/></a>";
				//elseif (trim(strtolower($nodo["STATE"]))!="down")
				//$nodo["STATE"] = $nodo["STATE"]."&nbsp;<a href='nodos.php?disable=".$nodo["NAME"]."'><img src='../../imagenes/control_pause_blue.png' title='Detener'/></a>";
			}
			$tabla->addRenglon($nodo);
		}

		$pagina =  $tabla->getTabla();
		}
	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"SMENU_NODOS"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
	?>