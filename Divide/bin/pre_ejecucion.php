#!/usr/bin/php -q
<? 
	if ($argc != 2 ) {
		echo "Error: Cantidad de parametros incorrecta";
		exit;
	}

	list($jobID, $maquina) = split("\.",$argv[1],2);

	echo "[pre_ejecucion] Job ID: ".$jobID."\n";
	/* echo "Maquina: ".$maquina."\n"; */
?>

