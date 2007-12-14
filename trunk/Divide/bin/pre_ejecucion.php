#!/usr/bin/php -q
<?
	if ($argc != 2 ) {
		echo "Error: Cantidad de parametros incorrecta";
		exit;
	}

	list($jobID, $maquina) = split("\.",$argv[1],2);
	$conexion	= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento 	= new Momento($conexion);
	$res = $momento->setIniciado($jobID);

	echo "[pre_ejecucion] Job ID: ".$jobID."\n";
	/* echo "Maquina: ".$maquina."\n"; */
?>

