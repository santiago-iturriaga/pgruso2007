<?
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("Sesion.php");
	include_once("Servidor.php");
	$s = new Sesion();

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		exit;
	}
	$archivo = $s->sesion->archivo_actual;
	$ini = $s->sesion->bytes_leidos;
	//error_log("INI:".$ini);
	$leido = "";
	//error_log("TAM:".filesize  ($archivo));

	// filesize:
	$salida = ejecutar_servidor("wc -c $archivo");
	$filesize = array_shift(explode(" ",$salida));
	if(!is_numeric($filesize)){ error_log($salida);exit;}
	error_log("wc:".$salida);
	error_log("tamanio:".$filesize);

	if($filesize > $ini){
		$leido	=  file_get_contents  ( $archivo  , false, null,$ini);
		$s->sesion->bytes_leidos += strlen($leido);
	}
	$s->salvar();
	echo $leido;

?>