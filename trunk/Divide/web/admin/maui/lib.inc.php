<?
set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');

include_once("Tabla/Tabla.php");

function linea_vacia($val) {
	return ($val != "");
}

function getTablaTrabajos($tabla,$cabezal,$tipo) {
	$tabla = new Tabla("","","../../");
	$tabla_lineas = explode("\n", $tabla);
	$tabla_lineas = array_values(array_filter($tabla_lineas,"linea_vacia"));
	$tabla_lineas_cant = sizeof($tabla_lineas);

	$cant_min_lineas = 0;
	if ($tipo < 2) {
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
			$tabla->addColumna($key,$value);
		}
		$status_column = 4;

		for ($linea = 1; $linea < $tabla_lineas_pie; $linea++) {
			$renglon = array();
			$valor = "";

			foreach($cabezal as $key => $value) {
				if ($key == 0) {
					$inicio_td = 0;
					$valor = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
				} else {
					$inicio_td = $cabezal_fin_pos[$key-1]+1;
					$valor = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
				}
			}

			if ($key == 0) {
				$valor	= "<a href='jobs.php?id=$valor'>$valor</a></td>";
			}

			$renglon[$columna] = $valor;
		}

		$tabla->addRenglon($renglon);
	}

}

// $tipo puede ser:
//		active = 0, idle = 1, blocked = 2
function parsear_tabla($tabla,$cabezal,$tipo) {
	$tabla_lineas = explode("\n", $tabla);
	$tabla_lineas = array_values(array_filter($tabla_lineas,"linea_vacia"));
	$tabla_lineas_cant = sizeof($tabla_lineas);

	if ($tipo < 2) {
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
		print("<table border=1>");
		print("<tr>");
		$cabezal_fin_pos = array();
		foreach($cabezal as $key => $value) {
			print("<td style='font-weight: bold'>$value");
			$inicio_cabezal = stripos($tabla_lineas[0],$cabezal[$key]);
			$fin_cabezal = $inicio_cabezal+strlen($cabezal[$key]);
			$cabezal_fin_pos[$key] = $fin_cabezal-1;
			print("</td>");
		}
		print("</tr>");

		// Trabajos
		for ($linea = 1; $linea < $tabla_lineas_pie; $linea++) {
			print("<tr>");
			$id = "";
			foreach($cabezal as $key => $value) {
				if ($key == 0) {
					$inicio_td = 0;
					$id = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
					print("<td><a href='job_status.php?id=$id'>$id</a></td>");
				} else {
					$inicio_td = $cabezal_fin_pos[$key-1]+1;
					print("<td>".trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1))."</td>");
				}
			}

			switch ($tipo) {
				case 0:
					// Active
					print("<td><a href='job_diagnose.php?id=$id'>[Diag]</a></td>");
					print("<td><a href='job_cancel.php?id=$id'>[Cancel]</a></td>");
					print("<td><a href='job_hold.php?id=$id'>[Hold]</a></td>");
					print("<td><a href='job_run.php?id=$id&suspend'>[Suspend]</a></td>");

					break;
				case 1:
					// Idle
					print("<td><a href='job_diagnose.php?id=$id'>[Diag]</a></td>");
					print("<td><a href='job_cancel.php?id=$id'>[Cancel]</a></td>");
					print("<td><a href='job_hold.php?id=$id'>[Hold]</a></td>");
					print("<td><a href='job_run.php?id=$id'>[Run]</a></td>");

					break;
				case 2:
					// Blocked
					print("<td><a href='job_diagnose.php?id=$id'>[Diag]</a></td>");
					print("<td><a href='job_cancel.php?id=$id'>[Cancel]</a></td>");
					print("<td><a href='job_release_hold.php?id=$id'>[Release]</a></td>");

					break;
				default:
					// None
					break;
			}
			print("</tr>");
		}
		print("</table>");
	} else {
		print("<pre>Empty.</pre>");
	}

	// Pie
	if ($tipo < 2) {
		print("$tabla_lineas[$tabla_lineas_pie]");
	}
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
?>