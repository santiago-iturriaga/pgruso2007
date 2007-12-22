<?php
class Conexion{
	var $link;
	var $conexion_ok=false;
	var $codError="";
	var $result=null;
	function Conexion($host,$usuario,$pwd,$bd){
		if (!($this->link=pg_connect("host=".$host." dbname=".$bd." user=".$usuario." password=".$pwd)))
   			{
      		$this->codError='CX01';
   			}
   		else
			$this->conexion_ok=true;
   	}
	function Desconectar(){
		pg_close($this->link);
		$this->conexion_ok=false;
		return true;
	}
	function Consulta($select){
		if(!$this->conexion_ok)
			{
			$this->codError='CX03';
			return false;
			}
		$this->result=pg_query($this->link,$select);
		if($this->result==false)
			{
			error_log("Consulta erronea: ".$select);
			$this->result=null;
			$this->msgError='CX04';
			return false;
			}
		return true;
	}
	function Next(){
		if(!$this->conexion_ok)
			{
			$this->codError='CX03';
			return false;
			}
		if($this->result==null)
			{
			$this->codError='CX05';
			return false;
			}
		$res=pg_fetch_array($this->result,null,PGSQL_ASSOC);
		if(!$res)
			{
			pg_free_result($this->result);
			$this->result=null;
			}
		return $res;
	}
	function LiberarConsulta(){
		if(!$this->conexion_ok)
			{
			$this->codError='CX03';
			return false;
			}
		if($this->result==null)
			{
			$this->codError='CX05';
			return false;
			}
		pg_free_result($this->result);
		$this->result=null;
		return true;
	}
	function Insertar($consulta){
		if(!$this->conexion_ok)
			{
			$this->codError='CX03';
			return false;
			}
		if(!pg_query($consulta,$this->link))
			{
			error_log("Consulta erronea: ".$consulta);
			$this->codError='CX04';
			return false;
			}
		return true;
	}
	function esc($parametro){
		//return mysql_real_escape_string($parametro);
		return $parametro;
	}

	function EjecutarConsulta($consulta,$parametros,$select=false){
		$array_cons=explode ('?',$consulta);
		if(count($array_cons)!=(count($parametros)+1))
			{
			$this->codError='CX06';
			return false;
			}
		$cons=array_shift($array_cons);
		foreach ($parametros as $parametro)
			{
			if($parametro==null)
				$parametro='null';
			elseif(!is_numeric($parametro) and $parametro!=null)
				$parametro="'".$this->esc($parametro)."'";
			$cons.=$parametro.array_shift($array_cons);
			}
		if($select)
			return $this->Consulta($cons);
		else
			return $this->Insertar($cons);
	}

	function getUltimoNumerador(){
		//error_log('cambiar esto');
		//if(!$res=$this->EjecutarConsulta("SELECT LASTVAL() as num",array(),true))
		if(!$res=$this->EjecutarConsulta("select (max(id)) as num from ejecucion",array(),true))
			return false;
		if(!$res=$this->Next())
			return false;
		return $res["num"];
	}
}

?>