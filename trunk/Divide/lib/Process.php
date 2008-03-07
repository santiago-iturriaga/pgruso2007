<?php
/*
 * Created on Mar 7, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<?
	include_once("Servidor.php");

	function estado_procesos() {
		// ps -eo pid,uname,cmd
		$result = ejecutar_servidor("ps -eo pid,uname,cmd");

		$lineas = explode("\n",$result);
		$qnoded = preg_grep("(qnoded|pbs_mom)",$lineas);
		$qserverd = preg_grep("(qserverd|pbs_server)",$lineas);
		$maui = preg_grep("(maui)",$lineas);

		//print_r($qnoded);
		//print_r($qserverd);
		//print_r($maui);

		return array("node"=>$qnoded,"server"=>$qserverd,"scheduler"=>$maui);
	}

	function parsear_proceso($lineaPS) {
		$columnas = explode(" ",$lineaPS);
		$pid = array_shift($columnas);
		$uname = array_shift($columnas);
		$cmd = implode(" ",$columnas);

		return array("pid" => $pid, "uname" => $uname, "cmd" => $cmd);
	}
?>