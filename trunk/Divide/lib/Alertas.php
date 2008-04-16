<?php
include_once("Conexion.php");
include_once("TPL.php");


class Alertas{
var $conexion	=	null;

function Alertas($bd){
		$this->conexion=$bd;
	}

function crearAlerta($asunto,$body  ){
	$consulta="insert into alertas (asunto,body) values (?,?)";

	if(!$this->conexion->EjecutarConsulta($consulta,array($asunto,$body),false))
			{
			return array("error"=>1, "codError"=>"EA07");
			}
	$idAlerta =$this->conexion->getUltimoNumerador();
	return array("error"=>0,"id"=>$idAlerta);
}

function getAlertasUsuarioTrabajo($idUsuario,$idTrabajo){

		$consulta= "select a.id,ua.usuario, ua.trabajo, a.asunto, ta.body, ua.leida, ua.fecha, ua.idua
				    from alertas a, usuario_alerta ua, trabajo_alerta ta
				    where ua.usuario=? and ua.trabajo=? and a.id = ua.alerta and ua.trabajo= ta.trabajo and ua.alerta = ta.alerta";

		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo),true))
			{
			return array("error"=>1, "codError"=>"EA06");
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["idua"]]=array("id"=>$row["id"],"usuario"=>$row["usuario"],"trabajo"=>$row["trabajo"],"asunto"=>$row["asunto"],"body"=>$row["body"],"leida"=>$row["leida"],"fecha"=>$row["fecha"],"idua"=>$row["idua"]);
			}

		return array("error"=>0,"alertas"=>$salida);
	}

	function getAlertasUsuario($idUsuario){
		$consulta= "select a.id,ua.usuario, ua.trabajo, a.asunto, ta.body, ua.leida, ua.fecha, t.cliente, ua.idua, c.nombre as nombre_cliente,t.nombre as nombre_trabajo from alertas a, usuario_alerta ua, trabajo_alerta ta, trabajo t, cliente c where ua.usuario=? and a.id = ua.alerta and ua.alerta = ta.alerta and t.id = ua.trabajo and t.cliente=c.id";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario),true))
			{
			return array("error"=>1, "codError"=>"EA06");
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["idua"]]=array("id"=>$row["id"],"usuario"=>$row["usuario"],"trabajo"=>$row["trabajo"],"asunto"=>$row["asunto"],"body"=>$row["body"],"leida"=>$row["leida"],"fecha"=>$row["fecha"], "cliente"=>$row["cliente"],"idua"=>$row["idua"], "nombre_cliente"=>$row["nombre_cliente"], "nombre_trabajo"=>$row["nombre_trabajo"]);
			}
		return array("error"=>0,"alertas"=>$salida);
	}


	function getAlerta($idUsuario,$idTrabajo,$idAlerta,$idUsuarioAlerta){
		$consulta= "select a.id,ua.usuario, ua.trabajo, a.asunto, ta.body, ua.leida, ua.fecha, t.cliente, ua.idua
				    from alertas a, usuario_alerta ua, trabajo_alerta ta, trabajo t
				    where ua.usuario=? and ua.trabajo=? and ua.alerta = ? and ua.idua = ? and a.id = ua.alerta and ua.trabajo= ta.trabajo and ua.alerta = ta.alerta and t.id = ta.trabajo";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo, $idAlerta,$idUsuarioAlerta),true))
			{
			return array("error"=>1, "codError"=>"EA03");
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=array("id"=>$row["id"],"usuario"=>$row["usuario"],"trabajo"=>$row["trabajo"],"asunto"=>$row["asunto"],"body"=>$row["body"],"leida"=>$row["leida"],"fecha"=>$row["fecha"],"cliente"=>$row["cliente"],"idua"=>$row["idua"]);
			}
		return array("error"=>0,"alerta"=>$salida);
	}

function marcarAlertaLeida($idUsuario,$idTrabajo,$idAlerta,$idUsuarioAlerta){
	$consulta= "UPDATE usuario_alerta SET leida = 1
				     where usuario=? and trabajo=? and alerta=? and idua = ?";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo, $idAlerta,$idUsuarioAlerta),true))
			{
			return array("error"=>1, "codError"=>"EA04");
			}
		return array("error"=>0);
	}

function deleteAlerta($idUsuario,$idTrabajo,$idAlerta,$idUsuarioAlerta){
	$consulta= "DELETE FROM usuario_alerta WHERE usuario=? and trabajo=? and alerta = ? and idua = ?";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo, $idAlerta,$idUsuarioAlerta),true))
			{
			return array("error"=>1, "codError"=>"EA02");
			}

	$consulta2	= "SELECT count(alerta) as cantidad FROM usuario_alerta WHERE  trabajo=? and alerta = ?";
	if(!$this->conexion->EjecutarConsulta($consulta2,array($idTrabajo, $idAlerta),true))
			{
			return array("error"=>1,"codError"=>"EA02");
			}
	else{
			$resCant = 	$this->conexion->Next();
			$cant = $resCant["cantidad"];

			if($cant == 0){

				$consulta3 ="DELETE FROM trabajo_alerta WHERE trabajo=? and alerta = ?";
				if(!$this->conexion->EjecutarConsulta($consulta3,array($idTrabajo, $idAlerta),true))
					{
					return array("error"=>1, "codError"=>"EA02");
					}

			}
	}
	return array("error"=>0);
	}

