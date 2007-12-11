<?
set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("Sesion.php");
	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado())
		exit;
	$s->sesion->archivo_actual = '/home/nosotros/ejemplo';
	$s->sesion->ultima_fecha = null;
	$s->sesion->bytes_leidos = 0;
	$s->salvar();
?>