<?
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("Sesion.php");
	$s = new Sesion();

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		exit;
	}
	$archivo = $s->sesion->archivo_actual;
	$ini = $s->sesion->bytes_leidos;
	error_log("INI:".$ini);
	$leido = "";
	if(filesize  ($archivo) > $ini){
		error_log("cambiar esto cuando este php 5.1 (acordarse de cambiar tambien en javascript)");
		//$leido	=  file_get_contents  ( $archivo  , false, null,$ini);
		$leido	=  file_get_contents  ( $archivo  , false);
		$s->sesion->bytes_leidos += strlen($leido);
	}
	$s->salvar();
	error_log($leido,3,$archivo."coso");
	echo $leido;

?>