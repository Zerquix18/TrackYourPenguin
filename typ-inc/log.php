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
	$fecha = vacio($fecha) ? time() : $fecha;
	return $zerdb->insert( $zerdb->log, array($accion, $fecha ) );
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
	return $zerdb->delete( $zerdb->log, array("id" => $zerdb->real_escape( $id ) ) )->_();
}
/**
*
* Muestra la fecha. Si es numérica (en formato Unix) la devuelve con la función de obt_fecha y obt_hora
* De lo contrario, la muestra
*
* @param $fecha int | str
* @return string
* @since 0.2
*
**/
function mostrar_fecha( $fecha, $segs = false ) {
	if( ! is_numeric($fecha) )
		return $fecha; // Por actualizaciones anteriores :p ^-^
	return obt_fecha( $fecha, false ) . __(' a la(s) ') . obt_hora( $fecha, $segs );
}
/**
*
* Decodifica el JSON de la actualización. Si lo devuelto se trata de una actualización anterior (string, ya que no es JSON) en el
* que no se usaba JSON, entonces lo devuelve así mismo.
*
*
* @param $fecha str
* @return string
*
**/
function mostrar_log( $log ) {
	if( is_string($r = @json_decode( $log ) ) )
		return $log; // actualizaciones anteriores, no pierdas la estabilidad :o
	$usuario = isset($r->usuario) && $r->usuario == $_SESSION['usuario'] ? __('Tú actualizaste ') : sprintf( __('<strong>%s</strong> actualizó ') );
	$texto = isset($r->usuario, $r->tracker) ? $usuario  . sprintf( __("el tracker de <strong>%s</strong> \n"), is_numeric($r->tracker) ? obt_tracker($r->tracker)->personaje : $r->tracker ) : '';
	$texto .= isset($r->estado) ? sprintf( __("Estado: %s \n"), $r->estado) : '';
	$texto .= isset($r->servidor) ? sprintf( __("Servidor: %s \n"), $r->servidor ) : '';
	$texto .= isset($r->sala) ? sprintf( __("Sala: %s \n"), $r->sala ) : '';
	$texto .= isset($r->tweet) ? sprintf( __("Tweet elegido: %s"), $r->tweet ) : '';
	return $texto;
}