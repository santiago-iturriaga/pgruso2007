<?php
include_once("Conexion.php");
//include_once("Alerta.php");

class Alertas{
var $conexion	=	null;

function Alertas($bd){
		$this->conexion=$bd;
	}

function crearAlerta($asunto,$body  ){
	$consulta="insert into alertas (asunto,body) values (?,?)";

	if(!$this->conexion->EjecutarConsulta($consulta,array($asunto,$body),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
	$idAlerta =$this->conexion->getUltimoNumerador();
	return array("error"=>0,"id"=>$idAlerta);
}

function getAlertasUsuarioTrabajo($idUsuario,$idTrabajo){
		$consulta= "select a.id,ua.usuario, ua.trabajo, a.asunto, ta.body, ua.leida, ua.fecha
				    from alertas a, usuario_alerta ua, trabajo_alerta ta
				    where ua.usuario=? and ua.trabajo=? and a.id = ua.alerta and ua.trabajo= ta.trabajo and ua.alerta = ta.alerta";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=array("id"=>$row["id"],"usuario"=>$row["usuario"],"trabajo"=>$row["trabajo"],"asunto"=>$row["asunto"],"body"=>$row["body"],"leida"=>$row["leida"],"fecha"=>$row["fecha"]);
			}
		return array("error"=>0,"alertas"=>$salida);
	}


	function getAlerta($idUsuario,$idTrabajo,$idAlerta){
		$consulta= "select a.id,ua.usuario, ua.trabajo, a.asunto, ta.body, ua.leida, ua.fecha
				    from alertas a, usuario_alerta ua, trabajo_alerta ta
				    where ua.usuario=? and ua.trabajo=? and ua.alerta = ?  and a.id = ua.alerta and ua.trabajo= ta.trabajo and ua.alerta = ta.alerta";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo, $idAlerta),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=array("id"=>$row["id"],"usuario"=>$row["usuario"],"trabajo"=>$row["trabajo"],"asunto"=>$row["asunto"],"body"=>$row["body"],"leida"=>$row["leida"],"fecha"=>$row["fecha"]);
			}
		return array("error"=>0,"alerta"=>$salida);

	}

function marcarAlertaLeida($idUsuario,$idTrabajo,$idAlerta){
	$consulta= "UPDATE usuario_alerta SET leida = 1
				     where usuario=? and trabajo=? and alerta=?";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo, $idAlerta),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

function deleteAlerta($idUsuario,$idTrabajo,$idAlerta){
	$consulta= "DELETE FROM usuario_alerta WHERE usuario=? and trabajo=? and alerta = ?";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo, $idAlerta),true))
			{
			return array("error"=>1,
				codError=>$this->conexion->msgError);
			}

	$consulta2	= "SELECT count(alerta) FROM usuario_alerta WUERE usuario=? and trabajo=? and alerta = ?";
	if(!$this->conexion->EjecutarConsulta($consulta2,array($idUsuario, $idTrabajo, $idAlerta),true))
			{
			return array("error"=>1,
				codError=>$this->conexion->msgError);
			}
	else{

		$consulta3 ="DELETE FROM trabajo_alerta WHERE trabajo=? and alerta = ?";
		if(!$this->conexion->EjecutarConsulta($consulta3,array($idTrabajo, $idAlerta),true))
			{
			return array("error"=>1,
				codError=>$this->conexion->msgError);
			}
	}
	return array("error"=>0);
	}

function cantidadAlertasNoLeida($idUsuario,$idTrabajo){
	$consulta= "Select count(alerta) as cantidad FROM usuario_alerta WHERE usuario=? and trabajo=? and leida = 0";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
			$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=array("id"=>$row["id"],"cantAlertas"=>$row["cantidad"]);
			}
		return array("error"=>0,"alerta"=>$salida);
	}

}
?>