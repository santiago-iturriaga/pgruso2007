<?php
define("RAIZ", "/home");
#define("RAIZ", "/net/home/pgccadar");
define("CONEXION_HOST", "localhost");
#define("CONEXION_HOST", "164.73.47.246");
define("CONEXION_USUARIO", "pgccadar");
define("CONEXION_PASSWORD", "pgccadar");
define("CONEXION_BASE", "pgccadar");
define("QSUB", "/usr/local/torque/bin/qsub");
#define("QSUB", "/net/local/pgccadar/bin/qsub");
define("MPIEXEC", "/usr/local/mpiexec/bin/mpiexec");
#define("MPIEXEC", "/net/local/pgccadar/bin/mpiexec");

define("REDIRECCION_SALIDA", "/pgruso/Divide/bin/redireccion_salida.php");
#define("REDIRECCION_SALIDA", "/net/home/pgccadar/subversion/trunk/Divide/bin/redireccion_salida.php");
define("OUTPUT", "salida_extra");
define("EJECUTABLE","plantillas/archivos/qsub.script");
define("LOG_EJECUCIONES","../log/ejecuciones.log");
define("TIEMPO_REFRESH_RESULTADOS","5");
?>