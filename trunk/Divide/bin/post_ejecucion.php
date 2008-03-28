#!/usr/bin/php5 -q
<?php
error_log("post ejecucion");
	if ($argc != 3 ) {
		echo "Error: Cantidad de parametros incorrecta";
		exit;
	}

	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Momento.php");

	list($jobID, $maquina) = split("\.",$argv[1],2);
	$recursos = $argv[2];
	echo "[post_ejecucion] Job ID: ".$jobID."\n";
	/* echo "Maquina: ".$maquina."\n"; */

	$conexion	= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento 	= new Momento($conexion);
	$res = $momento->setFinalizado($jobID,$recursos);

	if($res["error"]){
		echo "Error: ".print_r($res,1);
		exit;
	}

?>