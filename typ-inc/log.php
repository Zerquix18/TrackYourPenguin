<?php
/**
*
* Funciones para el log
*
* Funciones necesarias para el log
*
* @author Zerquix18
* @package TrackYourPenguin
* @since 0.1
*
**/
/**
*
* Agrega algo al log
*
* @param $accion string
* @param $fecha string
* @since 0.1
*
**/
function agregar_log( $accion, $fecha = '' ) {
	global $zerdb;
	$fecha = vacio($fecha) ? retornar_fecha() : $fecha;
	return $zerdb->insertar( $zerdb->log, array($accion, $fecha ) );
}
/**
*
* Elimina algo del log
*
* @param $id str | int
* @since 0.1
*
**/
function eliminar_log( $id ) {
	global $zerdb;
	if( ! existe_log_id( $id ) )
		return false;

	return $zerdb->eliminar( $zerdb->log, array("id" => $zerdb->proteger( $id ) ) );
}