<?php
/**
* Este archivo contiene funciones para extraer datos fácilmente
*
* @author Zerquix18
* @since 0.1
**/

function existe_usuario( $usuario ) {
	global $zerdb;
	$usuario = $zerdb->real_escape( $usuario );
	$u = $zerdb->select($zerdb->usuarios, "*", array("usuario" => $usuario) )->_();
	return $u && (int) $u->nums > 0 ? $u : false;
}


/** 
* Obtiene los datos de un usuario desde su nombre de usuario
*
*
* @param $id str
**/

function obt_id( $id ) {
	global $zerdb;
	$u = $zerdb->select($zerdb->usuarios, "*", array("id" => $id) )->_();
	return ($u && $u->nums > 0) ? $u : false;
}

/**
*
* Obtiene el usuario de la sesión actual
* 
**/

function obt_usuario_actual() {
	global $zerdb;
	if( ! sesion_iniciada() )
		return false;
	return obt_id( $_SESSION['id'] );
}

/**
*
* Comprueba si el rango del usuario es super administrador
*
**/

function es_super_admin() {
	global $zerdb;
	if( false == ($u = obt_usuario_actual() ) ) //removí la doble comprobación de sesión iniciada...
		return false;
	$u = $zerdb->select($zerdb->usuarios, "rango", array("id" => $u->id) )->_();
	return 1 == $u->rango;
}

/**
*
* Comprueba si el rango del usuario es administrador
*
**/

function es_admin() {
	global $zerdb;
	if( false == ($u = obt_usuario_actual() ) ) //removí la doble comprobación de sesión iniciada...
		return false;
	$u = $zerdb->select($zerdb->usuarios, "rango", array("id" => $u->id) )->_();
	return 2 == $u->rango || es_super_admin();
}


/**
*
* Comprueba si el rango del usuario es actualizador
*
**/

function es_actualizador() {
	global $zerdb;
	if( false == ($u = obt_usuario_actual() ) ) //removí la doble comprobación de sesiión iniciada...
		return false;
	$u = $zerdb->select($zerdb->usuarios, "rango", array("id" => $u->id) )->_();
	return 3 == $u->rango || es_admin();
}

/**
*
* Echa el rango del ID del usuario
*
* @param $id str | int
*
**/

function rango( $id = null ) {
	global $zerdb;
	$u = obt_id($id);
	if( false == $u )
		return $u;
	switch( $u->id ) {
		case "1":
		return __("Super Administrator");
		break;
		case "2":
		return __("Administrator");
		break;
		case "3":
		return __("Updater");
		break;
		default:
		return __("It has no rank .-. ):"); /// eeeeeeeeey troooooooooooll xdddddd
	}
}
function existe_usuario_id( $id ) {
	global $zerdb;
	$u = obt_id($u);
	return false !== $u;
}

function comprobar_rangos( $rango1, $rango2 = '') {
	if( ! is_numeric($rango1) )
		return false;
	$rango1 = (int) $rango1;
	if( $rango1 !== 1 || $rango1 !== 2 || $rango1 !== 3)
		return false;
	$u = obt_usuario_actual();
	if( empty($rango2) )
		$rango2 = $u->rango;
 	return $rango1 <= $rango2; //menor o igual
}

function existe_email( $email ) {
	global $zerdb;
	if( ! is_string($email) )
		return false;
	$q = $zerdb->select($zerdb->usuarios, "*", array("email" => $zerdb->real_escape( $email ) ) )->_();
	return $q && $q->nums > 0;
}

function estado( $estado ) {
	global $zerdb;
	return(int) $estado == 1 ? __("Active") : __("Banned");
}