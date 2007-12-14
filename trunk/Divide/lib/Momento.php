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
		$salida = explode(".",$salida);
		return array("id"=>$salida[0],"maquina"=>$salida[1]);

	}

	function ejecutar($archivo,$ruta,$parametros,$argumentos,$id_cliente,$id_trabajo){
		$plantilla	=	new TPL();

		$consulta = "insert into ejecucion (archivo,ruta,parametros,argumentos) values(?,?,?,?)";
		if(!$this->db->EjecutarConsulta($consulta,array($archivo,$ruta,$parametros,$argumentos),true))
			{echo "<pre>";print_r($this->db);echo '</pre>';
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$id =$this->db->getUltimoNumerador();

		$archivo_salida = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'salida_'.$id;
		touch($archivo_salida);
		$ejecutar = $plantilla->replace($plantilla->load("plantillas/archivos/ejecutar.sh"),
										array("NODOS"=>1,
											  "RUTA_EJECUTABLE"=>$ruta,
											  "EJECUTABLE"=>$archivo,
											  "SALIDA"=>$archivo_salida));
		$salida = exec("cd ".$ruta."; echo \"".$ejecutar."\" | ".QSUB);
		$salida = $this->parsear_salida($salida);
		error_log("Momento.php SALIDA:".print_r($salida,1));
		if(!isset($salida["id"])) return array("error"=>1);
		$id_torque = $salida["id"];

		$consulta = "update ejecucion set id_torque=? where id=?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque,$id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}

		return array("error"=>0,"id"=>$id,"id_trabajo"=>$id_torque,"salida"=>$archivo_salida);
	}

	function setFinalizado($id_torque){
		$consulta = "update ejecucion set fecha_fin=CURRENT_TIMESTAMP where id_torque = ?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		return array("error"=>0);

	}

	function setIniciado($id_torque){
		$consulta = "update ejecucion set fecha_ejecucion=CURRENT_TIMESTAMP where id_torque = ?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		return array("error"=>0);

	}

}
?>
