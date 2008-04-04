<?
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Momento.php");
	$s = new Sesion();

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		exit;
	}
	$conexion	= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento 	= new Momento($conexion);
	$res = $momento->getEstadoEjecucion($s->sesion->ejecucion_actual);
	if($res["error"]) error_log(print_r($res,1));
	else{
		echo $res["salida"];
	}

?>