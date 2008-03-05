<?php
define("RAIZ", "/home");
define("RAIZ_SISTEMA", "/home/sistema");
define("TMP", "/tmp");
define("CONEXION_HOST", "localhost");
define("CONEXION_PORT", "5432");
define("CONEXION_USUARIO", "pgccadar");
define("CONEXION_PASSWORD", "pgccadar");
define("CONEXION_BASE", "pgccadar");
define("QSUB", "/usr/local/torque/bin/qsub");
define("MPIEXEC", "/usr/local/mpiexec/bin/mpiexec");

define("REDIRECCION_SALIDA", "/pgruso/Divide/bin/redireccion_salida.php");
define("OUTPUT", "salida_extra");
define("EJECUTABLE","plantillas/archivos/qsub.script");
define("LOG_EJECUCIONES","../log/ejecuciones.log");
define("TIEMPO_REFRESH_RESULTADOS","5");

// =======================================================
// GANGLIA
// =======================================================
define("GANGLIA_URL","http://localhost/ganglia");

// =======================================================
// SSH
// =======================================================
define("SSH","ssh");

define("USERNAME","santiago");
define("HOST","localhost");

// =======================================================
// TORQUE / MAUI
// =======================================================
define("PATH_TORQUE","/usr/local/torque");
define("PATH_MAUI","/usr/local/maui");

define("QRUN_CMD",PATH_TORQUE."/bin/qrun");
define("QDEL_CMD",PATH_TORQUE."/bin/qdel");
define("QHOLD_CMD",PATH_TORQUE."/bin/qhold");
define("QSTOP_CMD",PATH_TORQUE."/bin/qstop");
define("QSTART_CMD",PATH_TORQUE."/bin/qstart");
define("QRLS_CMD",PATH_TORQUE."/bin/qrls");
define("QSTAT_CMD",PATH_TORQUE."/bin/qstat");
define("QNODES_CMD",PATH_TORQUE."/bin/qnodes");

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