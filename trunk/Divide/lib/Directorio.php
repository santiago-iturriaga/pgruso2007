<?php
define("CARPETA_ACTUAL",".");
define("CARPETA_ANTERIOR","..");
define("SEPARADOR_RUTA", "/");
include_once ("TPL.php");
include_once ("Constantes.php");
include_once ("Momento.php");
class Directorio{
	var $inicio	=	null;
	var $camino	=	array();
	function Directorio($inicio){
		$this->inicio	= $inicio;
		$this->camino	= array($inicio);
	}
	function es_vacio($dir){
		if ($handle = opendir($dir) and readdir($handle)===true)
			return true;
		else
			return false;
	}
	function getArchivos(){
		$archivos		=	array();
		$directorios	=	array();
		$ruta			=	$this->getRuta();
		if ($handle = opendir($this->getRuta())) {
			while (false !== ($file = readdir($handle))){
    			if(is_dir($ruta.SEPARADOR_RUTA.$file)){
    				if($file=='.')
    					$directorios[$file]=array("entrar"=>false,
    							  				 "eliminar"=>false);
    				else if($file=='..'){
    					if(count($this->camino)>1)
    						$directorios[$file]=array("entrar"=>true,
    						  					   "eliminar"=>false);
    					else
    						$directorios[$file]=array("entrar"=>false,
    						  					   "eliminar"=>false);
    				}
    				else if(!$this->es_vacio($ruta.SEPARADOR_RUTA.$file))
    					$directorios[$file]=array("entrar"=>true,
    										  "eliminar"=>false);
					else $directorios[$file]=array("entrar"=>true,
    											  "eliminar"=>true);
    				$directorios[$file]["ultima_modificacion"] = date ("F d Y H:i:s.", filemtime($ruta.SEPARADOR_RUTA.$file));

    			}
    			else{
    				$size	= filesize($ruta.SEPARADOR_RUTA.$file);
    				$ejecutable = is_executable  ($ruta.SEPARADOR_RUTA.$file);
					$fecha = date ("F d Y H:i:s.", filemtime($ruta.SEPARADOR_RUTA.$file));

    				$archivos[$file]=array("size_"=>$size,
    									   "size"=>$this->getStrSize($size),
    									   "ejecutar"=>$ejecutable,
    									   "ultima_modificacion"=>$fecha);
    			}
    		}
    		clearstatcache();
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
	function ejecutar($archivo,$argumentos,$id_cliente,$id_trabajo){
		$conexion= new Conexion(CONEXION_HOST,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);

		$momento = new Momento($conexion);
		//ejecutar($archivo,$ruta,$parametros,$argumentos,$id_cliente,$id_trabajo)
		$res = $momento->ejecutar($archivo,$this->getRuta(),$argumentos,$id_cliente,$id_trabajo);
		return $res;

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