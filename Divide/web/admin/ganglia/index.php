<?php
/*
 * Created on Feb 9, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 /*
  * http://localhost:8888/ganglia/graph.php?g=load_report&c=arca
  * http://localhost:8888/ganglia/graph.php?g=cpu_report&c=arca
  * http://localhost:8888/ganglia/graph.php?g=mem_report&c=arca
  * http://localhost:8888/ganglia/graph.php?g=network_report&c=arca
  */
?>
<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');

	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/ganglia.html");
	$mensaje = "";
	$error = "";

	$url = GANGLIA_URL;
	$url_carga = GANGLIA_URL."/graph.php?g=load_report&c=arca";
	$url_cpu = GANGLIA_URL."/graph.php?g=cpu_report&c=arca";
	$url_mem = GANGLIA_URL."/graph.php?g=mem_report&c=arca";
  	$url_network = GANGLIA_URL."/graph.php?g=network_report&c=arca";

	$ppal	=	$plantilla->replace($ppal,array("GANGLIA_URL"=>$url,
												"LOAD"=>$url_carga,
												"CPU"=>$url_cpu,
												"MEM"=>$url_mem,
												"NET"=>$url_network));

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>

