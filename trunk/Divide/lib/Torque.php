<?php
/*
 * Created on Feb 19, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include_once("Constantes.php");
include_once("Servidor.php");

function torque_ejecutar_trabajo($id){
	$command = SSH." -l ".USERNAME." ".HOST." \"".QRUN_CMD." $id; exit\" 2>&1";
	$qrun_result = `$command`;

	$mensaje = "";
	$error = "";
	if ($qrun_result=="") {
		$mensaje = "El trabajo fue iniciado.";
	} else {
		$error = "No fue posible iniciar el trabajo (".$qrun_result.")";
		error_log($qrun_result);
	}

	return array($mensaje,$error);
}

function torque_eliminar_trabajo($id){
	$command = SSH." -l ".USERNAME." ".HOST." \"".QDEL_CMD." $id; exit\" 2>&1";
	$qdel_result = `$command`;

	$mensaje = "";
	$error = "";
	if ($qdel_result=="") {
		$mensaje = "El trabajo fue eliminado.";
	} else {
		$error = "No fue posible eliminar el trabajo.<br />".$qdel_result;
		error_log($qdel_result);
	}

	return array($mensaje,$error);
}

function torque_detener_trabajo($id,$held_type){
	$command = SSH." -l ".USERNAME." ".HOST." \"".QHOLD_CMD." -h $held_type $id; exit\" 2>&1";
	$qhold_result = `$command`;
	$mensaje = "";
	$error = "";
	if ($qhold_result=="") {
		$mensaje = "El trabajo fue detenido.";
	} else {
		$error = "No fue posible detener el trabajo.<br />".$qhold_result;
		error_log($qhold_result);
	}

	return array($mensaje,$error);
}

function torque_liberar_trabajo($id,$held_type){
	$command = SSH." -l ".USERNAME." ".HOST." \"".QRLS_CMD." -h $held_type $id; exit\" 2>&1";
	$qrls_result = `$command`;

	$mensaje = "";
	$error = "";
	if ($qrls_result=="") {
		$mensaje = "El trabajo fue reiniciado.";
	} else {
		$error = "No fue posible reiniciar el trabajo.<br />".$qrls_result;
		error_log($qrls_result);
	}

	return array($mensaje,$error);
}

function torque_detener_cola($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".QSTOP_CMD." $id; exit\" 2>&1";
	$qstop_result = `$command`;

	$mensaje = "";
	$error = "";
	if ($qstop_result=="")
		$mensaje = "La cola fue detenida.";
	else {
		$error = "No fue posible detener la cola.<br/>".$qstop_result;
		error_log($qstop_result);
	}

	return array($mensaje,$error);
}

function torque_iniciar_cola($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".QSTART_CMD." $id; exit\" 2>&1";
	$qstart_result = `$command`;

	$mensaje = "";
	$error = "";
	if ($qstart_result=="")
		$mensaje = "La cola fue iniciada.";
	else {
		$error = "No fue posible iniciar la cola.<br/>".$qstart_result;
		error_log($qstart_result);
	}
	return array($mensaje,$error);
}

function torque_iniciar_nodo($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".QNODES_CMD." -c $id; exit\" 2>&1";
	$qnodes_result = `$command`;

	$mensaje = "";
	$error = "";
	if ($qnodes_result=="") {
		$mensaje ="El nodo fue habilitado.";
	} else {
		$error ="No fue posible habilitar el nodo.<br/>".$qnodes_result;
	}
	return array($mensaje,$error);
}

function torque_detener_nodo($id) {
	$command = SSH." -l ".USERNAME." ".HOST." \"".QNODES_CMD." -o $id; exit\" 2>&1";
	$qnodes_result = `$command`;

	$mensaje= "";
	$error = "";
	if ($qnodes_result=="") {
		$mensaje ="El nodo fue deshabilitado.";
	} else {
		$error ="No fue posible deshabilitar el nodo.<br/>".$qnodes_result;
	}
	return array($mensaje,$error);
}

function torque_tracejob($id,$dias) {
	$resultado = ejecutar_servidor(TRACEJOB." -n $dias $id");

	print_r($resultado);

	$resultado = array_pop(explode("Job: $id",$resultado));
	if (ereg("walltime=([^\n]*)",$resultado,$macheo)) {
		return array("error"=>0,"tiempo"=>$macheo[1],"log"=>$resultado);
	} else {
		return array("error"=>1,"log"=>$resultado);
	}
}
?>