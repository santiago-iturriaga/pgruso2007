<?php
class ArmaXML
{
   function ArmaXML()
   	{
   	} 
   
   function getXML($arbol)
   	{
   	$salida="";
   	foreach($arbol as $etiqueta=>$datos)
   		{
   		while ($dato=array_pop($datos))
   			{
   			$salida.='<'.$etiqueta;
   			foreach($dato["atribs"] as $clave=>$valor)
   				{
   				$salida.=' '.$clave.'="'.$valor.'"';
   				}
   			$salida.='>'.$dato["datos"];
   			$salida.=$this->getXML($dato["hijos"]);
   			$salida.='</'.$etiqueta.'>';
   			}
   		}
   	return $salida;
   	}
	
	function getXMLError($codigoError,$msgError)
		{
		return '<error>' .
					'<codigo>'.$codigoError.'</codigo>' .
					'<mensaje>'.$msgError.'</mensaje>' .
				'</error>';	
		}
	function getXMLExito($codigo,$msg)
		{
		return '<exito>' .
					'<codigo>'.$codigo.'</codigo>' .
					'<mensaje>'.$msg.'</mensaje>' .
				'</exito>';	
		}
}
?>
