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

	if($s->sesion->TrabajoActual==null){
		header("Location: trabajos.php");
		exit;
	}

	if(isset($_GET["alerta"])){

		$s->sesion->alerta_actual = $_GET["alerta"];
		$s->sesion->alerta_actualIDUA = $_GET["idUA"];
		$s->sesion->bytes_leidos = 0;
		$s->salvar();
		header("Location: alerta.php");
		exit;
	}

	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/alertas/alertas.html");
	$link		= 	$plantilla->load("plantillas/alertas/link.html");
	$checkbox = 	$plantilla->load("plantillas/alertas/checkbox.html");
	$menu		= $plantilla->replace($plantilla->load("plantillas/menu.html"),
										array("CLASE_ALERTAS"=>'id="actual"'));
	$msj = null;
	$msjerror = null;

	//obtengo las alertas


	$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$i = new Interfaz($conexion,$plantilla,$s);
	$alertas = new Alertas($conexion);
	//echo "<pre>";print_r($s);echo '</pre>';
	$resConsulta = $alertas->getAlertasUsuarioTrabajo($s->sesion->Usuario->id,$s->sesion->TrabajoActual);
	$tabAlertas= "";


	if($resConsulta["error"]){
		$msjerror = $i->getError($res);
	}
	else{
		$tabla = new Tabla();
		$tabla->addColumna(1,"fecha","Fecha");
		$tabla->addColumna(2,"asunto","Asunto");
		$tabla->addColumna(3,"link","");


		foreach ($resConsulta["alertas"] as $id=>$rowAlertas){
			$rowAlertas["link"]=$plantilla->replace($link,array("ID"=>$id,"IDUA"=>$rowAlertas["idua"]));

			if($rowAlertas{"leida"} == 0){
				$rowAlertas{"fecha"} = "<b>" . $rowAlertas{"fecha"} . "</b>";
				$rowAlertas{"asunto"} = "<b>" . $rowAlertas{"asunto"} . "</b>";
			}
			$tabla->addRenglon($rowAlertas);
		}

		$tabla->clase = "";
		$tabAlertas = $tabla->getTabla();
		//echo "<pre>";print_r($tabla);echo '</pre>';

	}

	//echo "<pre>";print_r($s->sesion->Usuario);echo '</pre>';exit;
	$ppal = $plantilla->replace($ppal,array("ALERTAS"=>$tabAlertas));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENU"=>$menu,
												"MENSAJE"=>$msj,
												"ERROR"=>$msjerror,
												"HEAD"=>"",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login));
	$s->salvar();
	echo $base;
?>