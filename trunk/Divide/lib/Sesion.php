<?php 

class Sesion {
	var $sesion = null;
	var $hay_sesion = false;
	function Sesion ($nueva = false)
		{
		if (session_id()) {
			return;	
		}
		if (!$nueva) {
			session_start();
			if (!isset($_SESSION['PHPSesion'])) {
				$this->cerrar();
				$this->hay_sesion = false;
				return;
			}
			$this->sesion = unserialize($_SESSION['PHPSesion']);
			if (!is_object($this)) {
				$this->cerrar();
				$this->hay_sesion = false;
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
			$this->hay_sesion = true;

			$this->salvar();
		}
	}		


	function salvar() {
		$_SESSION['PHPSesion'] = serialize($this->sesion);
		return true;
	}

	function cerrar(){
		if (isset($_COOKIE[session_name()])) {
		   setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		$this->sesion = null;
		$this->hay_sesion = false;
	}
	
}

?>
