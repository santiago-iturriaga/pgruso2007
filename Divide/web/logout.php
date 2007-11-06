<?php
	set_include_path(get_include_path().';'.
					 '../lib');
	include_once("TPL.php");
	include_once("Sesion.php");	
	include_once("MiSesion.php");
	
	$sesion = new MiSesion();
	if($sesion!=null) 
		$sesion->cerrar();
	header("Location: index.php");
	exit; 
			
		
	?>