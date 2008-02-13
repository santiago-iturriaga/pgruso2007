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
		$command = SSH." -l ".USERNAME." ".HOST." \"".CHECKJOB_CMD." -v $id; exit\" 2>&1";
		$checkjob_job = `$command`;

		$tabla = new Tabla("","","../../");
		$tabla->addColumna(0,0,"Informacion del trabajo");
		$tabla->addRenglon(array("<pre>".$checkjob_job."</pre>"));
		$info = $tabla->getTabla();
	} else if (ISSET($_REQUEST["diagnose"])) {
		$id = $_REQUEST["diagnose"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".DIAGNOSE_CMD." -j $id; exit\" 2>&1";
		$diagnose = `$command`;

		$cabezal_diag=array("Name","State","Par","Proc","QOS","WCLimit","R","Min","User","Group","Account","QueuedTime","Network","Opsys","Arch","Mem","Disk","Procs","Class Features");
		$cabezal_diag_titulos=array("Name","State","Par","Proc","QOS","WCLimit","R","Min","User","Group","Account","QueuedTime","Network","Opsys","Arch","Mem","Disk","Procs","Class Features");
		$info = getTablaDiag($diagnose,$cabezal_diag,$cabezal_diag_titulos,false)->getTabla();
	} else if (ISSET($_REQUEST["cancel"])) {
		$id = $_REQUEST["cancel"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".CANCELJOB_CMD." $id; exit\" 2>&1";
		$canceljob = `$command`;

		if ($canceljob == "") {
			$mensaje = "Trabajo cancelado.";
		} else {
			$mensaje = $canceljob;
		}
	} else if (ISSET($_REQUEST["hold"])) {
		$id = $_REQUEST["hold"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".SETHOLD_CMD." $id; exit\" 2>&1";
		$sethold = `$command`;

		if ($sethold == "") {
			$mensaje = "Trabajo detenido.";
		} else {
			$mensaje = $sethold;
		}
	} else if (ISSET($_REQUEST["suspend"])) {
		$id = $_REQUEST["suspend"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".RUNJOB_CMD." -s $id; exit\" 2>&1";
		echo $command;
		$runjob = `$command`;

		if ($runjob == "") {
			$mensaje = "Trabajo suspendido.";
		} else {
			$mensaje = $runjob;
		}
	} else if (ISSET($_REQUEST["run"])) {
		$id = $_REQUEST["run"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".RUNJOB_CMD." $id; exit\" 2>&1";
		$runjob = `$command`;

		if ($runjob == "") {
			$mensaje = "Trabajo iniciado.";
		} else {
			$mensaje = $runjob;
		}
	} else if (ISSET($_REQUEST["release"])) {
		$id = $_REQUEST["release"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".RELEASEHOLD_CMD." $id; exit\" 2>&1";
		$releasehold = `$command`;

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
	$command = SSH." -l ".USERNAME." ".HOST." \"".JOBS_CMD." -r; exit\" 2>&1";
	$jobs_active = `$command`;
	$cabezal_active = array("JobName","S Par","Effic","XFactor","Q","User","Group","MHost","Procs","Remaining","StartTime");
	$cabezal_titulos_active = array("Nombre","S Par","Eficiencia","XFactor","Q","Usuario","Grupo","Nodo madre","Procesadores","Restante","Inicio");
	$tabla_active = getTablaTrabajos($jobs_active,$cabezal_active,$cabezal_titulos_active,0);

	$command = SSH." -l ".USERNAME." ".HOST." \"".JOBS_CMD." -i; exit\" 2>&1";
	$jobs_idle = `$command`;
	$cabezal_idle = array("JobName","Priority","XFactor","Q","User","Group","Procs","WCLimit","Class","SystemQueueTime");
	$cabezal_titulos_idle = array("Nombre","Prioridad","XFactor","Q","Usuario","Grupo","Procesadores","WCLimit","Class","SystemQueueTime");
	$tabla_idle = getTablaTrabajos($jobs_idle,$cabezal_idle,$cabezal_titulos_idle,1);

	$command = SSH." -l ".USERNAME." ".HOST." \"".JOBS_CMD." -b; exit\" 2>&1";
	$jobs_blocked = `$command`;
	$cabezal_blocked = array("JobName","User","Reason");
	$cabezal_titulos_blocked = array("Nombre","Usuario","Razon");
	$tabla_blocked = getTablaTrabajos($jobs_blocked,$cabezal_blocked,$cabezal_titulos_blocked,2);

	$ppal	=	$plantilla->replace($ppal,array("TABLA_ACTIVE"=>$tabla_active->getTabla()));
	$ppal	=	$plantilla->replace($ppal,array("TABLA_IDLE"=>$tabla_idle->getTabla()));
	$ppal	=	$plantilla->replace($ppal,array("TABLA_BLOCKED"=>$tabla_blocked->getTabla()));
	$ppal	=	$plantilla->replace($ppal,array("INFO_TRABAJO"=>$info));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"SMENU_JOBS"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>