<?php
define("CARPETA_ACTUAL",".");
define("CARPETA_ANTERIOR","..");
define("SEPARADOR_RUTA", "/");
class Directorio{
	var $inicio	=	null;
	var $camino	=	array();
	function Directorio($inicio){
		$this->inicio	= $inicio;
		$this->camino	= array($inicio);
	}
	
	function getArchivos(){
		$archivos		=	array();
		$directorios	=	array();
		if ($handle = opendir($this->getRuta())) {
    		while (false !== ($file = readdir($handle))){
    			if(is_dir($this->getRuta().SEPARADOR_RUTA.$file))
    				$directorios[$file]=1;
    			else	
    				$archivos[$file]=1;
    		}
    		return array("error"=>0,
    					 "archivos"=>$archivos,
    					 "directorios"=>$directorios);	
    	}
    	return array("error"=>1,
    				 "codError"=>"D001"); 
	}
	
	function getContenido($archivo){
		return file_get_contents ($this->getRuta().SEPARADOR_RUTA.$archivo);
	}
	function eliminar($archivo){
		error_log("eliminar: ".$this->getRuta().SEPARADOR_RUTA.$archivo);
		if($this->getRuta().SEPARADOR_RUTA.$archivo) return array("error"=>0);
		else return array("error"=>1,
    			 		  "codError"=>"D003");
		}
	
	function mover($carpeta){
		if($carpeta != CARPETA_ACTUAL){
			if($carpeta == CARPETA_ANTERIOR){
				if(count($this->camino)==1){
					return array("error"=>1,
    				 			 "codError"=>"D002");
				}
				else{
					array_pop($this->camino);
				}
			}else{
				array_push($this->camino,$carpeta);
			}
		}
	}
	function getRuta(){
		return implode(SEPARADOR_RUTA,$this->camino);
	}
}


?>