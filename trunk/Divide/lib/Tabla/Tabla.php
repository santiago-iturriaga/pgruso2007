<?
	set_include_path(get_include_path().';'.
					 '../../lib');
	include_once("TPL.php");

	class Tabla{
		var $renglones=array();
		var $columnas=array();
		var $id="";
		var $clase="";
		function Tabla($id="",$clase="",$dir_relativa=""){
			$this->clase=$clase;
			$this->id=$id;
			$this->dir_relativa=$dir_relativa;
		}
		function addColumna($orden,$nombre,$titulo,$id=""){
			if($id!="") $id= "id=$id";
			$this->columnas[$orden]=array("nombre"=>$nombre,
									"titulo"=>$titulo,
									"id"=>$id);
		}
		function addRenglon($renglon){
			array_push($this->renglones,$renglon);
		}
		function getTabla($mostrar_head=true){
			$plantilla=new TPL();
			ksort($this->columnas);
			$tabla=$plantilla->load($this->dir_relativa."../lib/Tabla/plantillas/tabla.html");
			$renglon=$plantilla->load($this->dir_relativa."../lib/Tabla/plantillas/renglon.html");
			$columna=$plantilla->load($this->dir_relativa."../lib/Tabla/plantillas/columna.html");


			// cabezal
			$thead="";
			if($mostrar_head){
				foreach($this->columnas as $col){
					$thead.=$plantilla->replace($columna,array("TEXTO"=>$col["titulo"],"ID"=>$col["id"]));
				}
				$thead=$plantilla->replace($renglon,array("COLUMNAS"=>$thead));
			}
			//renglones
			$renglones="";
			foreach($this->renglones as $ren){
				$renglon_x="";
				foreach($this->columnas as $col){
					$renglon_x.=$plantilla->replace($columna,array("TEXTO"=>$ren[$col["nombre"]],"ID"=>$col["id"]));
					}
				$renglon_x=$plantilla->replace($renglon,array("COLUMNAS"=>$renglon_x));
				$renglones.=$renglon_x;
				}
			$clase = "";
			$id = "";
			if($this->clase !="") $clase = "class='".$this->clase."'";
			if($this->id !="")  $id = "id='".$this->id."'";
			$salida =$plantilla->replace($tabla,array("CLASE"=>$clase,
													"ID"=>$id,
													"CABEZAL"=>$thead,
													"CUERPO"=>$renglones,
													"PIE"=>""));
			return $salida;
		}
	}
?>