<?php
include_once ("Constantes.php");
include_once ("Conexion.php");
include_once ("Alertas.php");
include_once ("Servidor.php");

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
		$consulta = "select usr_linux from cliente where id=?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_cliente),false))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$row=$this->db->Next();
		$usr_linux=$row["usr_linux"];

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
		$consulta = "insert into ejecucion (archivo,ruta,argumentos,trabajo) values(?,?,?,?)";
		if(!$this->db->EjecutarConsulta($consulta,array($archivo,$ruta,$argumentos,$id_trabajo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$id =$this->db->getUltimoNumerador();

		$archivo_salida = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'salida_'.$id;
		$archivo_error = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'error_'.$id;
		$salida = ejecutar_servidor("touch $archivo_salida",$usr_linux);
		if($salida !="") error_log($salida);
		$salida = ejecutar_servidor("chmod 777 $archivo_salida",$usr_linux);
		if($salida !="") error_log($salida);
		$salida = ejecutar_servidor("touch $archivo_error",$usr_linux);
		if($salida !="") error_log($salida);
		$salida = ejecutar_servidor("chmod 777 $archivo_error",$usr_linux);
		if($salida !="") error_log($salida);

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

		$script  = TMP.'/'.'PGCCADAR_'.$id;
		$fscript = fopen($script,'w+');
		if($fscript ==NULL) error_log("ERROR1");
		$caracteres = fwrite  ($fscript, $ejecutar);
		fclose($fscript);
		chmod($script,0777);

		$salida = ejecutar_servidor("cp ".TMP.'/'.'PGCCADAR_'.$id.
									" ".RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'ejecutable_'.$id,$usr_linux);
		if($salida !="") error_log($salida);
		$script = RAIZ.'/'.$id_cliente.'/'.$id_trabajo.'/'.'ejecutable_'.$id;
		$salida = ejecutar_servidor("cd $ruta; ".QSUB." $script",$usr_linux);
		if($salida !="") error_log($salida);

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

		$salida = ejecutar_servidor("touch $archivo_salida",$usr_linux);
		if($salida !="") error_log($salida);

		$salida = ejecutar_servidor("chmod 0666 $archivo_salida",$usr_linux);
		if($salida !="") error_log($salida);
		return array("error"=>0,"id"=>$id,"id_trabajo"=>$id_torque,"salida"=>$archivo_salida);
	}

	function crearDirCliente($idCliente,$usr_linux){
		$ruta =RAIZ;
		$ejecutar =SSH." -l ".USERNAME." ".HOST." \"cd $ruta; mkdir $idCliente; chown $usr_linux $idCliente; exit\" 2>&1";
		$salida = `$ejecutar`;

		if($salida=="")
			return array("error"=>0);
		else{
			error_log($salida);
			return array("error"=>1,"codError"=>"M101");
		}
	}
	function crearDirTrabajo($idCliente,$idTrabajo){
		$consulta = "select usr_linux from cliente where id=?";
		if(!$this->db->EjecutarConsulta($consulta,array($idCliente),false))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}
		$row=$this->db->Next();
		$usr_linux=$row["usr_linux"];

		$ruta =RAIZ.'/'.$idCliente;
		$salida = exec(SSH." -l ".$usr_linux." ".HOST." 'cd $ruta; mkdir $idTrabajo; exit'");
		if($salida=="")
			return array("error"=>0);
		else{
			error_log($salida);
			return array("error"=>1,"codError"=>"M100");
		}
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
		//error_log("poner de vuelta despues");
		$salida = ejecutar_servidor("rm $script");
		if($salida==""){
			//envio alerta de finalizacion
			$consulta2 = "select trabajo from ejecucion where id_torque = ?";
			if(!$this->db->EjecutarConsulta($consulta2,array($id_torque),true))
				{
				return array("error"=>1,
							 "codError"=>"EA09");
				}
			$row=$this->db->Next();
			$trabajo = $row["trabajo"];

			$alertas = new Alertas($this->db);
			$result = $alertas->asignarAlertaTrabajoFin($trabajo);
			if($result["error"] = 1){
				return $result;
			}
			return array("error"=>0);
		}
		else
			return array("error"=>1,"codError"=>"M001");

	}

	function setIniciado($id_torque){
		$consulta = "update ejecucion set fecha_ejecucion=CURRENT_TIMESTAMP where id_torque = ?";
		if(!$this->db->EjecutarConsulta($consulta,array($id_torque),true))
			{
			return array("error"=>1,
						 "codError"=>$this->db->msgError);
			}

		//envio alerta de inicio
		$consulta2 = "select trabajo from ejecucion where id_torque = ?";
		if(!$this->db->EjecutarConsulta($consulta2,array($id_torque),true))
			{
			return array("error"=>1,
						 "codError"=>"EA09");
			}
		$row=$this->db->Next();
		$trabajo = $row["trabajo"];

		$alertas = new Alertas($this->db);
		$result = $alertas->asignarAlertaTrabajoInicio($trabajo);
		if($result["error"] = 1){
			return $result;
		}

		return array("error"=>0);
	}

	function getEnEjecucion($id){
		$consulta = "select id, id_torque, archivo, ruta, parametros, argumentos " .
					"from ejecucion " .
					"where id=? " .
					"  and fecha_ejecucion is not null" .
					"  and fecha_fin is null";
		if(!$this->db->EjecutarConsulta($consulta,array($id),true))
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
	function getCantEnEjecucion($id_trabajo){
		$consulta = "select count(*) as cantidad " .
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
		$row=$this->db->Next();
		return array("error"=>0,
					 "cantidad"=>$row["cantidad"]);
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