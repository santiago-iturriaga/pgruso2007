<?php
include_once ("Constantes.php");
include_once ("Conexion.php");

class Momento{
	var $db = null;

	function Momento($db=null){
		$this->db = $db;
	}

	function setDB($db){
		$this->db = $db;
	}

	function parsear_salida($salida){
		if(ereg("([0-9]+)\.([a-zA-Z0-9]+)",$salida,$array))
			return array("id"=>$array[0],"maquina"=>$array[1]);
		else
			return $salida;
	}

	function ejecutar($archivo,$ruta,$parametros,$argumentos,$id_cliente,$id_trabajo){
		$plantilla	=	new TPL();

		$consulta = "insert into ejecucion (archivo,ruta,parametros,argumentos) values(?,?,?,?)";
		if(!$this->db->EjecutarConsulta($consulta,array($archivo,$ruta,$parametros,$argumentos),false))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$id =$this->db->getUltimoNumerador();


		$ejecutar = $plantilla->replace($plantilla->load("plantillas/archivos/ejecutar.sh"),
										array("NODOS"=>1,
											  "RUTA_EJECUTABLE"=>$ruta,
											  "EJECUTABLE"=>$archivo,
											  "SALIDA"=>RAIZ.PATH_SEPARATOR.$id_cliente.PATH_SEPARATOR.$id_trabajo.PATH_SEPARATOR.'salida_'.$id));
		$salida = exec("cd ".$ruta."; echo \"".$ejecutar."\" | ".QSUB);
		$salida = $this->parsear_salida($salida);
		error_log(print_r($salida,1));
		if(!isset($salida["id"])) return array("error"=>1);
		$id_torque = $salida["id"];

		$consulta = "updata ejecucion set id_torque=? where id=?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque,$id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}

		return array("error"=>0,"id"=>$id);
	}

	function setFinalizado($id_torque){
		$consulta = "update ejecucion set fecha_fin=CURRENT_TIMESTAMP where id_torque = ?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque),false))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		return array("error"=>0);

	}
}
?>
