<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
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


	if(isset($_GET["alerta"])){

		$s->sesion->alerta_actual = $_GET["alerta"];
		$s->sesion->trabajo = $_GET["trabajo"];
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
	$menu		=	null;//$plantilla->replace($plantilla->load("plantillas/menu.html"),
					//					array("CLASE_ALERTAS"=>'id="actual"'));
	$msj = null;
	$msjerror = null;

	//obtengo las alertas


	$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);
	$i = new Interfaz($conexion,$plantilla,$s);
	$alertas = new Alertas($conexion);
	$resConsulta = $alertas->getAlertasUsuario($s->sesion->Usuario->id);
	//echo "<pre>";print_r($resConsulta);echo '</pre>';
	$tabAlertas= "";


	if($resConsulta["error"]){
		$msjerror = $i->getError($res);
	}
	else{
		$tabla = new Tabla();
		$tabla->dir_relativa = "../";
		$tabla->addColumna(1,"nombre_cliente","Cliente");
		$tabla->addColumna(2,"nombre_trabajo","Trabajo");
		$tabla->addColumna(3,"fecha","Fecha");
		$tabla->addColumna(4,"asunto","Asunto");
		$tabla->addColumna(5,"link","");


		//echo "<pre>";print_r($resConsulta);echo '</pre>';
		foreach ($resConsulta["alertas"] as $id=>$rowAlertas){
			//echo "<pre>";print_r($rowAlertas);echo '</pre>';
			$rowAlertas["link"]=$plantilla->replace($link,array("IDA"=>$rowAlertas["id"],"IDT"=>$rowAlertas{"trabajo"},"IDUA"=>$rowAlertas["idua"]));
			$rowAlertas["eliminar"]=$checkbox;
			if($rowAlertas{"leida"} == 0){
				$rowAlertas{"fecha"} = "<b>" . $rowAlertas{"fecha"} . "</b>";
				$rowAlertas{"asunto"} = "<b>" . $rowAlertas{"asunto"} . "</b>";
				$rowAlertas{"nombre_trabajo"} = "<b>" . $rowAlertas{"nombre_trabajo"} . "</b>";
				$rowAlertas{"nombre_cliente"} = "<b>" . $rowAlertas{"nombre_cliente"} . "</b>";
			}

			//$rowAlertas->asunto = $rowAlertas{"asunto"};
			$tabla->addRenglon($rowAlertas);

		}

		$tabla->clase = "";
		$tabAlertas = $tabla->getTabla();


	}

	//echo "<pre>";print_r($s->sesion->Usuario);echo '</pre>';exit;
	$ppal = $plantilla->replace($ppal,array("ALERTAS"=>$tabAlertas));

	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENSAJE"=>$msj,
												"ERROR"=>$msjerror,
												"MENU_USUARIOS"=>" class='menu_tab' ",
												"MENU_GANGLIA"=>" class='menu_tab'",
												"MENU_MAUI"=>" class='menu_tab'",
												"MENU_TORQUE"=>" class='menu_tab'",
												"MENU_ALERTAS"=>" id='actual'",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"MENU"=>""));

	$s->salvar();
	echo $base;
?>