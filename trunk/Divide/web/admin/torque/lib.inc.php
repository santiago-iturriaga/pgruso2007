<?
set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');

	include_once("Tabla/Tabla.php");

function linea_vacia($val) {
	return ($val != "");
}

function parsear_tabla_queues($tabla) {
	$salida	= "";
	$tabla_aux = stristr($tabla,"Queue: ");
	while (strlen($tabla_aux) > 0) {

		$nombre_array = explode("\n", $tabla_aux,2);
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


		$salida.= "<table border=1>";
		$salida.= "<tr><td>";;
		$salida.= "<b>Queue: </b>$nombre&nbsp;";
		if ($started=="F") {
			$salida.= "<a href='queue_start.php?id=$nombre'>[Start]</a>";
		} else if ($started=="T") {
			$salida.= "<a href='queue_stop.php?id=$nombre'>[Stop]</a>";
		} else {
			$salida.= "[No disponible]";
		}
		$salida.= "</td></tr>";
		$salida.= "<tr><td>";
		$salida.= "<pre>$datos</pre>";
		$salida.= "</td></tr>";
		$salida.= "</table>";

		//Siguiente queue
		$tabla_aux = stristr(substr($tabla_aux,6),"Queue: ");
	}
	return $salida;
}


function parsear_tabla_una_fila($tabla,$cabezal,$tienepie) {
	$tabla_lineas = explode("\n", $tabla);
	$tabla_lineas = array_values(array_filter($tabla_lineas,"linea_vacia"));
	$tabla_lineas_cant = sizeof($tabla_lineas);

	if ($tienepie) {
		// Tiene pie de tabla
		$cant_min_lineas = 2;
		$tabla_lineas_pie = $tabla_lineas_cant-1;
	} else {
		// No tiene pie de tabla
		$cant_min_lineas = 1;
		$tabla_lineas_pie = $tabla_lineas_cant;
	}

	// Cabecera
	if ($tabla_lineas_cant > $cant_min_lineas) {
		$cabezal_fin_pos = array();
		foreach($cabezal as $key => $value) {
			$inicio_cabezal = stripos($tabla_lineas[0],$cabezal[$key]);
			$fin_cabezal = $inicio_cabezal+strlen($cabezal[$key]);
			$cabezal_fin_pos[$key] = $fin_cabezal-1;
		}

		print("<table border=1>");
		for ($linea = 1; $linea < $tabla_lineas_pie; $linea++) {
			foreach($cabezal as $key => $value) {
				print("<tr>");
				print("<td>$value</td>");
				if ($key == 0) {
					$inicio_td = 0;
					$id = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
					print("<td><a href='job_status.php?id=$id'>$id</a></td>");
				} else {
					$inicio_td = $cabezal_fin_pos[$key-1]+1;
					print("<td>".trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1))."</td>");
				}
				print("</tr>");
			}
		}
		print("</table>");
	} else {
		print("<pre>Empty.</pre>");
	}

	// Pie
	if ($tienepie) {
		print("$tabla_lineas[$tabla_lineas_pie]");
	}
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
							$valor="Held";
							break;
						case "Q":
							$valor="Queued";
							break;
						case "R":
							$valor="Running";
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
		$botones = "<a href='jobs.php?eliminar=$job_id'>Eliminar</a>";
        switch ($job_status) {
        	case "H":
            	$botones .= "<a href='jobs.php?reiniciar=$job_id'>Reiniciar</a>";
                break;
            default:
            	$botones	.= "<a href='jobs.php?detener=$job_id'>Detener</a>";
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


		$salida.= "<table border=1>";
		$salida.= "<tr><td>";
		$salida.= "<b>Cola: </b>$nombre&nbsp;";
		if ($started=="F") {
			$salida.= "<a href='colas.php?iniciar=$nombre'>[Iniciar]</a>";
		} else if ($started=="T") {
			$salida.= "<a href='colas.php?detener=$nombre'>[Detener]</a>";
		} else {
			$salida.= "[No disponible]";
		}
		$salida.= "</td></tr>";
		$salida.= "<tr><td>";
		$salida.= "<pre>$datos</pre>";
		$salida.= "</td></tr>";
		$salida.= "</table>";

		//Siguiente queue
		$cadena = stristr(substr($cadena,6),"Queue: ");
	}
	return $salida;
}


?>