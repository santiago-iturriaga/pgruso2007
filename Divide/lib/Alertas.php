<?php
include_once("Conexion.php");
include_once("Alerta.php");

class Alertas{
var $conexion	=	null;

function Usuarios($bd){
		$this->conexion=$bd;
	}

function crearAlerta($idTrabajo,$idUsuario, $asunto,$body  ){
	$consulta="insert into alertas (usuario,trabajo,asunto,body) values (?,?,?,?)";

	if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario,$idTrabajo,$asunto,$body),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
	$idAlerta =$this->conexion->getUltimoNumerador();
	return array("error"=>0,"id"=>$idAlerta);
}

function getAlertasUsuarioTrabajo($idUsuario,$idTrabajo){
		$consulta= "select id,usuario, trabajo, asunto,body, leida
				    from alertas
				    where usuario=? and trabajo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=array("id"=>$row["id"],"usuario"=>$row["usuario"],"trabajo"=>$row["trabajo"],"asunto"=>$row["asunto"],"body"=>$row["body"],"leida"=>$row["leida"]);
			}
		return array("error"=>0,"alerta"=>$salida);

	}

function marcarAlertaLeida($idAlerta){
	$consulta= "UPDATE alertas SET leida = 1
				     where id=?";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idAlerta),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

}
?>