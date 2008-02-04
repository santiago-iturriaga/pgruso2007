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
	$tabla_active = getTablaTrabajos($jobs_active,$cabezal_active,0);

	$jobs_idle = `ssh -l $username $host "$jobs_cmd -i; exit" 2>&1`;
	$cabezal_idle = array("JobName","Priority","XFactor","Q","User","Group","Procs","WCLimit","Class","SystemQueueTime");
	$tabla_idle = getTablaTrabajos($jobs_idle,$cabezal_idle,1);

	$jobs_blocked = `ssh -l $username $host "$jobs_cmd -b; exit" 2>&1`;
	$cabezal_blocked = array("JobName","User","Reason");
	$tabla_blocked = getTablaTrabajos($jobs_blocked,$cabezal_blocked,2);

	$info = "";
    if (ISSET($_REQUEST["id"])) {
		// Detalle de un trabajo
		$id = $_REQUEST["id"];
		$qstat_job = `ssh -l $username $host "$qstat_cmd -f $id; exit" 2>&1`;

		$info .= "<span style='font-size: 1.5em'>Informacion del trabajo</span>";
		$info .= "<pre>$qstat_job</pre>";
	}

	$ppal	=	$plantilla->replace($ppal,array("TABLA_ACTIVE"=>$tabla_active->getTabla()));
	$ppal	=	$plantilla->replace($ppal,array("TABLA_IDLE"=>$tabla_idle->getTabla()));
	$ppal	=	$plantilla->replace($ppal,array("TABLA_BLOCKED"=>$tabla_blocked->getTabla()));
	$ppal	=	$plantilla->replace($ppal,array("INFO_TRABAJO"=>$info));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>