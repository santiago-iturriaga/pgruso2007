<?php
include_once("Constantes.php");

function ejecutar_servidor($comando,$usuario=USERNAME){
	$command = SSH." -l ".$usuario." ".HOST." \"".$comando."; exit\" 2>&1";
	$res = `$command`;
	return $res;
}

	function estado_procesos() {
		// ps -eo pid,uname,cmd
		$result = ejecutar_servidor("ps -eo pid,uname,cmd");

		$lineas = explode("\n",$result);
		$qnoded = preg_grep("(qnoded|pbs_mom)",$lineas);
		$qserverd = preg_grep("(qserverd|pbs_server)",$lineas);
		$maui = preg_grep("(maui)",$lineas);

		return array("node"=>$qnoded,"server"=>$qserverd,"scheduler"=>$maui);
	}

	function parsear_proceso($lineaPS) {
		$columnas = explode(" ",trim($lineaPS));
		$pid = array_shift($columnas);
		$uname = array_shift($columnas);
		$cmd = implode(" ",$columnas);

		return array("pid" => $pid, "uname" => $uname, "cmd" => $cmd);
	}
?>
