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
		$ruta			=	$this->getRuta();
		if ($handle = opendir($this->getRuta())) {
    		while (false !== ($file = readdir($handle))){
    			if(is_dir($ruta.SEPARADOR_RUTA.$file))
    				$directorios[$file]=1;
    			else{
    				$size	= filesize($ruta.SEPARADOR_RUTA.$file);
    				$archivos[$file]=array("size_"=>$size,
    									   "size"=>$this->getStrSize($size));
    			}
    		}
    		return array("error"=>0,
    					 "archivos"=>$archivos,
    					 "directorios"=>$directorios);	
    	}
    	return array("error"=>1,
    				 "codError"=>"D001"); 
	}
	
	function getStrSize($size){
		if($size<1024) return $size."B";
		else{
			$size=round($size/1024,1);
			if($size<1024) return $size."KB";
			else{
				round($size/1024,1);
				if($size<1024) return $size."MB";
				else{
					round($size/1024,1);
					return $size."GB";
				}
			}
		}
	}
	
	function getContenido($archivo){
		return file_get_contents ($this->getRuta().SEPARADOR_RUTA.$archivo);
	}
	function eliminar($archivo){
		error_log("eliminar: ".$this->getRuta().SEPARADOR_RUTA.$archivo);
		if(unlink($this->getRuta().SEPARADOR_RUTA.$archivo)) return array("error"=>0);
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
		return array("error"=>0);
	}
	
	function retroceder($cantidad){
		if(!is_numeric ( $cantidad) or $cantidad<0){
			return array("error"=>1,
    				 	 "codError"=>"D003");
		}
		while($cantidad>0 and count($this->camino)>1){
			array_pop($this->camino);
			$cantidad--;
		}
	}
	function getRuta(){
		return implode(SEPARADOR_RUTA,$this->camino);
	}
}


?>