<?php

/**
* Archivo de carga, reune los demás y es la cabecilla de todo el sistema.
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.1
*
**/

ob_start();
ob_implicit_flush( true );
session_start();
header('Content-Type: text/html; charset=utf-8');
define("INC", "typ-inc/");
define("CSS", "css/");
define("JS", "js/");
define("IMG", "img/");
define("LANG", "lang/");
define("PATH", dirname(__FILE__) . '/');
define("TRACKERS_PAG", (defined("TRACKERS_PAG")) ? (int) TRACKERS_PAG : 4);
define("VERSION", "0.2");
$GLOBALS['v'] = constant("VERSION");
$config = file_exists(PATH . 'typ-config.php') ? 'typ-config.php' : 'typ-config-sample.php';
require_once( PATH . $config );
require_once( PATH . INC . 'i18n.php');
require_once( PATH . INC . 'hora.php');
require_once( PATH . INC . 'class-db.php');
$zerdb = new zerdb( @DB_HOST, @DB_USUARIO, @DB_CLAVE, @DB_NOMBRE);
require_once( PATH . INC. 'funciones.php');
require_once( PATH . INC . 'class-sesion.php');
$sesion = $sesiones = new sesiones();
require_once( PATH . INC . 'class-archivos.php');
$archivos = new archivos();
require_once( PATH . INC . 'class-actualizar.php');
require_once( PATH . INC . 'class-twitter.php');
require_once( PATH . INC . 'cuerpo.php' );
require_once( PATH . INC . 'usuarios.php' );
require_once( PATH . INC . 'trackers.php' );
require_once( PATH . INC . 'params.php' );
require_once( PATH . INC . 'log.php' );
$preg = ! preg_match("/instalar.php/", $_SERVER['PHP_SELF']) ;

if( true !== $zerdb->listo && $preg || ! comprobar_instalacion() && $preg )
	exit( header("Location: instalar.php") );