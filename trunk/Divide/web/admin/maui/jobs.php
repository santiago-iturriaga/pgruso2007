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
	include_once("Maui.php");

	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/jobs/jobs.html");
	$mensaje 	= 	"";
	$error 		= 	"";

	$info = "";
    if (ISSET($_REQUEST["id"])) {
		// Detalle de un trabajo
		$id = $_REQUEST["id"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".CHECKJOB_CMD." -v $id; exit\" 2>&1";
		$checkjob_job = `$command`;

		$tabla = new Tabla("","","../../");
		$tabla->addColumna(0,0,"Informaci&oacute;n del trabajo");
		$tabla->addRenglon(array("<pre>".$checkjob_job."</pre>"));
		$info = $tabla->getTabla();
	} else if (ISSET($_REQUEST["diagnose"])) {
		$id = $_REQUEST["diagnose"];
		$command = SSH." -l ".USERNAME." ".HOST." \"".DIAGNOSE_CMD." -j $id; exit\" 2>&1";
		$diagnose = `$command`;

		$cabezal_diag=array("Name","State","Par","Proc","QOS","WCLimit","R","Min","User","Group","Account","QueuedTime","Network","Opsys","Arch","Mem","Disk","Procs","Class","Features");
		$cabezal_diag_titulos=array("Name","State","Par","Proc","QOS","WCLimit","R","Min","User","Group","Account","QueuedTime","Network","Opsys","Arch","Mem","Disk","Procs","Class","Features");
		list($tabla,$pie) = getTablaDiag($diagnose,$cabezal_diag,$cabezal_diag_titulos);
		$info = $tabla->getTabla()."<br />".$pie;
	} else if (ISSET($_REQUEST["cancel"])) {
		$mensaje = maui_cancelar_trabajo($_REQUEST["cancel"]);
	} else if (ISSET($_REQUEST["hold"])) {
		$mensaje = maui_detener_trabajo($_REQUEST["hold"]);
	} else if (ISSET($_REQUEST["suspend"])) {
		$mensaje = maui_suspender_trabajo($_REQUEST["suspend"]);
	} else if (ISSET($_REQUEST["run"])) {
		$mensaje = maui_ejecutar_trabajo($_REQUEST["run"]);
	} else if (ISSET($_REQUEST["release"])) {
		$mensaje = maui_liberar_trabajo($_REQUEST["release"]);
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
	list($tabla_active,$resumen_active) = getTablaTrabajos($jobs_active,$cabezal_active,$cabezal_titulos_active,0);

	$command = SSH." -l ".USERNAME." ".HOST." \"".JOBS_CMD." -i; exit\" 2>&1";
	$jobs_idle = `$command`;
	$cabezal_idle = array("JobName","Priority","XFactor","Q","User","Group","Procs","WCLimit","Class","SystemQueueTime");
	$cabezal_titulos_idle = array("Nombre","Prioridad","XFactor","Q","Usuario","Grupo","Procesadores","WCLimit","Class","SystemQueueTime");
	list($tabla_idle,$resumen_idle) = getTablaTrabajos($jobs_idle,$cabezal_idle,$cabezal_titulos_idle,1);

	$command = SSH." -l ".USERNAME." ".HOST." \"".JOBS_CMD." -b; exit\" 2>&1";
	$jobs_blocked = `$command`;
	$cabezal_blocked = array("JobName","User","Reason");
	$cabezal_titulos_blocked = array("Nombre","Usuario","Raz&oacute;n");
	list($tabla_blocked,$resumen_blocked) = getTablaTrabajos($jobs_blocked,$cabezal_blocked,$cabezal_titulos_blocked,2);

	if ($mensaje != "") {
		$mensajepl = $plantilla->load("plantillas/mensaje.html");
		$mensajepl = $plantilla->replace($mensajepl, array (
			"MENSAJE" => $mensaje
		));
	} else {
		$mensajepl = "";
	}

	if ($error != "") {
		$errorpl = $plantilla->load("plantillas/error.html");
		$errorpl = $plantilla->replace($errorpl, array (
			"ERROR" => $error
		));
	} else {
		$errorpl = "";
	}

	$ppal	=	$plantilla->replace($ppal,array("TABLA_ACTIVE"=>$tabla_active->getTabla(),
												"TABLA_IDLE"=>$tabla_idle->getTabla(),
												"TABLA_BLOCKED"=>$tabla_blocked->getTabla(),
												"RESUMEN_ACTIVE"=>$resumen_active,
												"RESUMEN_IDLE"=>$resumen_idle,
												"RESUMEN_BLOCKED"=>$resumen_blocked,
												"INFO_TRABAJO"=>$info));

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensajepl,
												"SMENU_JOBS"=>" id='smactual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"ERROR"=>$errorpl));
	$s->salvar();

	echo $base;
?>