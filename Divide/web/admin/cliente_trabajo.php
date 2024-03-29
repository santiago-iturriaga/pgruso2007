<?php
	set_include_path(get_include_path().PATH_SEPARATOR.
					 '../../lib');
	include_once("TPL.php");
	include_once("Usuarios.php");
	include_once("Sesion.php");
	include_once("Constantes.php");
	include_once("Conexion.php");
	include_once("Interfaz.php");
	include_once("Tabla/Tabla.php");
	include_once("Servidor.php");

	$s = new Sesion();
	if($s->sesion==null or !$s->sesion->Usuario->Logueado() or !$s->sesion->Usuario->administrador){
		header("Location: ../index.php");
		exit;
	}
	if(isset($_POST["cliente"])) $s->sesion->admin_cliente_actual=$_POST["cliente"];




	$plantilla	=	new TPL();
	$base		=	$plantilla->load("plantillas/base.html");
	$ppal		= 	$plantilla->load("plantillas/cliente_trabajo/cliente_trabajo.html");
	$links		= 	$plantilla->load("plantillas/cliente_trabajo/links.html");
	$nuevo		= 	$plantilla->load("plantillas/cliente_trabajo/nuevo.html");
	$opcion		= 	$plantilla->load("plantillas/cliente_trabajo/cliente.html");
	$menu=$plantilla->replace($plantilla->load("plantillas/menu_usuarios.html"),
							  array("SMENU_CT"=>" id='smactual' "));

	$form_cliente	= "";
	$opciones	=	"";
	$form		= 	"";
	$conexion = new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);

	$usuarios	= new Usuarios($conexion);
	$interfaz = new Interfaz($conexion,$plantilla,$s);
	$mensaje = "";
	$error = "";


	if(isset($_POST["nombre_cliente"])){
		$commandFinger = "finger ".$_POST["usr_linux"];
		$resFinger = ejecutar_servidor($commandFinger);
		if($s->sesion->estado_cliente == 'NUEVO'){

			if(stripos($resFinger,"no such user") == null){
					$res	= $usuarios->crearCliente($_POST["nombre_cliente"],$_POST["usr_linux"]);
				    if($res["error"])
					 $error	= $interfaz->getError($res);
				    else{
				      $mensaje = $interfaz->getMensaje("CT004");
				      $s->sesion->admin_cliente_actual = $res["id"];

				    }
			}else{
				$error	= $interfaz->getError2("U55");
			}
		}
		else{
			$res	= $usuarios->editarCliente($s->sesion->admin_cliente_actual,$_POST["nombre_cliente"]);
		    if($res["error"])
			 $error	= $interfaz->getError($res);
		    else{
		      $mensaje = $interfaz->getMensaje("CT008");
		    }
		}

	}
	if(isset($_POST["nodos"])){
		$s->sesion->trabajo_editado["NOMBRE"]=$_POST["nombre"];
		$s->sesion->trabajo_editado["TIEMPO"]=$_POST["tiempo_maximo"];
		$s->sesion->trabajo_editado["NODOS"]=$_POST["nodos"];
		$s->sesion->trabajo_editado["COLA"]=$_POST["cola"];
		$s->sesion->trabajo_editado["QUOTA"]=$_POST["quota"];
		if(isset($s->sesion->trabajo_editado["ID"]) and $s->sesion->trabajo_editado["ID"]!=null)
			$res = $usuarios->editarTrabajo($s->sesion->trabajo_editado["ID"],
										   $s->sesion->trabajo_editado["NOMBRE"],
										   $s->sesion->trabajo_editado["NODOS"],
										   $s->sesion->trabajo_editado["TIEMPO"],
										   $s->sesion->trabajo_editado["COLA"],
										   $s->sesion->trabajo_editado["QUOTA"]
										   );
		else
			$res = $usuarios->crearTrabajo($s->sesion->admin_cliente_actual,
										   $s->sesion->trabajo_editado["NOMBRE"],
										   $s->sesion->trabajo_editado["NODOS"],
										   $s->sesion->trabajo_editado["TIEMPO"],
										   $s->sesion->trabajo_editado["COLA"],
										   $s->sesion->trabajo_editado["QUOTA"],
										   $s->sesion->grupo_actual
										   );

		if($res["error"])
			$error	= $interfaz->getError($res);
		else{
			if(isset($s->sesion->trabajo_editado["ID"]) and $s->sesion->trabajo_editado["ID"]!=null)
				$mensaje = $interfaz->getMensaje("CT002");
			else{
				$mensaje = $interfaz->getMensaje("CT003");
			}
			$s->sesion->trabajo_editado=null;
		}
	}

	if(isset($_GET["nuevo_cliente"])){
		$form_cliente	= 	$plantilla->load("plantillas/cliente_trabajo/form_cliente.html");
		$s->sesion->estado_cliente = 'NUEVO';
	}
	if(isset($_GET["editar_cliente"])){
		$form_cliente	= 	$plantilla->load("plantillas/cliente_trabajo/form_cliente_editar.html");
		$s->sesion->admin_cliente_actual = $_GET["editar_cliente"];
		$res = $usuarios->getCliente($s->sesion->admin_cliente_actual);
		$form_cliente	=	$plantilla->replace($form_cliente,array("NOMBRE"=>$res["cliente"]["nombre"],"USR_LINUX"=>$res["cliente"]["usr_linux"]));
		$s->sesion->estado_cliente = 'EDITAR';
	}


	if(isset($_GET["eliminar"])){
		$res = $usuarios->eliminarTrabajo($_GET["eliminar"]);
		if($res["error"])
			$error	= $interfaz->getError($res);
		else
			$mensaje	= $interfaz->getMensaje("CT001");
	}

	if(isset($_GET["eliminar_cliente"])){
		$res = $usuarios->eliminarClienteFisicamente($_GET["eliminar_cliente"]);
		if($res["error"])
			$error	= $interfaz->getError($res);
		else{
			$mensaje	= $interfaz->getMensaje("CT007");
			$s->sesion->admin_cliente_actual = null;
		}
	}


	if(isset($_GET["editar"])){
		$s->sesion->trabajo_editado=array("ID"=>$_GET["editar"]);
		$res = $usuarios->getTrabajo($s->sesion->trabajo_editado["ID"]);
		if($res["error"])
			$error	= $interfaz->getError($res);
		else{
			$s->sesion->trabajo_editado["NOMBRE"]=$res["trabajo"]["nombre"];
			$s->sesion->trabajo_editado["TIEMPO"]=$res["trabajo"]["tiempo_maximo"];
			$s->sesion->trabajo_editado["NODOS"]=$res["trabajo"]["nodos"];
			$s->sesion->trabajo_editado["COLA"]=$res["trabajo"]["cola"];
			$s->sesion->trabajo_editado["QUOTA"]=$res["trabajo"]["quota"];
			$s->sesion->trabajo_editado["TITULO"]="Editar trabajo";
		}
	}
	if(isset($_GET["nuevo"])){
		$s->sesion->trabajo_editado["ID"]=null;
		$s->sesion->trabajo_editado["NOMBRE"]="";
		$s->sesion->trabajo_editado["TIEMPO"]="";
		$s->sesion->trabajo_editado["NODOS"]="";
		$s->sesion->trabajo_editado["COLA"]="";
		$s->sesion->trabajo_editado["QUOTA"]="";
		$s->sesion->trabajo_editado["TITULO"]="Crear trabajo";
	}

	$res	= $usuarios->getClientes();
	if($res["error"]) $error	= $interfaz->getError($res);
	else{
		$clientes_ = array();
		foreach ($res["clientes"] as $id=>$cliente){
			$clientes_[$id] = $cliente["nombre"];
		}
		asort($clientes_);
		foreach ($clientes_ as $id=>$nombre){

			if($s->sesion->admin_cliente_actual==null) $s->sesion->admin_cliente_actual=$id;
			if($s->sesion->admin_cliente_actual==$id)
				$opciones.=	$plantilla->replace($opcion,array("ID"=>$id,"NOMBRE"=>$nombre,"SELECTED"=>"SELECTED"));
			else
				$opciones.=	$plantilla->replace($opcion,array("ID"=>$id,"NOMBRE"=>$nombre,"SELECTED"=>""));
		}
	}
	$t="";
	$res	= $usuarios->getTrabajosCliente($s->sesion->admin_cliente_actual);
	if($res["error"]) $error	= $interfaz->getError($res);
	else{
		$tabla = new Tabla("","","../");
		$tabla->addColumna(0,"trabajo","Trabajo");
		$tabla->addColumna(1,"nodos","Nodos");
		$tabla->addColumna(2,"tiempo_maximo","Tiempo m&aacute;ximo");
		$tabla->addColumna(3,"cola","Cola");
		$tabla->addColumna(4,"quota","Quota");
		$tabla->addColumna(5,"links",$nuevo);
		foreach($res["trabajos"] as $id=>$trabajo){
			$row = array();
			$row["trabajo"]=$trabajo["nombre"];
			$row["nodos"]=$trabajo["nodos"];
			$row["tiempo_maximo"]=$trabajo["tiempo_maximo"];
			$row["cola"]=$trabajo["cola"];
			$row["quota"]=$trabajo["quota"];
			$row["links"]=$plantilla->replace($links,array("ID"=>$id));
			$tabla->addRenglon($row);
		}
		$t=$tabla->getTabla();
	}
	if($s->sesion->trabajo_editado!=null)
		$form	= $plantilla->replace($plantilla->load("plantillas/cliente_trabajo/form_trabajo.html"),
									  $s->sesion->trabajo_editado);

	$ppal	=	$plantilla->replace($ppal,array("OPCIONES"=>$opciones,
												"TABLA"=>$t,
												"FORM"=>$form,
												"FORM_CLIENTE"=>$form_cliente,
												"CLIENTE"=>$s->sesion->admin_cliente_actual));
	$base	=	$plantilla->replace($base,array("PAGINA"=>$ppal,
												"MENU_USUARIOS"=>" id='actual' ",
												"MENU_GANGLIA"=>" class='menu_tab'",
												"MENU_MAUI"=>" class='menu_tab'",
												"MENU_TORQUE"=>" class='menu_tab'",
												"MENU_ALERTAS"=>" class='menu_tab'",
												"MENSAJE"=>$mensaje,
												"ERROR"=>$error,
												"MENU_USUARIOS"=>" id='actual' ",
												"USUARIO_LOGUEADO"=>$s->sesion->Usuario->login,
												"MENU"=>$menu));
	$s->salvar();

	echo $base;

?>