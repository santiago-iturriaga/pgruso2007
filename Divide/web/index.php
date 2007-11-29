<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");	
	include_once("Conexion.php");
	include_once("Constantes.php");
	$manejador = new ManejadorSesion();
	$sesion = $manejador->getSesion();
	error_log(print_r($sesion,1));
	if($sesion==null) $sesion = $manejador->getSesion(true);
	if($sesion->Usuario==null){
		$sesion->Usuario=new Usuario(); 
		}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	"";
	
	if(isset($_POST["login"])){
		$conexion= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
		$sesion->Usuario->setBD($conexion);
		$res=$sesion->Usuario->Login($_POST["login"],$_POST["password"]);
		if($res["error"]) error_log(print_r($res,1));
		if($sesion->Usuario->Logueado()){
			$sesion->salvar();
			if(!$sesion->Usuario->administrador){
				header("Location: trabajos.php");
				exit;
			}
			else{
				header("Location: admin/index.php");
				exit;
			}
			
		}
	}
	
	if(!$sesion->Usuario->Logueado()){
		$ppal	=	$plantilla->load("plantillas/login/login.html");
	}
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>""));
	$manejador->salvar();
	echo $base;												
?>
