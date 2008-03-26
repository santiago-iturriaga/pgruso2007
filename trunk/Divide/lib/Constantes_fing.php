<?php
define("RAIZ", "/net/home/pgccadar");
define("RAIZ_SISTEMA", "/net/home/pgccadar/sistema");
define("TMP", "/tmp");
define("GRUPOFENTON","Fenton");


define("CONEXION_HOST", "lennon.fing.edu.uy");
define("CONEXION_PORT", "54320");
define("CONEXION_USUARIO", "pgccadar");
define("CONEXION_PASSWORD", "pgccadar");
define("CONEXION_BASE", "pgccadar");
define("QSUB", "/net/local/pgccadar/bin/qsub");
define("MPIEXEC", "/net/local/pgccadar/bin/mpiexec");

define("REDIRECCION_SALIDA", "/net/home/pgccadar/subversion/trunk/Divide/bin/redireccion_salida.php");
define("OUTPUT", "salida_extra");
define("EJECUTABLE","plantillas/archivos/qsub.script");
define("LOG_EJECUCIONES","../log/ejecuciones.log");
define("TIEMPO_REFRESH_RESULTADOS","5");

// =======================================================
// GANGLIA
// =======================================================
define("GANGLIA_URL","http://lennon.fing.edu.uy/ganglia");

// =======================================================
// SSH
// =======================================================
define("SSH","ssh");

define("USERNAME","pgccadar");
define("HOST","localhost");

// =======================================================
// TORQUE / MAUI
// =======================================================
define("PATH_TORQUE","/net/local/pgccadar");
define("PATH_MAUI","/net/local/pgccadar");

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