<?
set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');

	include_once("Tabla/Tabla.php");

function linea_vacia($val) {
	return ($val != "");
}

/* a partir de aca, tod va sin PRINT */
function getTablaTrabajos($datos){
	$tabla = new Tabla("","","../../");
	$tabla->addColumna(0,"Job id","Id");
	$tabla->addColumna(1,"Name","Nombre");
	$tabla->addColumna(2,"User","Usuario");
	$tabla->addColumna(3,"Time Use","Tiempo");
	$tabla->addColumna(4," S ","Estado");
	$tabla->addColumna(5,"Queue","Cola");
	$tabla->addColumna(6,"botones","");

	$lineas = explode("\n", $datos);
	$lineas = array_values(array_filter($lineas,"linea_vacia"));

	$posiciones = array();
	$cabezal	= array_shift($lineas);
	array_shift($lineas);
	$cabezales = array("Job id","Name","User","Time Use"," S ","Queue");
	foreach($cabezales as $etiqueta) {
			$inicio_cabezal = stripos($cabezal,$etiqueta);
			$posiciones[$etiqueta] = $inicio_cabezal;
		}

	$status_column = 4;
	foreach ($lineas as $linea){
		$renglon = array();
		$job_id = "";
		$job_status = "";

		foreach($cabezales as $i=>$columna) {
			$valor = "";
			if ($i == (sizeof($cabezales)-1)) {
				// Ultima columna
				$fin_td = strlen($linea);
				$valor = trim(substr($linea,$posiciones[$columna],$fin_td-$posiciones[$columna]));
				}
			elseif ($i == 0) {
				// Primer columna (ID)
				$fin_td = $posiciones[$cabezales[1]]-1;
				$fullid = trim(substr($linea,$posiciones[$columna],$fin_td-$posiciones[$columna]+1));
				list($job_id, $maquina) = split("\.",$fullid,2);
				$valor = $fullid;
				}
			else {
				$fin_td = $posiciones[$cabezales[$i+1]]-1;
				$hasta =$fin_td-$posiciones[$columna]+1;
				$valor = trim(substr($linea,$posiciones[$columna],$hasta));
				}

			if ($i == $status_column) {
				$job_status = $valor;
				switch($valor) {
						case "H":
							$valor="Held <img src='../../imagenes/cog_error.png' />";
							break;
						case "Q":
							$valor="Queued <img src='../../imagenes/hourglass_go.png' />";
							break;
						case "R":
							$valor="Running <img src='../../imagenes/cog_go.png' />";
							break;
						case "C":
							$valor="Complete <img src='../../imagenes/cog.png' />";
							break;
						default:
							break;
				}
			}
			if ($i == 0) {
				$valor	= "<a href='jobs.php?id=$job_id'>$valor</a></td>";
			}
			$renglon[$columna] = $valor;
		}
		$jobid =trim($job_id);
		$botones = "<a href='jobs.php?eliminar=$job_id'><img src='../../imagenes/delete.png' title='Eliminar' /></a>";
        switch ($job_status) {
        	case "H":
            	$botones .= "<a href='jobs.php?reiniciar=$job_id'><img src='../../imagenes/play_green.png' title='Reiniciar' /></a>";
                break;
            case "Q":
            	$botones .= "<a href='jobs.php?iniciar=$job_id'><img src='../../imagenes/play_green.png' title='Ejecutar' /></a>";
            	break;
            default:
            	$botones .= "<a href='jobs.php?detener=$job_id'><img src='../../imagenes/pause_green.png' title='Detener' /></a>";
				break;
        }
		$renglon["botones"]=$botones;
		$tabla->addRenglon($renglon);

	}
	return $tabla;
}

function getStatusJob($cadena){
	$salida = '<table><tbody><tr><td>'.
				implode('</td></tr><tr><td>', explode ("\n",$cadena)).
				'</td></tr></tbody></table>';
	return $salida;
}

function getTablasColas($cadena) {
	$salida	= "";
	$cadena = stristr($cadena,"Queue: ");
	$salida.= "<table border=0>";
	while (strlen($cadena) > 0) {
		$nombre_array = explode("\n", $cadena,2);
		$nombre = substr($nombre_array[0],7);

		$datos_fin = stripos($nombre_array[1],"Queue: ") - 1;
		if ($datos_fin == "") {
			$datos = substr($nombre_array[1],0);
		} else {
			$datos = substr($nombre_array[1],0,$datos_fin);
		}

		$started_str = stristr($datos,"started = ");
		if ($started_str=="") {
			$started = "";
		} else {
			$started_array = explode("\n", $started_str, 1);
			$started = substr($started_array[0],10,1);
		}



		$salida.= "<tr><td align='center' style='color:#ffffff;' bgcolor='#85859C'>";
		$salida.= "<b>Cola: </b>$nombre&nbsp;</td><td  style='color:#ffffff;' bgcolor='#85859C'>";
		if ($started=="F") {
			$salida.= "<a href='colas.php?iniciar=$nombre' style='color:#ffffff;'><img src='../../imagenes/control_play_blue.png' title='Iniciar'/> Iniciar</a>";
		} else if ($started=="T") {
			$salida.= "<a href='colas.php?detener=$nombre' style='color:#ffffff;'><img src='../../imagenes/control_pause_blue.png' title='Detener'/> Detener</a>";
		}
		$salida.= "</td></tr>";
		//$salida.= "<tr><td>";

		$datos=explode("\n",$datos);
		$datos_ = array();
		foreach($datos as $d) {
			array_push($datos_,implode('</td><td>',explode("=",$d,2)));
		}

		//$salida.= "<pre>$datos</pre>";
		$salida.=	"<tr><td>".implode('</td></tr><tr><td>',$datos_)."</td></tr>";
		//$salida.= "</td></tr>";


		//Siguiente queue
		$cadena = stristr(substr($cadena,6),"Queue: ");
	}$salida.= "</table>";
	return $salida;
}

?>