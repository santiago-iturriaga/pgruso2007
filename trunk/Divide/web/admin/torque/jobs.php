<?php
set_include_path(get_include_path() .
PATH_SEPARATOR .
'../../../lib');
include_once ("TPL.php");
include_once ("Sesion.php");
include_once ("Constantes.php");
include_once ("Conexion.php");
include_once ("Tabla/Tabla.php");
include_once ("lib.inc.php");
include_once ("Torque.php");

$s = new Sesion(0);
if ($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador) {
	header("Location: ../../index.php");
	exit;
}

$plantilla = new TPL();
$base = $plantilla->load("plantillas/base.html");
$ppal = $plantilla->load("plantillas/jobs/jobs.html");
$mensaje = "";
$error = "";

/*        u - USER
           o - OTHER
           s - SYSTEM
           n - None */
$held_type = "u";
if (ISSET ($_REQUEST["iniciar"])) {
	list($mensaje,$error) = torque_ejecutar_trabajo($_REQUEST["iniciar"]);
}
if (ISSET ($_REQUEST["eliminar"])) {
	list($mensaje,$error) = torque_eliminar_trabajo($_REQUEST["eliminar"]);
}
if (ISSET ($_REQUEST["detener"])) {
	list($mensaje,$error) = torque_detener_trabajo($_REQUEST["detener"], $held_type);
}
if (ISSET ($_REQUEST["reiniciar"])) {
	list($mensaje,$error) = troque_liberar_trabajo($_REQUEST["reiniciar"], $held_type);
}

// Listado de todos los trabajos
$command = SSH . " -l " . USERNAME . " " . HOST . " \"" . QSTAT_CMD . "; exit\" 2>&1";
$qstat = `$command`;

if ($qstat == "") {
	$pagina = "No hay trabajos.";
} else {
	$tabla = getTablaTrabajos($qstat);
	$pagina = $tabla->getTabla();
}

if (ISSET ($_REQUEST["id"])) {
	// Detalle de un trabajo
	$id = $_REQUEST["id"];
	$command = SSH . " -l " . USERNAME . " " . HOST . " \"" . QSTAT_CMD . " -f $id; exit\" 2>&1";
	$qstat_job = `$command`;
	$pagina .= "<br />" . getStatusJob($qstat_job);
}

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

$ppal = $plantilla->replace($ppal, array (
	"PAGINA" => $pagina
));
$base = $plantilla->replace($base, array (
	"PAGINA" => $ppal,
	"MENSAJE" => $mensajepl,
	"SMENU_JOBS" => " id='smactual' ",
	"USUARIO_LOGUEADO" => $s->sesion->Usuario->login,
	"ERROR" => $errorpl
));
$s->salvar();

echo $base;
?>