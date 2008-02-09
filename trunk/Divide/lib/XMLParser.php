<?php
/* Parsea un XML y provee de funciones para facilitar el trabajo sobre el resultado.
 * El resultado queda guardado en el elemento arbol
 * en caso de que ocurriera un error, el c�digo del mismo quedar� guardado en
 * error_code, y el mensaje en error_string.
 *
 * El resultado (guardado en arbol) puede resultar poco pr�ctico para trabajar,
 * para un manejo mas accesible, est� implementada la funcion getObjeto, que
 * devuelve un array con un formato mas amigable
 */

class XMLParser
{
   var $parser;
   var $error_code;
   var $error_string;
   var $linea_actual;
   var $columna_actual;
   var $arbol = array();
   var $datos = array();

	var $MENSAJE_XMLP=array(
			"XP00"=>"Falta tag *CLAVE* bajo el elemento *ETIQUETA*",
			"XP01"=>"Clave duplicada en elemento *ETIQUETA*",
			"XP02"=>"Formato inválido");

   function XMLParser()
   	{
   		$this->error_code="";
   	}

   	/* Dado un arbol (en general el que se obtuvo del parseo) devuelve
   	 * un array con un formato un poco mas "normal".
   	 * Si $minimizar=falso => elimina la entrada "atributo" para los
   	 * elementos del arbol que no los tengan, y NO devuelve como array
   	 * a los hijos de cada nodo si hay uno para cada etiqueta y la etiqueta
   	 * no esta en $etiquetas_siempre_array
   	 *
   	 * Ejemplo:
   	 * 	XML: <c>
   	 * 			<d1> d1 </d1>
   	 * 			<d2> d2 </d2>
   	 * 		 </c>
   	 * Resultado con minimizar=true, $etiquetas_siempre_array=array()
   	 * 		Array ( [C] =>
   	 * 					Array ( [D1] => d1
   	 * 							[D2] => d2
   	 * 						  )
   	 * 			  )
   	 *
   	 * Resultado con minimizar=true, $etiquetas_siempre_array=array("D1"=>1)
   	 * 		Array ( [C] =>
   	 * 					Array ( [D1] =>
   	 * 								Array ( [0] => d1  )
   	 * 							[D2] => d2 )
   	 * 			  )
   	 *
   	 * La idea es que cuando sabemos que el caso habitual es que lleguen varias
   	 * ocurrencias de un mismo tag, entonces lo minimicemos mandandole ese tag
   	 * en el array para que tanto si llega una sola ocurrencia del mismo como si
   	 * llegan mas, la estructura devuelta sea similar.
   	 *
   	 */
	function getObjeto($arbol,$minimizar=false,$etiquetas_siempre_array=array(),$claves=array()){
   		$salida=array();
   		foreach($arbol as $etiqueta=>$datos)
	   		{
	   		$salida[$etiqueta]=array();
		   	while ($dato=array_pop($datos))
   				{
   				$elem=array();

   				$atribs=array();
   				foreach($dato["atribs"] as $clave=>$valor)
	   				{
   					$atribs[$clave]=$valor;
   					}
   				if(!$minimizar or count($atribs)>0)
   					{
   					$elem["atributos"]=$atribs;
   					}
   				$hijos=$this->getObjeto($dato["hijos"],$minimizar,$etiquetas_siempre_array,$claves);
   				if($this->error_code != "") return;

   				foreach($hijos as $k=>$v)
   					$elem[$k]=$v;
   				$dato["datos"]=ereg_replace ( '^[[:space:]]*','',$dato["datos"]);
				$dato["datos"]=ereg_replace ( '[[:space:]]*$','',$dato["datos"]);
   				if(!$minimizar or count($elem)>0)
   					{
   					if($dato["datos"]!="")
   						$elem["datos"]=$dato["datos"];
   					}
   				else
   					$elem=$dato["datos"];
   				if(isset($claves[$etiqueta]))
   					{
   				 	if(!isset($elem[$claves[$etiqueta]]))
   				 		{
   				 		$this->error_code = "XP00";
   				 		$this->error_string =strtr ($this->MENSAJE_XMLP["XP00"],array("CLAVE"=>$claves[$etiqueta],
   				 																	  "ETIQUETA"=>$etiqueta));
           				return;
           	 			}
           	 		elseif(isset($salida[$etiqueta][$elem[$claves[$etiqueta]]]))
           	 			{
           	 			$this->error_code = "XP01";
           				$this->error_string = strtr ($this->MENSAJE_XMLP["XP01"],array("ETIQUETA"=>$etiqueta));
           				return;
           	 			}
   					$salida[$etiqueta][$elem[$claves[$etiqueta]]]=$elem;
   					}
   				else
   					{
   					array_push($salida[$etiqueta],$elem);
   					}
   				}
   			if($minimizar and !isset($etiquetas_siempre_array[$etiqueta]))
   				{
   				if(count($salida[$etiqueta]) ==1)
	   				$salida[$etiqueta]=array_pop($salida[$etiqueta]);
   				}
   			}
   		return $salida;
		}

   function parsear($datos,$codificacion)
   {
       $this->parser = xml_parser_create($codificacion);

       //setea al objeto como parser
       xml_set_object($this->parser, $this);

       xml_set_element_handler($this->parser, 'inicio_tag', 'fin_tag');
       xml_set_character_data_handler($this->parser, 'llegan_datos');
       if (!xml_parse($this->parser, $datos))
       	   {
           $this->arbol = array();
           $this->error_code = xml_get_error_code($this->parser);
           $this->error_string = xml_error_string($this->error_code);
           $this->linea_actual= xml_get_current_line_number($this->parser);
           $this->columna_actual = xml_get_current_column_number($this->parser);
       	   }
       else
       	   {
       	   if(!isset($this->arbol['hijos']))
       	   	{
       	   		$this->error_code = "XP02";
           		$this->error_string = $this->MENSAJE_XMLP["XP02"];
           	}
           else
       	   		$this->arbol = $this->arbol['hijos'];
       	   }
       xml_parser_free($this->parser);
       return $this->arbol;
   }

   function inicio_tag($parser, $tag, $attribs)
   {

       $this->arbol['hijos'][$tag][] = array('datos' => '', 'atribs' => $attribs, 'hijos' => array());
       $this->datos[] =& $this->arbol;
       $this->arbol=& $this->arbol['hijos'][$tag][count($this->arbol['hijos'][$tag])-1];
   }

   function llegan_datos($parser, $cdata)
   {
       $this->arbol['datos'] .= $cdata;
   }

   function fin_tag($parser, $tag)
   {
       $this->arbol =& $this->datos[count($this->datos)-1];
       array_pop($this->datos);
   }
}


?>