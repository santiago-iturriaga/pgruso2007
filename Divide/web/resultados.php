<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");
	include_once("Conexion.php");
	include_once("Constantes.php");
	include_once("Directorio.php");
	include_once("Momento.php");


	$conexion	= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$momento 	= new Momento($conexion);

	$s = new Sesion();

	if($s->sesion==null or !$s->sesion->Usuario->Logueado()){
		header("Location: index.php");
		exit;
		}

	if($s->sesion->TrabajoActual==null){
		header("Location: trabajos.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/resultados/resultados.html");
	$menu		=	$plantilla->replace($plantilla->load("plantillas/menu.html"),
										array("CLASE_RESULTADOS"=>'id="actual"'));//$s->sesion->getMenuVertical($plantilla->load("plantillas/menu_vertical.html"),$plantilla);
	$msj = null;
	$msjerror = null;


	$enejecucion=torque_getTrabajosEnEjecucion(
				$s->sesion->Usuario->clientes[$s->sesion->ClienteActual]["usr_linux"]
				);
	$mostrar_en_ejecucion = 1;
	if(isset($_GET["terminado"])) $mostrar_en_ejecucion = 0;
	$ppal	=	$plantilla->replace($ppal,array("SEGUNDOS"=>TIEMPO_REFRESH_RESULTADOS,
												"ID_EJECUCION"=>$s->sesion->ejecucion_actual,
												"MOSTRAR_EN_EJECUCION"=>$mostrar_en_ejecucion,
												"ENEJECUCION"=>$enejecucion));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
							"MENU"=>$menu,
							"HEAD"=>"",
							"BODY"=>"onload='delay();
									 if(!NiftyCheck())
		    							return;
   									RoundedTop(\"div#cabezal_resultados\",\"#ffffff\",\"#dddddd\",\"small\");'","MENSAJE"=>$msj,"ERROR"=>$msjerror,"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login));
	$s->salvar();
	echo $base;
?>