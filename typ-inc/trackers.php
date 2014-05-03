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
	if( ! is_numeric($id) || is_null($id) )
		return false;
	return new extraer($zerdb->trackers, "*", array("id" => $id) );
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

	$zerdb->insertar( $zerdb->trackers, array(
			$nombre, $img, $imgbg, $fuente
		)
	);

	$id = mysql_insert_id();
	$zerdb->insertar( $zerdb->parametros, array(
			$id, '1', '16', '0', '120', '130'
		)
	);
	$zerdb->insertar( $zerdb->parametros, array(
			$id, '2', '16', '0', '135', '177'
		)
	);
	$zerdb->insertar( $zerdb->parametros, array(
			$id, '3', '16', '0', '75', '220'
		)
	);
}
/**
*
* Actualiza el tracker elegido
*
* @param $id int | str
* @param $nombre str
* @param $fuente str
*
**/
function actualizar_tracker($id, $nombre, $fuente) {
	global $zerdb;

	if( ! is_numeric($id) || !is_string($nombre) || !is_string($fuente) )
		return false;
	$id = $zerdb->proteger($id);
	$nombre = $zerdb->proteger($nombre);
	$fuente = $zerdb->proteger($fuente);
	
	return 	$zerdb->actualizar( $zerdb->trackers,
					array("personaje" => $nombre, "fuente" => $fuente),
					array("id" => $id)
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
	global $zerdb;

	$id = $zerdb->proteger( $id );
	$t = obt_tracker( $id );

	if( ! $t || ! $t->nums > 0 )
		return FALSE;

	return true;
}

/**
*
* Elimina un tracker desde su ID
*
* @param $id string
* @return bool
*
**/

function eliminar_tracker( $id ) {
	global $zerdb;

	if( ! existe_tracker($id) )
		return false;

	$tracker  = obt_tracker( $id );

	//borra las imágenes
	@unlink($tracker->imgbg);
	@unlink($tracker->img);

	$q = $zerdb->eliminar($zerdb->trackers, array("id" => $id) );
	$q = $zerdb->eliminar($zerdb->parametros, array("tracker" => $id) );

	return true;
}
/**
*
* Obtiene un tracker desde una columna
*
* @param $por str
* @param $donde array
*
**/
function obt_tracker_por( $por, $donde ) {
	global $zerdb;

	if( ! is_string($por) || !is_string($donde) )
		return false;

	$por_ = array("id", "personaje", "img", "imgbg", "fuente");
	if( ! in_array($por, $por_) )
		return;
	$d = array();

	foreach($donde as $a => $b) {
		$d[ $zerdb->proteger($a) ] = $zerdb->proteger($b); //proteccion
	}

	$t = new extraer( $zerdb->trackers, "*", $d );

	if( $t && $t->nums > 0)
		return $t->$por;
	else
		return '';
}