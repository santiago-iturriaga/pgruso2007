<?
set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');

include_once("Tabla/Tabla.php");

function linea_vacia($val) {
	return ($val != "");
}

//// $tipo puede ser:
function getTablaDiag($tabla_string,$cabezal,$cabezal_titulos) {
	$tabla = new Tabla("","","../../");
	$tabla->addColumna(0,0,"Diagnostico");
	$tabla->addColumna(1,1,"");

	$tabla_lineas = explode("\n", $tabla_string);
	$tabla_lineas = array_values(array_filter($tabla_lineas,"linea_vacia"));
	$tabla_lineas_cant = sizeof($tabla_lineas);

	$pie = "";

	// Cabecera
	if ($tabla_lineas_cant > 1) {
		$cabezal_fin_pos = array();

		foreach($cabezal as $key => $value) {
			$inicio_cabezal = stripos($tabla_lineas[0],$cabezal[$key]);
			$fin_cabezal = $inicio_cabezal+strlen($cabezal[$key]);
			$cabezal_fin_pos[$key] = $fin_cabezal-1;
		}

		$linea = 1;
		foreach($cabezal as $key => $value) {
			$valor = "";
			if ($key == 0) {
				$inicio_td = 0;
				$valor = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
				$valor = "<a href='jobs.php?id=$valor'>$valor</a></td>";
			} else {
				$inicio_td = $cabezal_fin_pos[$key-1]+1;
				$valor = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
			}

			$tabla->addRenglon(array($cabezal_titulos[$key],$valor));
		}

		// Pie
		array_splice($tabla_lineas,0,2);
		$pie = implode("<br />",$tabla_lineas);
	}

	return array($tabla,$pie);
}

//// $tipo puede ser:
////		active = 0, idle = 1, blocked = 2, diagnose = 3
function getTablaTrabajos($tabla_string,$cabezal,$cabezal_titulos,$tipo) {
	$tabla = new Tabla("","","../../");
	$tabla_lineas = explode("\n", $tabla_string);
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
			$tabla->addColumna($key,$key,$cabezal_titulos[$key]);
		}
		$tabla->addColumna(count($cabezal),count($cabezal),"");
		$status_column = 4;

		for ($linea = 1; $linea < $tabla_lineas_pie; $linea++) {
			$renglon = array();
			$id = "";

			foreach($cabezal as $key => $value) {
				$valor = "";
				if ($key == 0) {
					$inicio_td = 0;
					$valor = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
					$id = $valor;
					$valor = "<a href='jobs.php?id=$valor'>$valor</a></td>";
				} else {
					$inicio_td = $cabezal_fin_pos[$key-1]+1;
					$valor = trim(substr($tabla_lineas[$linea],$inicio_td,$cabezal_fin_pos[$key]-$inicio_td+1));
				}

				$renglon[$key] = $valor;
			}

			$botones = "";
			switch ($tipo) {
				case 0:
					// Active
					$botones .= "<a href='jobs.php?diagnose=$id'><img src='../../imagenes/information.png' title='Diagnostico'/></a>";
					$botones .= "<a href='jobs.php?cancel=$id'><img src='../../imagenes/decline.png' title='Cancelar'/></a>";
					$botones .= "<a href='jobs.php?hold=$id'><img src='../../imagenes/pause_green.png' title='Detener'/></a>";
					$botones .= "<a href='jobs.php?suspend=$id'><img src='../../imagenes/stop.png' title='Suspender'/></a>";

					break;
				case 1:
					// Idle
					$botones .= "<a href='jobs.php?diagnose=$id'><img src='../../imagenes/information.png' title='Diagnostico'/></a>";
					$botones .= "<a href='jobsl.php?cancel=$id'><img src='../../imagenes/decline.png' title='Cancelar'/></a>";
					$botones .= "<a href='jobs.php?hold=$id'><img src='../../imagenes/pause_green.png' title='Detener'/></a>";
					$botones .= "<a href='jobs.php?run=$id'><img src='../../imagenes/play_green.png' title='Ejecutar'/></a>";

					break;
				case 2:
					// Blocked
					$botones .= "<a href='jobs.php?diagnose=$id'><img src='../../imagenes/information.png' title='Diagnostico'/></a>";
					$botones .= "<a href='jobs.php?cancel=$id'><img src='../../imagenes/decline.png' title='Cancelar'/></a>";
					$botones .= "<a href='jobs.php?release=$id'><img src='../../imagenes/play_green.png' title='Liberar'/></a>";

					break;
				default:
					// None
					break;
			}
			$renglon[count($cabezal)] = $botones;

			$tabla->addRenglon($renglon);
		}
	}

	// Pie
	$pie = "";
	if ($tipo < 2) {
		$pie = $tabla_lineas[$tabla_lineas_pie];
	}

	return array($tabla,$pie);
}
?>