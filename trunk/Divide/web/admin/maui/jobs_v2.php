<?php
/*
 * Created on Feb 4, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	include_once("const.inc.php");
	include_once("lib.inc.php");

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/jobs/jobs.html");
	$mensaje = "";
	$error = "";

	// Listado de todos los trabajos
    /* $qstat = `ssh -l $username $host "$qstat_cmd; exit" 2>&1`;

	if ($qstat == "") {
		$pagina	.= "<pre>Empty.</pre>";
	} else {
		$tabla = getTablaTrabajos($qstat);
		$pagina =  $tabla->getTabla();
	} */

	/*
	-b // BLOCKED QUEUE
	-i // IDLE QUEUE
	-r // ACTIVE QUEUE
	*/
	$jobs_active = `ssh -l $username $host "$jobs_cmd -r; exit" 2>&1`;
	$cabezal_active = array("JobName","S Par","Effic","XFactor","Q","User","Group","MHost","Procs","Remaining","StartTime");
	parsear_tabla($jobs_active,$cabezal_active,0);

	$jobs_idle = `ssh -l $username $host "$jobs_cmd -i; exit" 2>&1`;
	$cabezal_idle = array("JobName","Priority","XFactor","Q","User","Group","Procs","WCLimit","Class","SystemQueueTime");
	parsear_tabla($jobs_idle,$cabezal_idle,1);

	$jobs_blocked = `ssh -l $username $host "$jobs_cmd -b; exit" 2>&1`;
	$cabezal_blocked = array("JobName","User","Reason");
	parsear_tabla($jobs_blocked,$cabezal_blocked,2);

    /* if (ISSET($_REQUEST["id"])) {
		// Detalle de un trabajo
		$id = $_REQUEST["id"];
		$qstat_job = `ssh -l $username $host "$qstat_cmd -f $id; exit" 2>&1`;
		$pagina.="<span style='font-size: 2.0em'>Job status</span>&nbsp;".
			"<span style='font-size: 0.7em'>[<a href='job_status.php'>volver</a>]</span>";
		$pagina.="<pre>$qstat_job</pre>";
	} */

	$ppal	=	$plantilla->replace($ppal,array("PAGINA"=>$pagina));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>