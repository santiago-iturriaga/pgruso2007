<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Directorio.php");
	include_once("Interfaz.php");
	$interfaz = new Interfaz($conexion,$plantilla,$s);
	$plantilla	=	new TPL();

	$s = new Sesion();

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit;
		}

	if($s->sesion->TrabajoActual==null){
		header("Location: trabajos.php");
		exit;
	}

	if(isset($_POST["Enviar"])) {
		if (is_uploaded_file($HTTP_POST_FILES['archivo_']['tmp_name'])) {
			$command = "chown ".USERNAME." ".$HTTP_POST_FILES['archivo_']['name'];
			$rs = `$command`;
			$command = SSH . " -l " . USERNAME . " " . HOST . " \"cp ".$HTTP_POST_FILES['archivo_']['tmp_name']." ".$s->sesion->Directorio->getRuta().'/'.$HTTP_POST_FILES['archivo_']['name']."; exit\" 2>&1";
			$rs = `$command`;
			if($rs!=""){
				error_log($rs);
				$msjerror	= $interfaz->getError(array("codError"=>"D100"));
			}
		}
		if($HTTP_POST_FILES['archivo_']['type'] == 'application/zip'){
			$res=$s->sesion->Directorio->descomprimir($HTTP_POST_FILES['archivo_']['name']);
			if($res["error"]) $msjerror	= $interfaz->getError($res);
		}

	}
	if(isset($_POST["carpeta"])) {
		$res = $s->sesion->Directorio->crearCarpeta($_POST["carpeta"]);
		if($res["error"]) $msjerror	= $interfaz->getError($res);
	}

	if(isset($_GET["descargar"])){
		$archivo	=	$s->sesion->Directorio->getContenido($_GET["descargar"]);
		header ("Content-Disposition: attachment; filename=".$_GET["descargar"]."\n\n");
		header ("Content-Type: application/text");
		header ("Content-Length: ".strlen($archivo));
		echo $archivo;
		exit;
	}
	if(isset($_GET["eliminar"])){
		$res	=	$s->sesion->Directorio->eliminar($_GET["eliminar"]);
		if($res["error"]) $msjerror	= $interfaz->getError($res);
	}
	if(isset($_GET["rmdir"])){
		$res	=	$s->sesion->Directorio->eliminarCarpeta($_GET["rmdir"]);
		if($res["error"]) $msjerror	= $interfaz->getError($res);
	}
	if(isset($_POST["ejecutar"])){
		$res	=	$s->sesion->Directorio->ejecutar($_POST["archivo"],
													 $_POST["argumentos"],
													 $s->sesion->ClienteActual,
													 $s->sesion->TrabajoActual);
		$s->sesion->archivo_actual = $res["salida"];
		$s->sesion->ejecucion_actual = $res["id"];
		$s->sesion->bytes_leidos = 0;
		$s->salvar();
		if($res["error"]){
			if($res["error"]) $msjerror	= $interfaz->getError($res);
		}
		else{
			header("Location: resultados.php");
			exit;
		}
	}


	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/archivos/archivos.html");
	$p_directorio	=	$plantilla->load("plantillas/archivos/directorio.html");
	$p_archivo	=	$plantilla->load("plantillas/archivos/archivo.html");
	$p_ruta		=	$plantilla->load("plantillas/archivos/ruta.html");
	$menu		=	$plantilla->replace($plantilla->load("plantillas/menu.html"),
										array("CLASE_ARCHIVOS"=>'id="actual"'));//$s->sesion->getMenuVertical($plantilla->load("plantillas/menu_vertical.html"),$plantilla);


	if($s->sesion->Directorio == null)
		{
		$s->sesion->Directorio	= new Directorio(RAIZ."/".$s->sesion->ClienteActual."/".$s->sesion->TrabajoActual);
		}


	if(isset($_GET["directorio"]) and $_GET["directorio"]!=""){
		$s->sesion->Directorio->mover($_GET["directorio"]);
		}
	if(isset($_GET["retroceder"]) and $_GET["retroceder"]!=""){
		$s->sesion->Directorio->retroceder($_GET["retroceder"]);
		}
	$res		=	$s->sesion->Directorio->getArchivos();
	if($res["error"]){
		$res["archivos"]=array();
		$res["directorios"]=array();
		$msjerror	= $interfaz->getError($res);
	}
	$archivos	=	$res["archivos"];
	$directorios=	$res["directorios"];
	asort($archivos);
	asort($directorios);
	$p_archivos	=	"";
	foreach($directorios as $d=>$valores){
		if($valores["entrar"]) {
			$directorio_nuevo= 	$plantilla->replace($p_directorio,array("NOMBRE"=>$d,"FECHA"=>$valores["ultima_modificacion"]));
			$directorio_nuevo= $plantilla->uncomment($directorio_nuevo,array("ENTRAR"));

			if ($d=="..") {
				$directorio_nuevo= $plantilla->uncomment($directorio_nuevo,array("UP"));
				$directorio_nuevo= $plantilla->uncomment($directorio_nuevo,array("ENTRAR_UP"));
			} elseif ($d==".") {
				// nada?
			} else {
				$directorio_nuevo= $plantilla->uncomment($directorio_nuevo,array("DOWN"));
				$directorio_nuevo= $plantilla->uncomment($directorio_nuevo,array("ENTRAR_DOWN"));
			}

			if($valores["eliminar"])  $directorio_nuevo= $plantilla->uncomment($directorio_nuevo,array("ELIMINAR"));
			$p_archivos	.= $directorio_nuevo;
		}
	}
	foreach($archivos as $archivo=>$valores){
		$archivo_nuevo = $plantilla->replace($p_archivo,array("NOMBRE"=>$archivo,"TAMANIO"=>$valores["size"],"FECHA"=>$valores["ultima_modificacion"]));
		if($valores["ejecutar"])  $archivo_nuevo= 	$plantilla->uncomment($archivo_nuevo,array("EJECUTAR"));
		else   $archivo_nuevo= 	$plantilla->uncomment($archivo_nuevo,array("NO_EJECUTAR"));
		$p_archivos	.=	$archivo_nuevo;
	}

	$camino		=	$s->sesion->Directorio->camino;
	//$camino[0]	=	"home";
	$camino[0] = "<img src='imagenes/house.png' title='Home' alt='Home' />";
	$ruta	= "";

	while($carpeta=array_shift($camino)){
		$ruta	.= $plantilla->replace($p_ruta,array("DIRECTORIO"=>$carpeta,
													 "PASOS"=>count($camino)));
	}


	$ppal	=	$plantilla->replace($ppal,array("MENU_VERTICAL"=>"",
												"ARCHIVOS"=>$p_archivos,
												"RUTA"=>$ruta));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu,
							"BODY"=>'',"MENSAJE"=>$msj,"ERROR"=>$msjerror,"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login));
	$s->salvar();
	echo $base;
?>