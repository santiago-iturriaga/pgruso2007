<?php
/*
 * Created on Feb 19, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include_once("Constantes.php");

function maui_cancelar_trabajo($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".CANCELJOB_CMD." $id; exit\" 2>&1";
	$canceljob = `$command`;

	$mensaje="";
	if ($canceljob == "") {
		$mensaje = "Trabajo cancelado.";
	} else {
		$mensaje = $canceljob;
	}
	return $mensaje;
}

function maui_detener_trabajo($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".SETHOLD_CMD." $id; exit\" 2>&1";
	$sethold = `$command`;

	$mensaje = "";
	if ($sethold == "") {
		$mensaje = "Trabajo detenido.";
	} else {
		$mensaje = $sethold;
	}

	return $mensaje;
}

function maui_suspender_trabajo($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".RUNJOB_CMD." -s $id; exit\" 2>&1";
	$runjob = `$command`;

	$mensaje = "";
	if ($runjob == "") {
		$mensaje = "Trabajo suspendido.";
	} else {
		$mensaje = $runjob;
	}

	return $mensaje;
}

function maui_ejecutar_trabajo($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".RUNJOB_CMD." $id; exit\" 2>&1";
	$runjob = `$command`;

	$mensaje = "";
	if ($runjob == "") {
		$mensaje = "Trabajo iniciado.";
	} else {
		$mensaje = $runjob;
	}

	return $mensaje;
}

function maui_liberar_trabajo($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".RELEASEHOLD_CMD." $id; exit\" 2>&1";
	$releasehold = `$command`;

	$mensaje = "";
	if ($releasehold == "") {
		$mensaje = "Trabajo liberado.";
	} else {
		$mensaje = $releasehold;
	}

	return $mensaje;
}

?>
