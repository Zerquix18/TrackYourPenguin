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
	if(! $u || $u->nums == 0)
		return false;

	return true;
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
*
**/

function obt_usuario_actual() {
	global $zerdb;
	if( ! sesion_iniciada() )
		return false;

	$u = obt_id( $_SESSION['id'] );
	unset($u->clave);
	return $u;
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

	if( (integer) $u->rango == 1)
		return true;

	return false;
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

	if( (integer) $u->rango == 2 or es_super_admin() )
		return true;

	return false;
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

	if( (integer) $u->rango == 3 or es_admin() )
		return true;

	return false;
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
	if( ! is_numeric($id) || is_null($id) )
		return false;

	$u = @new extraer($zerdb->usuarios, "*", array("id" => $id) );
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
	if(! $u || $u->nums == 0)
		return false;

	return true;
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

	if( ! $q || ! $q->nums > 0)
		return false;

	return true;
}

function estado( $estado ) {
	global $zerdb;

	if( $estado == '1' )
		return "Activo";
	else
		return "Suspendido";
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

	if( $u->estado !== "1")
		return true;

	return false;
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