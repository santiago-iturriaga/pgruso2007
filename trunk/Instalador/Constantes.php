<?php
define("RAIZ", "/home/fenton/repositorio");
define("RAIZ_SISTEMA", "/home/fenton/repositorio/sistema");
define("TMP", "/tmp");
define("CONEXION_HOST", "192.168.0.1");
define("CONEXION_PORT", "5433");
define("CONEXION_USUARIO", "fentondb");
define("CONEXION_PASSWORD", "fentondb");
define("CONEXION_BASE", "fentondb");
define("QSUB", "/usr/local/torque-2.3.0/bin/qsub");
define("MPIEXEC", "/usr/local/openmpi-1.2.6/bin/mpiexec");
define("GRUPOFENTON","fenton");

define("REDIRECCION_SALIDA", "/home/fenton/web/bin/redireccion_salida.php");
define("OUTPUT", "salida_extra");
define("EJECUTABLE","plantillas/archivos/qsub.script");
define("LOG_EJECUCIONES","../log/ejecuciones.log");
define("TIEMPO_REFRESH_RESULTADOS","5");

define("COMANDOS_EJECUCION","make=make&mpicc=/usr/local/openmpi-1.2.6/bin/mpicc");

function ObtenerCOMANDOS_EJECUCION() {
	$lista = array();

	$listaPares = split("&",COMANDOS_EJECUCION);
	foreach ($listaPares as $parEjecucion) {
		list($desc,$path) = split("=",$parEjecucion);
		$lista[$desc]=$path;
	}

	return $lista;
}

// =======================================================
// GANGLIA
// =======================================================
define("GANGLIA_URL","http://192.168.0.1/ganglia");

// =======================================================
// SSH
// =======================================================
define("SSH","ssh");

define("USERNAME","fenton");
define("HOST","localhost");

// =======================================================
// TORQUE / MAUI
// =======================================================
define("PATH_TORQUE","/usr/local/torque-2.3.0");
define("PATH_MAUI","/usr/local/maui");

define("QRUN_CMD",PATH_TORQUE."/bin/qrun");
define("QDEL_CMD",PATH_TORQUE."/bin/qdel");
define("QHOLD_CMD",PATH_TORQUE."/bin/qhold");
define("QSTOP_CMD",PATH_TORQUE."/bin/qstop");
define("QSTART_CMD",PATH_TORQUE."/bin/qstart");
define("QRLS_CMD",PATH_TORQUE."/bin/qrls");
define("QSTAT_CMD",PATH_TORQUE."/bin/qstat");
define("QNODES_CMD",PATH_TORQUE."/bin/qnodes");
define("TRACEJOB",PATH_TORQUE."/bin/tracejob");

define("CONFIG_CMD",PATH_MAUI."/bin/showconfig");
define("CANCELJOB_CMD",PATH_MAUI."/bin/canceljob");
define("DIAGNOSE_CMD",PATH_MAUI."/bin/diagnose");
define("SETHOLD_CMD",PATH_MAUI."/bin/sethold");
define("RELEASEHOLD_CMD",PATH_MAUI."/bin/releasehold");
define("RUNJOB_CMD",PATH_MAUI."/bin/runjob");
define("CHECKJOB_CMD",PATH_MAUI."//bin/checkjob");
define("JOBS_CMD",PATH_MAUI."/bin/showq");
define("STATUS_CMD",PATH_MAUI."/bin/showstats");
define("SHOWSTATE_CMD",PATH_MAUI."/bin/showstate");
?>
