<?php


class TPL {

	function TPL(){}
	
	/*
	 * Carga las plantillas (templates) indicadas en strings en memoria
	 * Se le pasa un array de strings indicando los archivos a leer o un solo string.
	 * Para cada uno se devuelve un string con su contenido
	 */
	function load($tpls) {
	
		if (!is_array($tpls)) 
			$auxTpls = array($tpls);
		else
			$auxTpls = $tpls;
			
		$stringTpls = array();
		foreach($auxTpls as $tpl) 
			$stringTpls[] = join("", file($tpl));
			
		if (!is_array($tpls))
			$stringTpls = $stringTpls[0];
			
		return $stringTpls;
	}
	
	/*
	 * Realiza reemplazos en el string subject, donde search puede ser un array
	 * asociativo con valores de busqueda y reemplazo o se le puede pasar en search
	 * un string de busqueda y en replace un string de reemplazo.
	 * retorna el string con los cambios producidos
	 * NOTA: Los reemplazos son simultaneos no repetitivos.
	 */
	function replace ($subject, $search, $replace=NULL) {
	
		if (!is_array($search)) 
			$search = array($search=>$replace);
			
		$searchAndReplace = array();
		foreach($search as $k=>$v)
			$searchAndReplace["{".$k."}"] = $v;
		
		return strtr($subject, $searchAndReplace);
		
	}
	
	/*
	 * Descomenta codigo abarcado por la tag indicada
	 * tag puede ser una sola tag (un string) o muchas tags (array de string).
	 * Retorna el string con los cambios producidos
	 * NOTA: Los reemplazos son simultaneos no repetitivos.
	 */
	function unComment ($subject, $tag) {
	
		if (!is_array($tag))
			$tag = array($tag);
			
		$searchAndReplace = array();
		foreach($tag as $t) {
			$searchAndReplace["<!--{".$t."}"] = "";
			$searchAndReplace["{".$t."}-->"]  = "";
		}
		
		return strtr($subject, $searchAndReplace);
		
	}

}

?>
