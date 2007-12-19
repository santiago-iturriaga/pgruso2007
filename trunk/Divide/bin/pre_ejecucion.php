#!/usr/bin/php -q
<?
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Momento.php");

	if ($argc != 2 ) {
		echo "Error: Cantidad de parametros incorrecta";
		exit;
	}

	list($jobID, $maquina) = split("\.",$argv[1],2);
	$conexion	= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento 	= new Momento($conexion);
	$res = $momento->setIniciado($jobID);
error_log("PHP!!!!".$jobId);
	echo "[pre_ejecucion] Job ID: ".$jobID."\n";
	/* echo "Maquina: ".$maquina."\n"; */
?>

