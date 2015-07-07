<?php
/**
*
* Archivo de traducciones
*
* @author Zerquix18
* @package TrackYourPenguin
* @since 0.1.1
*
**/

$preg = sprintf("#%s#", basename(__FILE__) );
if( preg_match($preg, $_SERVER['PHP_SELF'])) exit();


//archivos necesarios para la traduccion
require_once( PATH . INC . 'i18n/gettext.php');
require_once( PATH . INC . 'i18n/streams.php');

/**
*
* Obtiene el lenguaje actual
*
* Hasta ahora, sólo un lenguaje permitido
*
**/

$lenguajes = array("es_ES", "en_US");

function obt_lenguaje() {
	global $lenguajes;
	if( ! defined("TYP_LANG") )
		return 'en_US';
	$lenguaje = constant("TYP_LANG");
	$posibles_leng_es = array("es", "español", "spanish", "esp", "espanol"); //por si las fallas...
	$posibles_leng_en = array("en", "english", "eng", "ing", "ingles");
	if( in_array( strtolower($lenguaje), $posibles_leng_es) )
		return "en_US";
	if( in_array( strtolower($lenguaje), $posibles_leng_en) )
		return "en_US";
	if( ! in_array( $lenguaje, $lenguajes) )
		return "en_US";
	else
		return $lenguaje;
}

// requerimos al lenguaje, dando excepción de que no sea el default.

if( obt_lenguaje() !== "en_US" && file_exists(PATH . INC . LANG . obt_lenguaje() . '.mo') ):
	$tr = new gettext_reader( new CachedFileReader( PATH . INC . LANG . obt_lenguaje() . '.mo' ) );
	$tr->load_tables();
	#_textdomain('default');
else:
	$tr = new gettext_reader(null);
endif;
/**
*
* Devuelve el mensaje, si necesita traducción lo traduce
*
**/
function __( $texto ) {
	return $GLOBALS['tr']->translate( $texto );
}

/**
*
* Lanza el mensaje
*
*
**/
function _e( $texto ) {
	echo __($texto);
}
/**
*
* Forma plural de una cadena
* Ejemplo: _n("Juan tiene %d gato", "Juan tiene %d gatos", 1);
*
**/
function _n( $singular, $plural, $numero ) {
	global $tr;
	if( (int) $numero = 1 )
		return $singular;
	else
		return $plural;
}
/**
*
* Quita el HTML y lo devuelve
*
**/
function esc_html( $texto ) {
	return htmlspecialchars( __($texto) );
}
/**
*
* Lanza con la funcion anterior
*
**/
function esc_html_e( $texto ) {
	echo esc_html($texto);
}
/**
*
* Actualiza el lenguaje
* @since 1.1
*
*/
function actualizar_lenguaje( $lenguaje ) {
	global $lenguajes;
	if( ! in_array($lenguaje, $lenguajes) )
		return false;
	$c = file_get_contents( PATH . 'typ-config.php');
	if( ! $c )
		return false;
	$c = str_replace( TYP_LANG, $lenguaje, $c);
	file_put_contents(PATH.'typ-config.php', $c);
	return true;
}

$lenguajest = array(
		"en_US"=> __("English"),
		"es_ES" => __("Spanish"),
	);