function cantidadAlertasNoLeida($idUsuario,$idTrabajo){
	$consulta= "Select count(alerta) as cantidad FROM usuario_alerta WHERE usuario=? and trabajo=? and leida = 0";
	if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idTrabajo),true))
			{
			return array("error"=>1, "codError"=>"EA05");
			}
			$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=array("id"=>$row["id"],"cantAlertas"=>$row["cantidad"]);
			}
		return array("error"=>0,"alerta"=>$salida);
	}

	function enviarAlerta($idUsuario,$idTrabajo,$idAlerta,$idUsuarioAlerta){

		$consulta= "select  a.asunto as asunto, ta.body as body, u.email as email
				    from alertas a, usuario u, trabajo_alerta ta, usuario_alerta ua
				    where u.id = ? and  a.id = ? and   ta.alerta = a.id and ta.trabajo = ? and ua.idua = ? ";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario, $idAlerta, $idTrabajo,$idUsuarioAlerta),true))
			{

			return array("error"=>1, "codError"=>$this->conexion->msgError);
			}

		$salida=array();
		while(($row=$this->conexion->Next()) != null){
			$salida = array("id"=>$row["id"],"asunto"=>$row["asunto"],"body"=>$row["body"],"email"=>$row["email"]);
		}
		$mensaje = wordwrap($salida["body"], 70);

		if(!mail($salida["email"], $salida["asunto"], $mensaje )){
			return array("error"=>1, "codError"=>"EA01");
		}

		return array("error"=>0);
	}

	function asignarAlertaTrabajoInicio($idTrabajo){
		$consulta= "select distinct ug.usuario from usuario_grupo as ug, trabajo_grupo as tg  where tg.trabajo = ? and tg.grupo = ug.grupo";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idTrabajo),true))
			{
				return array("error"=>1, "codError"=>"EA10");
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null){
			array_push($salida,$row["usuario"]);
		}
		foreach ($salida as $idU){
			$params = array("TRABAJO"=>$idTrabajo);
			$this->asignarAlerta($idU,$idTrabajo,3,$params);
		}
		return array("error"=>0);

	}

	function asignarAlertaTrabajoFin($idTrabajo){
		$consulta= "select distinct ug.usuario from usuario_grupo as ug, trabajo_grupo as tg  where tg.trabajo = ? and tg.grupo = ug.grupo";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idTrabajo),true)){
			return array("error"=>1, "codError"=>"EA10");
		}

		$salida=array();
		while(($row=$this->conexion->Next()) != null){
			array_push($salida,$row["usuario"]);
		}

		foreach ($salida as $idU){
			$params = array("TRABAJO"=>$idTrabajo);
			$this->asignarAlerta($idU,$idTrabajo,4,$params);
		}

		return array("error"=>0);

	}


	function asignarAlertaTrabajoCancelado($idTrabajo){
		$consulta= "select distinct ug.usuario from usuario_grupo as ug, trabajo_grupo as tg  where tg.trabajo = ? and tg.grupo = ug.grupo";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idTrabajo),true))
			{
				return array("error"=>1, "codError"=>"EA10");
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null){
			array_push($salida,$row["usuario"]);
		}
		foreach ($salida as $idU){
			$params = array("TRABAJO"=>$idTrabajo);
			$this->asignarAlerta($idU,$idTrabajo,5,$params);
		}
		return array("error"=>0);

	}

	function asignarAlerta($idUsuario,$idTrabajo,$idAlerta, $textVar){
		$consulta= "select asunto, body from alertas where id = ?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idAlerta),true))
			{
				return array("error"=>1, "codError"=>"EA03");
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null){
			$salida = array("id"=>$row["id"],"asunto"=>$row["asunto"],"body"=>$row["body"]);
		}
		$mensaje = $salida["body"];
		if($textVar != null){
		$plantilla	=	new TPL();
		$mensaje = 	$plantilla->replace($mensaje,$textVar);
		}

		$consulta2= "insert into trabajo_alerta select ?,?,? where not exists (select alerta, trabajo from trabajo_alerta where alerta = ? and trabajo = ?)";
		if(!$this->conexion->EjecutarConsulta($consulta2,array($idAlerta, $idTrabajo, $mensaje, $idAlerta, $idTrabajo),true))
			{
				return array("error"=>1, "codError"=>"EA08");
			}

		//$consulta3= "insert into usuario_alerta select ?,?,? where not exists (select alerta, usuario, trabajo from usuario_alerta where alerta = ? and usuario= ? and trabajo = ?)";
		$consulta3= "insert into usuario_alerta (alerta, usuario, trabajo) values (?,?,?)";

		if(!$this->conexion->EjecutarConsulta($consulta3,array($idAlerta, $idUsuario, $idTrabajo),true))
			{
				return array("error"=>1, "codError"=>"EA09");
			}

		return array("error"=>0);
	}

}


?>