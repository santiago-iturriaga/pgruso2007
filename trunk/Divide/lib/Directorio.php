<?php
define("DIRECTORIO","DIRECTORIO");
define("ARCHIVO","ARCHIVO");
class Directorio{
	var $ruta	=	null;
	function Directorio($ruta){
		$this->ruta	= $ruta;
	}
	
	function getArchivos(){
		$salida	=	array();
		if ($handle = opendir($this->ruta)) {
    		while (false !== ($file = readdir($handle))){
    			$tipo	=	DIRECTORIO;
    			if(!(is_dir($this->ruta.SEPARADOR_RUTA.$file))) $tipo=ARCHIVO;	
    			$salida[$file]=array("tipo"=>$tipo);
    		}
    		return array("error"=>0,
    					 "archivos"=>$salida);	
    	}
    	return array("error"=>1,
    				 "codError"=>"D001"); 
	}
	
	function getContenido($archivo){
		return file_get_contents ($this->ruta.SEPARADOR_RUTA.$archivo);
	}
}


?>
