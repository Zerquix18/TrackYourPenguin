<?php
/**
*
* Porque siempre llega la hora de bregar con AJAX :)
*
* @author Zerquix18
* @since 1.0
* @link http://trackyourpenguin.com/
* @package TrackYourPenguin
*
**/

require_once("../typ-load.php");

if( ! comprobar_args( @$_POST['accion']) || vacio($_POST['accion']) )
	return exit(0); // fak u fgt.

switch($_POST['accion']) {
	case "sesion":
	if( ! sesion_iniciada() ) {
		$url = comprobar_args($_POST['href']) && ! vacio($_POST['href']) ? trim($_POST['href']) : url();
		echo json_encode( array("estado" => 0, "mensaje" => __("Please, log in to continue...") . redireccion( url() . 'acceso.php?continuar=' . urlencode($url), 3 ) ) );
	}else{
		echo json_encode( array("estado" => 1 ) );
	}
	default:
	return exit(0); // fak u fgt.
}