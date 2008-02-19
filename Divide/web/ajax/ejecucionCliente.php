<?
	if(!isset($_GET["accion"])){
		echo "ERROR1";
		exit;
	}
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Momento.php");
	include_once("Torque.php");
	$s = new Sesion();
	$conexion	= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento 	= new Momento($conexion);

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		echo "ERROR2";
		exit;
	}

	$res = $momento->getEnEjecucion($s->sesion->ejecucion_actual);
	if($res["error"] or !isset($res["salida"][$s->sesion->ejecucion_actual]["id_torque"])) {
		echo "ERROR3";
		exit;
	}

	$id_torque = $res["salida"][$s->sesion->ejecucion_actual]["id_torque"];
	if($_GET["accion"]=='Detener'){
		$res=  torque_eliminar_trabajo($id_torque);
		echo $res;
		exit;
	}
	echo "ERROR";
?>