<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Momento.php");
	if(isset($_GET["id"])){
		$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
		$momento = new Momento($conexion);
		$error = $_GET["error"]==1;
		$res = $momento->getSalida($_GET["id"],$error);
		if($res["error"]) error_log(print_r($res,1));
		else {
			$nombre_archivo = "output";
			if($error) $nombre_archivo = "error";
			header ("Content-Disposition: attachment; filename=".$nombre_archivo."\n\n");
			header ("Content-Type: application/text");
			header ("Content-Length: ".strlen($res["archivo"]));
			echo $res["archivo"];
			exit;
		}
	}
?>