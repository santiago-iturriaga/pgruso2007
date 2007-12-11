#!/usr/bin/php -q
<?php
	if ($argc != 2 ) {
		echo "Error: Cantidad de parametros incorrecta";
		exit;
	}

	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Momento.php");

	$conexion	= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento 	= new Momento($conexion);
	$res = $momento->setFinalizado($argv[1]);

	if($res["error"]){
		echo "Error: ".print_r($res,1);
		exit;
	}

?>
