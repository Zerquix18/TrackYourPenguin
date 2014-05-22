<?php
/**
*
* Funciones para manejo de Trackers
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.1
*
**/
/**
*
* Obtiene el tracker desde su ID
*
* @param $id int | str
* @return bool | array
*
**/
function obt_tracker( $id = null ) {
	global $zerdb;
	$u = $zerdb->select($zerdb->trackers, "*", array("id" => $id) )->_();
	return $u && $u->nums > 0 ? $u : false;
}
/**
*
* Agrega un tracker
*
* @param $nombre string
* @param $img string
* @param $imgbg string
* @param $fuente string
*
**/
function agregar_tracker($nombre, $img, $imgbg, $fuente) {
	global $zerdb;

	$zerdb->insert( $zerdb->trackers, array(
			$nombre, $img, $imgbg, $fuente
		)
	);

	$id = $zerdb->id;
	
	$zerdb->insert( $zerdb->parametros, array(
			$id, '1', '16', '0', '120', '130'
		)
	);
	$zerdb->insert( $zerdb->parametros, array(
			$id, '2', '16', '0', '135', '177'
		)
	);
	$zerdb->insert( $zerdb->parametros, array(
			$id, '3', '16', '0', '75', '220'
		)
	);
}
/**
*
* Comprueba que exista un tracker
*
* @param $id int | str
*
**/

function existe_tracker( $id ) {
	return false !== obt_tracker($id);
}