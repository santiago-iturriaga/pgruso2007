<?php
include_once("Conexion.php");
class Usuarios{
	var $conexion	=	null;

	function Usuarios($bd){
		$this->conexion=$bd;
	}

	function altaGrupo($nombre){
		$consulta="insert into grupo (nombre) values (?)";
		if(!$this->conexion->EjecutarConsulta($consulta,array($nombre),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$idGrupo =$this->conexion->getUltimoNumerador();
		return array("error"=>0,"id"=>$idGrupo);
	}

	function asociarTrabajoGrupo($idTrabajo,$idGrupo){
		$consulta="insert into trabajo_grupo (trabajo,grupo) values (?,?)";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idTrabajo,$idGrupo),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

	function desasociarTrabajoGrupo($idTrabajo,$idGrupo){
		$consulta="delete from trabajo_grupo where trabajo=? and grupo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idTrabajo,$idGrupo),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}


	function altaUsuario($login,$clave,$administrador='N',$email="",$idGrupos=array()){
		$consulta="insert into usuario (login,password,administrador,email) values (?,?,?,?)";
		if(!$this->conexion->EjecutarConsulta($consulta,array($login,$clave,$administrador,$email),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$idUsuario =$this->conexion->getUltimoNumerador();
		foreach($idGrupos as $grupo){
			$res = $this->asociarUsuarioGrupo($idUsuario,$grupo);
			if($res["error"]) return $res;
		}
		return array("error"=>0,"id"=>$idUsuario);
	}

	function bajaUsuario($id){
		$consulta="delete from usuario_grupo where usuario=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$consulta="delete from usuario where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}
	function asociarUsuarioGrupo($idUsuario,$idGrupo){
		$consulta="insert into usuario_grupo (usuario,grupo) values (?,?)";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario,$idGrupo),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

	function getUsuarios($idGrupos = array()){
		$consulta= "select * from usuario u";
		if(count($idGrupos)>0){
			$consulta.=" where exists (select id,login
									   from usuario_grupo
									   where usuario = u.id
									   	 and grupo in (".implode($idGrupos,",")."))";
		}
		if(!$this->conexion->EjecutarConsulta($consulta,array(),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=array("login"=>$row["login"],"email"=>$row["email"]);
			}
		return array("error"=>0,"usuarios"=>$salida);
	}

	function getGrupos(){
		$consulta= "select * from grupo";
		if(!$this->conexion->EjecutarConsulta($consulta,array(),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row["nombre"];
			}
		return array("error"=>0,"grupos"=>$salida);
	}

	function getTrabajosGrupo($idGrupo){
		$consulta= "select t.* from trabajo t, trabajo_grupo tg where t.id=tg.trabajo and tg.grupo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idGrupo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row["nombre"];
			}
		return array("error"=>0,"trabajos"=>$salida);
	}

	function getTrabajos(){
		$consulta= "select t.* from trabajo a";
		if(!$this->conexion->EjecutarConsulta($consulta,array(),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row;
			}
		return array("error"=>0,"trabajo"=>$salida);
	}

	function getUsuariosGrupo($idGrupo){
		$consulta= "select u.* from usuario u, usuario_grupo ug where u.id=ug.usuario and ug.grupo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idGrupo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row["login"];
			}
		return array("error"=>0,"usuarios"=>$salida);
	}

	function getGruposUsuario($idUsuario){
		$consulta= "select g.* from grupo g, usuario_grupo ug where ?=ug.usuario and ug.grupo=g.id";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row["nombre"];
			}
		return array("error"=>0,"grupos"=>$salida);
	}

	function getTrabajosUsuario($idUsuario){
		$consulta= "select t.id, t.nombre as trabajo, c.id as id_cliente, c.nombre as cliente ".
				   "from trabajo t, trabajo_grupo tg, usuario_grupo ug,cliente c ".
				   "where t.id=tg.trabajo and tg.grupo=ug.grupo and ug.usuario=? and t.cliente=c.id";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idUsuario),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row;
			}
		return array("error"=>0,"trabajos"=>$salida);
	}

	function validarUsuario($login,$clave){
		$consulta= "select id,administrador ".
				   "from usuario ".
				   "where login=? and password=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($login,$clave),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		$salida=array();
		if(($row=$this->conexion->Next()) != null)
			return array("error"=>0,"id"=>$row["id"],"administrador"=>$row["administrador"]);
		else
			return array("error"=>1,codError=>"U001");
	}

	function cambiarClaveUsuario($login,$clave){
		$consulta= "UPDATE usuario SET password = ?
					     where login=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($clave, $login),true))
				{
				return array("error"=>1,
							 codError=>$this->conexion->msgError);
				}
		return array("error"=>0);

	}
}
?>