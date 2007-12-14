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
		$consulta = "select nombre,nodos,tiempo_maximo,cola from trabajo where id = ?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_trabajo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$trabajo = $this->db->Next();
		if($trabajo == null){
			return array("error"=>1,
						 "codError"=>"M000");
		}

		$consulta = "insert into ejecucion (archivo,ruta,parametros,argumentos) values(?,?,?,?)";
		if(!$this->db->EjecutarConsulta($consulta,array($archivo,$ruta,$parametros,$argumentos),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$id =$this->db->getUltimoNumerador();

		$archivo_salida = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'salida_'.$id;
		$archivo_error = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'error_'.$id;
		touch($archivo_salida);
		$ejecutar = $plantilla->replace($plantilla->load(EJECUTABLE),
										array("PBS_0"=>$trabajo["nombre"],
											  "PBS_1"=>$trabajo["nodos"],
											  "PBS_2"=>$trabajo["tiempo_maximo"],
											  "PBS_3"=>$trabajo["cola"],
											  "PBS_4"=>$archivo_salida,
											  "PBS_5"=>$archivo_error,
											  "0"=>$archivo,
											  "1"=>$ruta,
											  "2"=>$argumentos,
											  "3"=>REDIRECCION_SALIDA,
											  "4"=>$ruta.'/'.OUTPUT));
		$salida = exec("cd ".$ruta."; echo \"".$ejecutar."\" | ".QSUB);
		error_log("\ncd ".$ruta."; echo '".$ejecutar."' | ".QSUB,3,LOG_EJECUCIONES);
		$salida = $this->parsear_salida($salida);
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
