<?php
include_once("Constantes.php");

function ejecutar_servidor($comando,$usuario=USERNAME){
	$command = SSH." -l ".$usuario." ".HOST." \"".$comando."; exit\" 2>&1";
	$res = `$command`;
	error_log("COMANDO: ".$command);
	error_log("SALIDA: ".$res);
	return $res;
}
?>
