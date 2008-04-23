<?php
include_once("Conexion.php");
include_once("Momento.php");
include_once("Directorio.php");
class Usuarios{
	var $conexion	=	null;

	function Usuarios($bd){
		$this->conexion=$bd;
	}

	function altaGrupo($nombre){
		$nombre = trim($nombre);
		if($nombre=="") return array("error"=>1,
									 "codError"=>"U50");
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
		$login	= trim($login);
		if($login=="") return array("error"=>1,
									 "codError"=>"U51");
		$clave = trim($clave);
		if($clave=="") return array("error"=>1,
									 "codError"=>"U52");
		$email = trim($email);
		if($email=="") return array("error"=>1,
									 "codError"=>"U53");
		if(!eregi("^([a-z0-9._]+)@([a-z0-9.-_]+).([a-z]{2,4})(.[a-z]{2,4})*$",$email))
			return array("error"=>1,
						 "codError"=>"U54");
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


	function getUsuariosConAlertas(){
		$consulta= "select * from usuario u where u.id in " .
					"(select ug.usuario from usuario_grupo ug, grupo g where ug.grupo=g.id and g.recibe_alertas='S')";
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
	function getUsuariosSinAlertas(){
		$consulta= "select * from usuario u where u.id not in " .
					"(select ug.usuario from usuario_grupo ug, grupo g where ug.grupo=g.id and g.recibe_alertas='S')";
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
	function agregarUsuarioAlertas($idUsuario){
		$consulta = "select id from grupo where recibe_alertas='S'";
		if(!$this->conexion->EjecutarConsulta($consulta,array(),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$row=$this->conexion->Next();
		$idGrupo=$row["id"];
		return $this->asociarUsuarioGrupo($idUsuario,$idGrupo);
	}
	function eliminarUsuarioAlertas($idUsuario){
		$consulta = "select id from grupo where recibe_alertas='S'";
		if(!$this->conexion->EjecutarConsulta($consulta,array(),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$row=$this->conexion->Next();
		$idGrupo=$row["id"];
		$consulta = "delete from usuario_grupo where grupo=? and usuario=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idGrupo,$idUsuario),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

	function getGrupos(){
		$consulta= "select * from grupo where (recibe_alertas<>'S' or recibe_alertas is null) ";
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

	function getTrabajosGrupo($idGrupo,$noBorrados=true){
		$consulta= "select t.* from trabajo t, trabajo_grupo tg where t.id=tg.trabajo and tg.grupo=?";
		if($noBorrados) $consulta.=" and t.fecha_fin is null";
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

	function getTrabajos($noBorrados = true){
		$consulta= "select t.* from trabajo t";
		if($noBorrados) $consulta.=" and t.fecha_fin is null";
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

	function getTrabajosBorrados(){
		$consulta= "select c.nombre as cliente, t.*" .
				   " from trabajo t, cliente c " .
				   " where t.cliente=c.id and t.fecha_fin is not null";
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
		return array("error"=>0,"trabajos"=>$salida);
	}

	function resucitarTrabajo($id){
		$consulta= "update trabajo set fecha_fin = null where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

	function eliminarTrabajo($id){
		$consulta= "update trabajo set fecha_fin=CURRENT_TIMESTAMP where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}


	function eliminarClienteFisicamente($id){
		$consulta= "select id from trabajo where cliente=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$trabajos = array();
		while($row=$this->conexion->Next()){
			array_push($trabajos,$row["id"]);
		}
		foreach($trabajos as $trabajo){
			$res = $this->eliminarTrabajoFisicamente($trabajo);
			if($res["error"]) return $res;
		}

		$consulta	= "select usr_linux " .
					  "from cliente " .
					  "where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$row=$this->conexion->Next();
		if(!$row) return array ("error"=>1,"codError"=>"U998");
		$usr_linux = $row["usr_linux"];

		// elimino la carpeta
		$directorio = new Directorio(RAIZ);
		$res = $directorio->eliminarCarpeta($id,$usr_linux,true);
		if($res["error"]) return $res;

		// elimino la carpeta sistema
		$directorio = new Directorio(RAIZ_SISTEMA);
		$res = $directorio->eliminarCarpeta($id,$usr_linux,true);
		if($res["error"]) return $res;

		$consulta = "delete from cliente where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}


	function eliminarTrabajoFisicamente($tid){
		$consulta	= "select t.cliente, c.usr_linux " .
					  "from trabajo t, cliente c " .
					  "where t.cliente=c.id and t.id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($tid),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$row=$this->conexion->Next();
		if(!$row) return array ("error"=>1,"codError"=>"U999");
		$cid = $row["cliente"];
		$usr_linux = $row["usr_linux"];

		// elimino la carpeta
		$directorio = new Directorio(RAIZ."/".$cid);
		$res = $directorio->eliminarCarpeta($tid,$usr_linux,true);
		if($res["error"]) return $res;


		// elimino la carpeta sistema
		$directorio = new Directorio(RAIZ_SISTEMA."/".$cid);
		$res = $directorio->eliminarCarpeta($tid,$usr_linux,true);
		if($res["error"]) return $res;

		//elimino tuplas
		$consulta	= "delete from trabajo_grupo where trabajo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($tid),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$consulta	= "delete from usuario_alerta where trabajo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($tid),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$consulta	= "delete from trabajo_alerta where trabajo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($tid),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$consulta	= "delete from ejecucion where trabajo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($tid),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$consulta	= "delete from trabajo where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($tid),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}

		return array("error"=>0);

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
		$consulta= "select g.* from grupo g, usuario_grupo ug where ?=ug.usuario and ug.grupo=g.id  and (g.recibe_alertas<>'S' or g.recibe_alertas is null) ";
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

	function getTrabajosUsuario($idUsuario,$noBorrados=true){
		$consulta= "select t.id, t.nombre as trabajo, c.id as id_cliente, c.nombre as cliente ".
				   "from trabajo t, trabajo_grupo tg, usuario_grupo ug,cliente c ".
				   "where t.id=tg.trabajo and tg.grupo=ug.grupo and ug.usuario=? and t.cliente=c.id";
		if($noBorrados) $consulta.=" and t.fecha_fin is null";
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

	function getClientesUsuario($idUsuario,$noBorrados=true){
		$consulta= "select c.id, c.nombre, c.usr_linux ".
				   "from trabajo t, trabajo_grupo tg, usuario_grupo ug,cliente c ".
				   "where t.id=tg.trabajo and tg.grupo=ug.grupo and ug.usuario=? and t.cliente=c.id";
		if($noBorrados) $consulta.=" and t.fecha_fin is null";
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
		return array("error"=>0,"clientes"=>$salida);
	}


	function validarUsuario($login,$clave){
		$consulta= "select id,administrador ".
				   "from usuario ".
				   "where login=? and password=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($login,$clave),true))
			{
			return array("error"=>1, "codError"=>"EU01");
			}
		$salida=array();
		if(($row=$this->conexion->Next()) != null)
			return array("error"=>0,"id"=>$row["id"],"administrador"=>$row["administrador"]);
		else
			return array("error"=>1,"codError"=>"EU02");
	}

	function cambiarClaveUsuario($login,$clave){
		$consulta= "UPDATE usuario SET password = ?
					     where login=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($clave, $login),true))
				{
				return array("error"=>1, "codError"=>"EU03");
				}
		return array("error"=>0);

	}

	function getClientes(){
		$consulta= "select * from cliente ";
		if(!$this->conexion->EjecutarConsulta($consulta,array(),true))
			{
			return array("error"=>1,
						 codError=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row;
			}
		return array("error"=>0,"clientes"=>$salida);
	}

	function getTrabajosCliente($idCliente,$noBorrados=true){
		$consulta= "select t.* from trabajo t where t.cliente=?";
		if($noBorrados) $consulta.=" and t.fecha_fin is null";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idCliente),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			$salida[$row["id"]]=$row;
			}
		return array("error"=>0,"trabajos"=>$salida);
	}
	function getTrabajo($id){
		$consulta= "select t.* from trabajo t where t.id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$row=$this->conexion->Next();
		return array("error"=>0,"trabajo"=>$row);
	}
	function crearTrabajo($cliente,$nombre,$nodos,$tiempo_maximo,$cola,$quota){
		$nombre = trim($nombre);
		$nodos = trim($nodos);
		$tiempo_maximo = trim($tiempo_maximo);
		$cola = trim($cola);
		$quota = trim($quota);
		if($nombre == "") return array("error"=>1,"codError"=>"CT101");
		if($nodos == "" or !is_numeric($nodos) or $nodos<0) return array("error"=>1,"codError"=>"CT102");
		if($tiempo_maximo == "" or !ereg("[0-9][0-9]:[0-9][0-9]:[0-9][0-9]",$tiempo_maximo))
			return array("error"=>1,"codError"=>"CT103");
		if($cola == "") return array("error"=>1,"codError"=>"CT104");
		if($quota == "" or !is_numeric($quota) or $quota<0) return array("error"=>1,"codError"=>"CT105");

		$consulta= "insert into trabajo (cliente,nombre,nodos,tiempo_maximo,cola,quota) values (?,?,?,?,?,?)";
		if(!$this->conexion->EjecutarConsulta($consulta,array($cliente,$nombre,$nodos,$tiempo_maximo,$cola,$quota),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$id=$this->conexion->getUltimoNumerador();
		$momento = new Momento($this->conexion);
		$res	= $momento->crearDirTrabajo($cliente,$id);
		if($res["error"]) {
			$consulta= "delete from trabajo where id=?";
			$this->conexion->EjecutarConsulta($consulta,array($id),false);
			return $res;
		}
		$consulta= "select id from grupo where es_admin='S'";
		if(!$this->conexion->EjecutarConsulta($consulta,array(),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$row=$this->conexion->Next();
		$idGrupo = $row["id"];
		if($idGrupo!=null){
			$res = $this->asociarTrabajoGrupo($id,$idGrupo);
			if($res["error"]) return $res;
		}

		return array("error"=>0,"id"=>$id);
	}

	function crearCliente($nombre,$usr_linux){
		$consulta= "insert into cliente (nombre,usr_linux) values (?,?)";
		if(!$this->conexion->EjecutarConsulta($consulta,array($nombre,$usr_linux),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$id=$this->conexion->getUltimoNumerador();
		$momento = new Momento($this->conexion);
		$res	= $momento->crearDirCliente($id,$usr_linux);

		if($res["error"]) {
			$consulta= "delete from cliente where id=?";
			$this->conexion->EjecutarConsulta($consulta,array($id),false);
			return $res;
		}
		return array("error"=>0,"id"=>$id);
	}

	function editarCliente($id,$nombre){
		$consulta= "update cliente set nombre=? where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($nombre,$id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}

	function editarTrabajo($id,$nombre,$nodos,$tiempo_maximo,$cola,$quota){
		$nombre = trim($nombre);
		$nodos = trim($nodos);
		$tiempo_maximo = trim($tiempo_maximo);
		$cola = trim($cola);
		$quota = trim($quota);
		if($nombre == "") return array("error"=>1,"codError"=>"CT101");
		if($nodos == "" or !is_numeric($nodos) or $nodos<0) return array("error"=>1,"codError"=>"CT102");
		if($tiempo_maximo == "" or !ereg("[0-9][0-9]:[0-9][0-9]:[0-9][0-9]",$tiempo_maximo))
			return array("error"=>1,"codError"=>"CT103");
		if($cola == "") return array("error"=>1,"codError"=>"CT104");
		if($quota == "" or !is_numeric($quota) or $quota<0) return array("error"=>1,"codError"=>"CT105");


		$consulta= "update trabajo set nombre=?,nodos=?,tiempo_maximo=?,cola=?,quota=? where id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($nombre,$nodos,$tiempo_maximo,$cola,$quota,$id),false))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		return array("error"=>0);
	}
	function getUsuariosTrabajo($idTrabajo){
		$consulta= "select ug.usuario from usuario_grupo ug, trabajo_grupo tg where tg.grupo=ug.grupo and tg.trabajo=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($idTrabajo),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		while(($row=$this->conexion->Next()) != null)
			{
			array_push($salida,$row["usuario"]);
			}
		return array("error"=>0,"usuarios"=>$salida);
	}

	function getCliente($id){
		$consulta= "select c.*" .
				   " from cliente c " .
				   " where c.id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($id),true))
			{
			return array("error"=>1,
						 "codError"=>$this->conexion->msgError);
			}
		$salida=array();
		$row=$this->conexion->Next();
		return array("error"=>0,"cliente"=>$row);
	}

}
?>