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
	$leido = "";
	if(filesize  ($archivo) > $ini){
		$s->sesion->ultima_fecha	= $nueva_fecha;

		$leido	=  file_get_contents  ( $archivo  , true, null,$ini);
		$s->sesion->bytes_leidos += strlen($leido);
	}
	$s->salvar();
	echo $leido
?>