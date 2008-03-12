<?php
define("CARPETA_ACTUAL",".");
define("CARPETA_ANTERIOR","..");
define("SEPARADOR_RUTA", "/");
include_once ("TPL.php");
include_once ("Constantes.php");
include_once ("Momento.php");
include_once ("Servidor.php");
class Directorio{
	var $inicio	=	null;
	var $camino	=	array();
	function Directorio($inicio){
		$this->inicio	= $inicio;
		$this->camino	= array($inicio);
	}
	function es_vacio($dir){
		if ($handle = opendir($dir)){
			 $aux = readdir($handle);
			 if($aux!='.' and $aux!='..') return false;
			 $aux = readdir($handle);
			 if($aux!='.' and $aux!='..') return false;
			 $aux = readdir($handle);
			 if($aux=="")
			 	return true;
			 else
			 	return false;
		}
		else
			return false;
	}
	function getArchivos(){
		$archivos		=	array();
		$directorios	=	array();
		$ruta			=	$this->getRuta();
		if ($handle = @opendir($ruta)) {
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

	function getContenido($archivo,$usr_linux){
		return file_get_contents ($this->getRuta().SEPARADOR_RUTA.$archivo);
	}
	function eliminar($archivo,$usr_linux){
		$salida = ejecutar_servidor("rm ".$this->getRuta().SEPARADOR_RUTA.$archivo,$usr_linux);
		if($salida=="") return array("error"=>0);
		else{
			error_log($salida);
			 return array("error"=>1,
    			 		  "codError"=>"D003");
			}
		}
	function ejecutar($archivo,$argumentos,$id_cliente,$id_trabajo){
		$conexion= new Conexion(CONEXION_HOST,CONEXION_PORT,CONEXION_USUARIO,CONEXION_PASSWORD,CONEXION_BASE);

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

	function crearCarpeta($nombre,$usr_linux){
		$nombre = trim($nombre);
		if($nombre == "") return array("error"=>0,
    				 	 			   "codError"=>"D102");
		$ruta = $this->getRuta();
		$res = ejecutar_servidor("mkdir $ruta/$nombre",$usr_linux);
		if($res == ""){
			return array("error"=>0);
		}else return array("error"=>0,
    				 	 "codError"=>"D103");
	}
	function comprimir($usr_linux,$id_cliente,$id_trabajo){
		$ruta = $this->getRuta();
		$nombre = 'pgccadar_zip_'.$id_cliente.'.zip';
		$nombre_archivo = TMP.'/'.$nombre;
		@unlink($nombre_archivo);
		@rmdir($nombre_archivo);
		$comando = "zip -r ".RAIZ_SISTEMA.'/'.$id_cliente.'/'.$id_trabajo.'/'.$nombre." ".$ruta."/*";
		$res = ejecutar_servidor($comando,$usr_linux);
    	$comando = "chmod 777 ".RAIZ_SISTEMA.'/'.$id_cliente.'/'.$id_trabajo.'/'.$nombre." .";
		$res = ejecutar_servidor($comando,$usr_linux);
error_log($comando);
error_log($res);
		if($res != ""){
			$comando = "rm ".RAIZ_SISTEMA.'/'.$id_cliente.'/'.$id_trabajo.'/'.$nombre;
			$res = ejecutar_servidor($comando,$usr_linux);
			return array("error"=>1,
					 	 "codError"=>"D201");
		}

		$comando = "mv ".RAIZ_SISTEMA.'/'.$id_cliente.'/'.$id_trabajo.'/'.$nombre." ".$nombre_archivo;
		$res = ejecutar_servidor($comando,$usr_linux);
		if($res != ""){
			$comando = "rm ".RAIZ_SISTEMA.'/'.$id_cliente.'/'.$id_trabajo.'/'.$nombre;
			$res = ejecutar_servidor($comando,$usr_linux);
			return array("error"=>1,
					 	 "codError"=>"D202");
		}
		return array("error"=>0,"archivo"=>$nombre_archivo);

	}
	function eliminarCarpeta($nombre,$usr_linux,$recursivo = false){
		$nombre = trim($nombre);
		if($nombre == "") return array("error"=>0,
    				 	 			   "codError"=>"D102");
		$ruta = $this->getRuta();

		$command = "rmdir $ruta/$nombre";
		if($recursivo) $command = "rmdir -r $ruta/$nombre";
		$res = ejecutar_servidor($command,$usr_linux);

		if($res == ""){
			return array("error"=>0);
		}else return array("error"=>0,
    				 	 "codError"=>"D103");
	}

	function getRuta(){
		return implode(SEPARADOR_RUTA,$this->camino);
	}

	function descomprimir($nombre_archivo,$usr_linux){
		//ejecutamos el comando unzip del sistema para descomprimir el fichero zip
		$ruta=$this->getRuta();
		$nombre_carpeta = array_shift(explode(".",$nombre_archivo));
		$res = $this->crearCarpeta($nombre_carpeta,$usr_linux);
		if($res["error"]) return $res;

		$command = "unzip $ruta/$nombre_archivo -d $ruta/$nombre_carpeta";
		$rs = ejecutar_servidor($command,$usr_linux);

		if($rs){
			$command = "rm $ruta/$nombre_archivo";
			$rs = ejecutar_servidor($command,$usr_linux);
			return array("error"=>0);
		}
		else{
			$rs = `$command`;
			error_log($rs);
			return array("error"=>1,
    				 	 "codError"=>"D104");

		}
	}


}


?>