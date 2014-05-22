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
* @since 0.1
* 
* 
**/

$preg = sprintf("#%s#", basename(__FILE__) );
if( preg_match($preg, $_SERVER['PHP_SELF'])) exit();

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

		$data = $zerdb->select($zerdb->usuarios, "id", array("usuario" => $usuario) );
		$data = $data->execute();

		if( $this->sesion_iniciada() || !$data->nums > 0 ) //Si ya inició sesión o el usuario no existe.
			return false;

		$id = $data->id;
		$hash = generar_hash(32, false, false);
		$ip = obt_ip();
		$dia = time();
		if( $alargar ) {
			$time = time() + 60 * 60 * 24 * 30 * 2;
			setcookie('hash', $hash, $time);	// 2 meses.
		}
		$_SESSION['hash'] = $hash;
		$_SESSION['usuario'] = $usuario;
		$_SESSION['id'] = $id;
		return $zerdb->insert($zerdb->sesiones, array($id, $hash, $ip, $dia ) );
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
		if( ! comprobar_instalacion() || empty($_SESSION) )
			return false;

		$comprobar = $zerdb->select($zerdb->sesiones, "*", array("id" => $_SESSION['id'], "hash" => $_SESSION['hash']) )->_();
		
		if( isset($comprobar) && $comprobar->nums > 0) //si existe en la db...
			return true;

		return false;
	}
/**
*
* Destruye todas las sesiones actuales del usuario
*
**/
	public function destruir() {
		global $zerdb;
		if( ! $this->sesion_iniciada() )
			return false;
		$var = $zerdb->delete($zerdb->sesiones, array("hash" => $_SESSION['hash'], "id" => $_SESSION['id'] ) );
		$var->execute();
		if( isset($_COOKIE['hash']) )
			setcookie('hash', '', time() - 3600 );
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
		$id = $zerdb->real_escape($id);
		$comprobar = $zerdb->select($zerdb->sesiones, "*", array("id" => $id ) )->_();
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
		$hash = $zerdb->real_escape( $hash );
		$comprobar = $zerdb->select($zerdb->sesiones, "*", array("hash" => $hash ) )->_();
		if( ! $comprobar || ! $comprobar->nums > 0)
			return false;

		return $zerdb->delete($zerdb->sesiones, array("hash" => $hash ) )->_();
	}

	/* fin de la clase */
}

/* Abre la sesión si existe la cookie y si existe el hash en la base de datos */

if( comprobar_instalacion() && isset($_COOKIE['hash'] ) ) {
	$s = $zerdb->select($zerdb->sesiones, "*", array("hash" => $zerdb->real_escape($_COOKIE['hash']), "ip" => obt_ip() ) )->_();

	if( $s && $s->nums > 0) {
		$u = $zerdb->select($zerdb->usuarios, "*", array("id" => $s->id) )->_();
		if( $u && $u->nums > 0):
		$_SESSION['hash'] = $_COOKIE['hash'];
		$_SESSION['id'] = $s->id;
		$_SESSION['usuario'] = $u->usuario;
		endif;
	}else{
		setcookie('hash', '', time() - 6000);
	}
}