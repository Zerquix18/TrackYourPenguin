<?php
/**
*
* Clase de sesiones
*
* Hace las sesiones, las borra y maneja las cookies que utliza el sitio
*
*
* @package TrackYourPenguin
* @subpackage Sesiones
* @author Zerquix18 <http://www.zerquix18.com/>
* @link http://trackyourpenguin.com/
* @copyright 2013 TrackYourPenguin
* @since 0.0.1
* 
* 
**/

class sesiones {

	public function __construct() {
		return true;
	}
/**
*
* Crea la sesión
*
* Crea la sesión según el nombre de usuario dado y alarga la sesión sin $alargar resulta true.
*
* @return bool (false) si el usuario no existe
* @param $usuario string
* @param $alargar bool
*
**/

	public function crear($usuario, $alargar = false) {
		global $zerdb;

		$data = new extraer($zerdb->usuarios, "id", array("usuario" => $usuario) );

		if( $this->sesion_iniciada() || !$data->nums > 0 ) //Si ya inició sesión o el usuario no existe.
			return false;

		$id = $data->id;
		$hash = md5( uniqid() );
		$ip = $_SERVER['REMOTE_ADDR'];
		$dia = retornar_fecha();
		if( $alargar )
		$xd = setcookie('hash', $hash, time() + 60 * 60 * 24 * 30 * 2);	
		$_SESSION['hash'] = $hash;
		$_SESSION['usuario'] = $usuario;
		$_SESSION['id'] = $id;
		$this->insertar( $id, $hash, $ip, $dia );
	}
/**
* Inserta la sesión en la base de datos
*
* @param $id int|string
* @param $hash string
* @param $ip string
* @param $dia string
* @return bool
*
**/
	private function insertar($id, $hash, $ip, $dia) {
		global $zerdb;
		$var = $zerdb->insertar($zerdb->sesiones, array($id, $hash, $ip, $dia) );
		return true;
	}
/**
*
* Comprueba si el usuario inició sesión
*
* @return bool
*
*/

	public function sesion_iniciada() {
		global $zerdb;
		if( ! $_SESSION )
			return false;

		$comprobar = new extraer($zerdb->sesiones, "*", array("id" => $_SESSION['id'], "hash" => $_SESSION['hash']) );
		
		if( $comprobar && (int) $comprobar->nums > 0) //si existe en la db...
			return true;

		return false;
	}
/**
*
* Destruye todas las cookies existentes...
*
* @link http://www.php.net/manual/en/function.setcookie.php#73484
*
**/
	function destruir_cookies() {
		if ( isset($_SERVER['HTTP_COOKIE']) ) {
    		$cookies = explode('; ', $_SERVER['HTTP_COOKIE']);
    		foreach($cookies as $cookie) {
        		$partes = explode('=', $cookie);
        		$nombre = trim( current($partes) );
        		setcookie($nombre, '', time() - 6000);
        		setcookie($nombre, '', time() - 6000);
    		}
		}
	}
/**
*
* Destruye todas las sesiones actuales del usuario
*
**/
	public function destruir() {
		global $zerdb;
		$this->destruir_cookies();
		if( ! $this->sesion_iniciada() )
			return false;

		$var = $zerdb->eliminar($zerdb->sesiones, array("hash" => $_SESSION['hash'], "id" => $_SESSION['id'] ) );
		session_destroy();
		session_unset();
	}
/**
*
* Destruye todas las sesiones provenientes de un ID
*
**/
	public function destruir_id( $id ) {
		global $zerdb;
		$id = $zerdb->proteger($id);
		$comprobar = new extraer($zerdb->sesiones, "*", array("id" => $id ) );
		if( ! $comprobar || ! $comprobar->nums > 0)
			return false;

		return $zerdb->eliminar($zerdb->sesiones, array("id" => $id ) );
	}
	
/**
* Destruye todas las sesiones desde un HASH
*
*
**/
	public function destruir_hash( $hash ) {
		global $zerdb;
		$hash = $zerdb->proteger( $hash );
		$comprobar = new extraer($zerdb->sesiones, "*", array("hash" => $hash ) );
		if( ! $comprobar || ! $comprobar->nums > 0)
			return false;

		return $zerdb->eliminar($zerdb->sesiones, array("hash" => $hash ) );
	}

	/* fin de la clase */
}

/* Abre la sesión si existe la cookie y si existe el hash en la base de datos */

if( comprobar_instalacion() && isset($_COOKIE['hash'] ) ) {
	$s = new extraer($zerdb->sesiones, "*", array("hash" => $zerdb->proteger($_COOKIE['hash']), "ip" => $_SERVER['REMOTE_ADDR'] ) );

	if( $s && $s->nums > 0) {
		$u = new extraer($zerdb->usuarios, "*", array("id" => $s->id) );
		if( $u && $u->nums > 0):
		$_SESSION['hash'] = $_COOKIE['hash'];
		$_SESSION['id'] = $s->id;
		$_SESSION['usuario'] = $u->usuario;
		endif;
	}else{
		setcookie('hash', '', time() - 6000);
	}
}