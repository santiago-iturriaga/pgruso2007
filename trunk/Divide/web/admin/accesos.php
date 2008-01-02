<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("TPL.php");
	include_once("Usuarios.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Tabla/Tabla.php");
	$s = new Sesion(0);
	if($s->sesion == null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/accesos/accesos.html");
	$opcion_select=$plantilla->load("plantillas/usuarios/grupo.html");
	$mensaje = "";
	$error = "";

	$opciones_grupos	= "";


	$conexion = new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$Usuarios	= new Usuarios($conexion);

	$mensaje	= "";

	if(isset($_POST["asociar"])){
		$accesos_grupo= array();
		$res 	= $Usuarios->getTrabajosGrupo($s->sesion->grupo_actual);
		if($res["error"]) error_log(print_r($res,1));
		else $accesos_grupo = $res["trabajos"];

		$total_accesos = $s->sesion->Usuario->trabajos;

		foreach($total_accesos as $id=>$accion){
			// eliminar
			if(isset($accesos_grupo[$id]) and !isset($_POST["accion_".$id])){
				$res = $Usuarios->desasociarTrabajoGrupo($id,$s->sesion->grupo_actual);
				if($res["error"]) error_log(print_r($res,1));
			}
			// nueva
			if(!isset($accesos_grupo[$id]) and isset($_POST["accion_".$id])){
				$res = $Usuarios->asociarTrabajoGrupo($id,$s->sesion->grupo_actual);
				if($res["error"]) error_log(print_r($res,1));
			}

		}
	}


	if(isset($_POST["grupo"])) $s->sesion->grupo_actual  = $_POST["grupo"];


	$res=$Usuarios->getGrupos();
	if($res["error"]) error_log(print_r($res,1));
	else{
		$opciones_grupos.=$plantilla->replace($opcion_select,array("ID"=>"","NOMBRE"=>"","SELECTED"=>""));
		foreach ($res["grupos"] as $id=>$nombre){
			$selected ="";
			if($s->sesion->grupo_actual==$id) $selected="SELECTED";
			$opciones_grupos.=$plantilla->replace($opcion_select,array("ID"=>$id,"NOMBRE"=>$nombre,"SELECTED"=>$selected));
			}
		}

	$form_accesos = "";
	if($s->sesion->grupo_actual != null){
		$form_accesos=$plantilla->load("plantillas/accesos/form_accesos.html");
		$check_box	   =$plantilla->load("plantillas/accesos/check.html");
		$accesos = "";

		$total_accesos = $s->sesion->Usuario->trabajos;

		$accesos_grupo= array();
		$res 	= $Usuarios->getTrabajosGrupo($s->sesion->grupo_actual);
		if($res["error"]) error_log(print_r($res,1));
		else $accesos_grupo = $res["trabajos"];

		$accesos = "";
		foreach($total_accesos as $id=>$accion){
			$checked	= "";
			if(isset($accesos_grupo [$id])) $checked = "CHECKED";
			$accesos .=$plantilla->replace($check_box,array("ID"=>$id,"ACCION"=>$accion["cliente"]." - ".$accion["trabajo"],"CHECKED"=>$checked));
		}
		$form_accesos	=$plantilla->replace($form_accesos,array("ACCESOS"=>$accesos));
	}


	$ppal  = $plantilla->replace($ppal,array("GRUPOS"=>$opciones_grupos,"ACCESOS"=>$form_accesos));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error));
	$s->salvar();

	echo $base;
?>