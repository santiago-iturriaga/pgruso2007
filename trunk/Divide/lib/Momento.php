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
		return array("id"=>array_shift($salida),"maquina"=>implode(".",$salida));

	}

	function ejecutar($archivo,$ruta,$argumentos,$id_cliente,$id_trabajo){

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

		//$consulta = "insert into ejecucion (archivo,ruta,argumentos,trabajo) values(?,?,?,?,?)";
		$consulta = "insert into ejecucion (id,archivo,ruta,argumentos,trabajo) values((select max(id)+1 from ejecucion),?,?,?,?,?)";
		if(!$this->db->EjecutarConsulta($consulta,array($archivo,$ruta,$argumentos,$id_trabajo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$id =$this->db->getUltimoNumerador();

		$archivo_salida = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'salida_'.$id;
		$archivo_error = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'error_'.$id;

		touch($archivo_salida);
		if(!chmod($archivo_salida,0777)) error_log("no funco el chmod");
		touch($archivo_error);
		chmod($archivo_error,0777);

		$ejecutar = $plantilla->replace($plantilla->load(EJECUTABLE),
										array("MPIEXEC"=>MPIEXEC,
											  "PBS_0"=>$trabajo["nombre"],
											  "PBS_1"=>$trabajo["nodos"],
											  "PBS_2"=>$trabajo["tiempo_maximo"],
											  "PBS_3"=>$trabajo["cola"],
											  "PBS_4"=>$archivo_salida,
											  "PBS_5"=>$archivo_error,
											  "0"=>$archivo,
											  "1"=>$ruta,
											  "2"=>$argumentos,
											  "3"=>REDIRECCION_SALIDA,
											  "4"=>$ruta.'/'.OUTPUT."_".$id));
		$script = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'ejecutable_'.$id;
		$fscript = fopen($script,'w+');
		if($fscript ==NULL) error_log("ERROR1");
		$caracteres = fwrite  ($fscript, $ejecutar);
		fclose($fscript);
		chmod($script,0777);

		$salida = exec("ssh -l pgccadar lennon.fing.edu.uy 'cd $ruta; ".QSUB." $script; exit'");

		$salida = $this->parsear_salida($salida);
		if(!isset($salida["id"])) return array("error"=>1);
		$id_torque = $salida["id"];
		$consulta = "update ejecucion set id_torque=? where id=?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque,$id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$archivo_salida = $ruta.'/'.OUTPUT."_".$id;
		touch($archivo_salida);
		chmod($archivo_salida,0666);
		return array("error"=>0,"id"=>$id,"id_trabajo"=>$id_torque,"salida"=>$archivo_salida);
	}

	function setFinalizado($id_torque){
		$consulta = "update ejecucion set fecha_fin=CURRENT_TIMESTAMP where id_torque = ?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$consulta = "select t.id as id_trabajo," .
					"		t.cliente as id_cliente," .
					"	    e.id as id_ejecutable " .
					"from trabajo t, ejecucion e " .
					"where e.id_torque=? " .
					"  and e.trabajo=t.id";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$row=$this->db->Next();
		$script = RAIZ.'/'.$row["id_cliente"].'/'.$row["id_trabajo"].'/'.'ejecutable_'.$row["id_ejecutable"];
		error_log("poner de vuelta despues");
		//if(unlink($script))
			return array("error"=>0);
		//else
			//return array("error"=>0,"codError"=>"M001");

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
	function getEnEjecucion($id_trabajo){
		$consulta = "select id, id_torque, archivo, ruta, parametros, argumentos " .
					"from ejecucion " .
					"where trabajo=? " .
					"  and fecha_ejecucion is not null" .
					"  and fecha_fin is null";
		if(!$this->db->EjecutarConsulta($consulta,array($id_trabajo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$salida=array();
		while(($row=$this->db->Next()) != null)
			{
			$salida[$row["id"]]=$row;
			}
		return array("error"=>0,
					 "salida"=>$salida);
	}

	function getFinalizado($id){
		$consulta = "select fecha_fin " .
					"from ejecucion " .
					"where id=? ";
		if(!$this->db->EjecutarConsulta($consulta,array($id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$row=$this->db->Next();
		return array("error"=>0,
					 "salida"=>$row["fecha_fin"]);
	}

	function getSalida($id,$error=false){
		$consulta = "select t.id as id_trabajo," .
					"		t.cliente as id_cliente " .
					"from trabajo t, ejecucion e " .
					"where e.id=? " .
					"  and e.trabajo=t.id";
		if(!$this->db->EjecutarConsulta($consulta,array($id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$row=$this->db->Next();
		$salida="";
		if(!$error){
			$archivo_salida = RAIZ.'/'.$row["id_cliente"].'/'.$row["id_trabajo"].'/'.'salida_'.$id;
			$salida= file_get_contents  ($archivo_salida);
		}
		else{
			$archivo_error = RAIZ.'/'.$row["id_cliente"].'/'.$row["id_trabajo"].'/'.'error_'.$id;
			$salida= file_get_contents  ($archivo_error);
		}
		if($salida===false)
			return array("error"=>1,"codError"=>"M002");
		else
			return array("error"=>0,"archivo"=>$salida);
	}

	function getEjecuciones($id_trabajo){
		$consulta = "select id, id_torque, archivo, fecha_ini, fecha_ejecucion, fecha_fin " .
					"from ejecucion " .
					"where trabajo=? ";
		if(!$this->db->EjecutarConsulta($consulta,array($id_trabajo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$salida=array();
		while(($row=$this->db->Next()) != null)
			{
			$salida[$row["id"]]=$row;
			}
		return array("error"=>0,
					 "salida"=>$salida);
	}

}
?>