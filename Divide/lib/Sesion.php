<?php 

class Sesion {

	function Sesion ($nueva = false)
		{
		if (session_id()) {
			$this = null;
			return;	
		}
		if (!$nueva) {
			session_start();
			if (!isset($_SESSION['PHPSesion'])) {
				$this->cerrar();
				$this = null;
				return;
			}
			$this = unserialize($_SESSION['PHPSesion']);
			if (!is_object($this)) {
				$this->cerrar();
				$this = null;
				return;
			}
		}
		else {
			list($usec, $sec) = explode(' ', microtime());
    		mt_srand( (float) $sec + ((float) $usec * 100000) );
			if (function_exists("posix_getpid")){
				#Linux
				session_id( md5( uniqid(mt_rand().posix_getpid(),true) ) );
			}
			else {
				#Windows
				session_id( md5( uniqid(mt_rand(),true) ) );
			}
			session_start();
			session_unset();
			$this->salvar();
		}
	}		


	function salvar() {
		$_SESSION['PHPSesion'] = serialize($this);
		return true;
	}

	function cerrar(){
		if (isset($_COOKIE[session_name()])) {
		   setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		$this = null;
	}
	
}

?>