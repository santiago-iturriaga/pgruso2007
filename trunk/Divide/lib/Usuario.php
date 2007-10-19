<?php
include_once("Conexion.php");
class Usuario{
	var $conexion	=	null;
	var $login		=	null;
	var $cliente	=	null;
	var $trabajos	=	null;
	
	function Usuario($bd){
		$this->conexion	=	$bd;
	}
	
	function Logueado(){
		return $this->login != null;
	}
	
	function Login($login,$clave){
		$consulta="select count(*) cant
				   from usuario
				   where login=?
				     and password=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($login,$clave),true))
			return array("error"=>1,
						 "codError"=>$this->conexion->codError);
		$res=$this->conexion->Next();
		if(!$res)
			return array("error"=>1,
						 "codError"=>$this->conexion->codError);
			
		if($res["cant"]==0)
			return array("error"=>1,
						 "codError"=>'U001');
		$this->login	=	$login;
		
		$res	=	$this->_getCliente();
		if($res["error"]) return $res;
		$this->cliente	=	$res["cliente"];
		
		$res	=	$this->_getTrabajos();
		if($res["error"]) return $res;
		$this->trabajos	=	$res["trabajos"];
		
		return array("error"=>0);
	}
	
	function _getCliente(){
		$consulta="select c.*
				   from cliente c, usuario u
				   where c.id=u.cliente
				   	 and u.login=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($this->login),true))
			return array("error"=>1,
						 "codError"=>$this->conexion->codError);
		$res=$this->conexion->Next();
		if(!$res)
			return array("error"=>1,
						 "codError"=>$this->conexion->codError);
			
		return array("error"=>0,
					 "cliente"=>$res);
	}
	
	function _getTrabajos(){
		$consulta="select t.*
				   from cliente c, trabajo t
				   where t.id=c.trabajo
				   	 and c.id=?";
		if(!$this->conexion->EjecutarConsulta($consulta,array($this->cliente["id"]),true))
			return array("error"=>1,
						 "codError"=>$this->conexion->codError);
		$trabajos	=	array();
		while($res=$this->conexion->Next()) array_push($trabajos,$res);
			
		return array("error"=>0,
					 "trabajos"=>$trabajos);
	}
	
}
?>
