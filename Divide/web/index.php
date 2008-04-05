<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	$s = new Sesion();
	if($s->sesion==null) $s= new Sesion(true);
	if($s->sesion->Usuario==null){
		$s->sesion->Usuario=new Usuario();
		}

	$plantilla	=	new TPL();
	$ppal		= 	"";
	$msj = null;
	$msjerror = null;

	if(isset($_POST["login"])){
		$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
		$s->sesion->Usuario->setBD($conexion);
		$res=$s->sesion->Usuario->Login($_POST["login"],$_POST["password"]);
		if($res["error"]) $msj="Usuario o clave incorrecta.";
		if($s->sesion->Usuario->Logueado()){
			$s->salvar();
			if(!$s->sesion->Usuario->administrador){
				header("Location: trabajos.php");
				exit;
			}
			else{
				header("Location: admin/index.php");
				exit;
			}

		}
	}
	$base	= "";


	if(!$s->sesion->Usuario->Logueado())
		$base	=	$plantilla->load("plantillas/login/login.html");
	else
		{
		header("Location: archivos.php");
		exit;
		}
	$base	=	$plantilla->replace($base,array("MENSAJE"=>$msj,"ERROR"=>$msjerror));
	$s->salvar();
	echo $base;
?>