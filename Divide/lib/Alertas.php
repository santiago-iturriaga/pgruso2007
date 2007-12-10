<?php
include_once("Conexion.php");
include_once("Alerta.php");

class Alertas{
var $conexion	=	null;

function Usuarios($bd){
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
		$consulta= "select a.id,ua.usuario, ua.trabajo, a.asunto,a.body, ua.leida, ua.fecha
				    from alertas a, usuario_alerta ua
				    where ua.usuario=? and ua.trabajo=? and ua.leida = 0 and a.id = ua.alerta";
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
		return array("error"=>0,"alerta"=>$salida);

	}

function marcarAlertaLeida($idAlerta){
	$consulta= "UPDATE usuario_alerta SET leida = 1
				     where alerta=?";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idAlerta),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

function deleteAlertaLeida($idAlerta){
	$consulta= "DELETE FROM uasuario_alerta WHERE alerta = ?";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idAlerta),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

}
?>