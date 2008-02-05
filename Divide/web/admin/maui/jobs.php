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

	$info = "";
    if (ISSET($_REQUEST["id"])) {
		// Detalle de un trabajo
		$id = $_REQUEST["id"];
		$checkjob_job = `ssh -l $username $host "$checkjob_cmd -v $id; exit" 2>&1`;

		$tabla = new Tabla("","","../../");
		$tabla->addColumna(0,0,"Informacion del trabajo");
		$tabla->addRenglon(array("<pre>".$checkjob_job."</pre>"));
		$info = $tabla->getTabla();
	} else if (ISSET($_REQUEST["diagnose"])) {
		$id = $_REQUEST["diagnose"];
		$diagnose = `ssh -l $username $host "$diagnose_cmd -j $id; exit" 2>&1`;
		$cabezal_diag=array("Name","State","Par","Proc","QOS","WCLimit","R","Min","User","Group","Account","QueuedTime","Network","Opsys","Arch","Mem","Disk","Procs","Class Features");
		$cabezal_diag_titulos=array("Name","State","Par","Proc","QOS","WCLimit","R","Min","User","Group","Account","QueuedTime","Network","Opsys","Arch","Mem","Disk","Procs","Class Features");
		$mensaje = getTablaDiag($diagnose,$cabezal_diag,$cabezal_diag_titulos,false)->getTabla();
	} else if (ISSET($_REQUEST["cancel"])) {
		$id = $_REQUEST["cancel"];
		$canceljob = `ssh -l $username $host "$canceljob_cmd $id; exit" 2>&1`;
		if ($canceljob == "") {
			$mensaje = "Trabajo cancelado.";
		} else {
			$mensaje = $canceljob;
		}
	} else if (ISSET($_REQUEST["hold"])) {
		$id = $_REQUEST["hold"];
		$sethold = `ssh -l $username $host "$sethold_cmd $id; exit" 2>&1`;
		if ($sethold == "") {
			$mensaje = "Trabajo detenido.";
		} else {
			$mensaje = $sethold;
		}
	} else if (ISSET($_REQUEST["suspend"])) {
		$id = $_REQUEST["suspend"];
		$runjob = `ssh -l $username $host "$runjob_cmd -s $id; exit" 2>&1`;
		if ($runjob == "") {
			$mensaje = "Trabajo suspendido.";
		} else {
			$mensaje = $runjob;
		}
	} else if (ISSET($_REQUEST["run"])) {
		$id = $_REQUEST["run"];
		$runjob = `ssh -l $username $host "$runjob_cmd $id; exit" 2>&1`;
		if ($runjob == "") {
			$mensaje = "Trabajo iniciado.";
		} else {
			$mensaje = $runjob;
		}
	} else if (ISSET($_REQUEST["release"])) {
		$id = $_REQUEST["release"];
		$releasehold = `ssh -l $username $host "$releasehold_cmd $id; exit" 2>&1`;
		if ($releasehold == "") {
			$mensaje = "Trabajo liberado.";
		} else {
			$mensaje = $releasehold;
		}
	}

	/*
	-b // BLOCKED QUEUE
	-i // IDLE QUEUE
	-r // ACTIVE QUEUE
	*/
	$jobs_active = `ssh -l $username $host "$jobs_cmd -r; exit" 2>&1`;
	$cabezal_active = array("JobName","S Par","Effic","XFactor","Q","User","Group","MHost","Procs","Remaining","StartTime");
	$cabezal_titulos_active = array("Nombre","S Par","Eficiencia","XFactor","Q","Usuario","Grupo","Nodo madre","Procesadores","Restante","Inicio");
	$tabla_active = getTablaTrabajos($jobs_active,$cabezal_active,$cabezal_titulos_active,0);

	$jobs_idle = `ssh -l $username $host "$jobs_cmd -i; exit" 2>&1`;
	$cabezal_idle = array("JobName","Priority","XFactor","Q","User","Group","Procs","WCLimit","Class","SystemQueueTime");
	$cabezal_titulos_idle = array("Nombre","Prioridad","XFactor","Q","Usuario","Grupo","Procesadores","WCLimit","Class","SystemQueueTime");
	$tabla_idle = getTablaTrabajos($jobs_idle,$cabezal_idle,$cabezal_titulos_idle,1);

	$jobs_blocked = `ssh -l $username $host "$jobs_cmd -b; exit" 2>&1`;
	$cabezal_blocked = array("JobName","User","Reason");
	$cabezal_titulos_blocked = array("Nombre","Usuario","Razon");
	$tabla_blocked = getTablaTrabajos($jobs_blocked,$cabezal_blocked,$cabezal_titulos_blocked,2);

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