<?php
set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Directorio.php");
	include_once("Alertas.php");
	include_once("Tabla/Tabla.php");
	include_once("Interfaz.php");
	$s = new Sesion();

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit;
	}

	$plantilla	=	new TPL();
	$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$i = new Interfaz($conexion,$plantilla,$s);
	$msj = null;
	$msjerror = null;


if(isset($_POST["form_actual"])){
	$u = new Usuarios($conexion);
	$validos = $u->validarUsuario($s->sesion->Usuario->login,$_POST["form_actual"]);
	if($validos["error"] == 0){
		if($_POST["form_nueva"] == $_POST["form_confirmacion"]){
			$res = $u->cambiarClaveUsuario($s->sesion->Usuario->login,$_POST["form_nueva"]);
			if($res["error"] == 0){
				$msj		= 	$i->getMensaje("U01");

			}else{
				$msjerror		= 	$i->getError($res);

			}
		}else{

			$msjerror		= 	$i->getError2("EU04");
		}
	}
	else{
		$msjerror		= 	$i->getError($validos);
	}
}


	$ppal		= 	$plantilla->load("plantillas/clave.html");
	$base		=	$plantilla->load("plantillas/base.html");
	$menu		=	$plantilla->replace($plantilla->load("plantillas/menu.html"),
										array("CLASE_CLAVE"=>'id="actual"'));


	$ppal = $plantilla->replace($ppal,array("MENU_VERTICAL"=>$i->getMenuVertical(),));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,"MENU"=>$menu,"MENSAJE"=>$msj,"ERROR"=>$msjerror));
	$s->salvar();
	echo $base;
?>