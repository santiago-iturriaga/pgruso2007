<?php
include_once("Constantes.php");

function ejecutar_servidor($comando,$usuario=USERNAME){
	$command = SSH." -l ".$usuario." ".HOST." \"".$comando."; exit\" 2>&1";
	$res = `$command`;
	return $res;
}
?>