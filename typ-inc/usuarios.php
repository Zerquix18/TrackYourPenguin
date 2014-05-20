<?php
/**
* Este archivo contiene funciones para extraer datos fácilmente
*
* @author Zerquix18
* @since 0.1
**/

function existe_usuario( $usuario ) {
	global $zerdb;
	$usuario = $zerdb->proteger( $usuario );
	$u = new extraer($zerdb->usuarios, "*", array("usuario" => $usuario) );

	return true == ( $u && (int) $u->nums > 0 );
}


/** 
* Obtiene los datos de un usuario desde su nombre de usuario
*
*
* @param $id str
**/

function obt_id( $id ) {
	global $zerdb;
	return @new extraer($zerdb->usuarios, "*", array("id" => $id ) );
}

/**
*
* Obtiene el usuario de la sesión actual
* Si $array es true, lo devolverá en un array y no en objeto.
* 
**/

function obt_usuario_actual( $array = false ) {
	global $zerdb;
	if( ! sesion_iniciada() )
		return false;

	$u = obt_id( $_SESSION['id'] );
	unset($u->clave);
	return ($array) ? (array) $u : $u;
}

/**
*
* Comprueba si el rango del usuario es super administrador
*
**/

function es_super_admin() {
	global $zerdb;
	if( ! sesion_iniciada() )
		return false;

	$u = obt_usuario_actual();
	$id = $u->id;

	$u = new extraer($zerdb->usuarios, "rango", array("id" => $id) );

	return true == ( (int) $u->rango == 1);
}

/**
*
* Comprueba si el rango del usuario es administrador
*
**/

function es_admin() {
	global $zerdb;

	if( ! sesion_iniciada() )
		return;

	$u = obt_usuario_actual();
	$id = $u->id;

	$u = @new extraer($zerdb->usuarios, "rango", array("id" => $id) );

	return true == ( (int) $u->rango == 2 || es_super_admin() );
}


/**
*
* Comprueba si el rango del usuario es actualizador
*
**/

function es_actualizador() {
	global $zerdb;
	if( ! sesion_iniciada() )
		return;

	$u = obt_usuario_actual();
	$id = $u->id;

	$u = @new extraer($zerdb->usuarios, "rango", array("id" => $id) );

	return true == ( (int) $u->rango == 3 || es_admin() );
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
	$u = new extraer($zerdb->usuarios, "*", array("id" => $zerdb->proteger($id)) );
	if( false == ($u && $u->nums > 0 ) )
		return false;
	switch( $u->id ) {
		case "1":
		return __("Super Administrador");
		break;
		case "2":
		return __("Administrador");
		break;
		case "3":
		return __("Actualizador");
		break;
		default:
		return __("No tiene rango .-. ):"); /// eeeeeeeeey troooooooooooll xdddddd
	}
}
function existe_usuario_id( $id ) {
	global $zerdb;
	$id = $zerdb->proteger( $id );
	$u = new extraer($zerdb->usuarios, "*", array("id" => $id) );
	return true == ($u && (int) $u->nums > 0 );
}

function comprobar_rangos( $rango1, $rango2 = '') {
	if( ! is_numeric($rango1) )
		return false;

	$rango1 = (int) $rango1;

	if( $rango1 !== 1 || 2 || 3)
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

	$q = new extraer($zerdb->usuarios, "*", array("email" => $zerdb->proteger( $email ) ) );

	return true == (  $q && $q->nums > 0);
}

function estado( $estado ) {
	global $zerdb;
	return ( (int) $estado == 1 ) ? __("Activo") : __("Suspendido");
}

function eliminar_usuario( $id ) {
	global $zerdb;
	
	if( !existe_usuario_id($id) )
		return false;

	$u = obt_usuario( $id );

	if( $u->rango == '1')
		return false;

	return $zerdb->eliminar( $zerdb->usuarios, array("id" => $zerdb->proteger( $id ) ) );
}

function esta_suspendido( $id ) {

	$u = obt_id( $id );

	return (int) $u->estado !== 1;
}

function suspender_usuario( $id ) {
	global $zerdb;

	if( ! existe_usuario_id($id) || esta_suspendido($id) )
		return false;

	return $zerdb->actualizar($zerdb->usuarios, array("estado" => "0"), array("id" => $id) );
}

function quitar_suspension( $id ) {
	global $zerdb;

	if( ! existe_usuario_id($id) || ! esta_suspendido($id) )
		return false;

	return $zerdb->actualizar( $zerdb->usuarios, array("estado" => "1"), array("id" => $id) );
}