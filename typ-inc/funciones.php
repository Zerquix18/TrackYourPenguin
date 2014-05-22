<?php
/**
* Carga algunas funciones varias muy necesarias en el sistema
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.1
*
**/
//----------------
/**
*
* Da seguridad a los argumentos
*
* Para que no nos trolleen desde afuera.
*
* @since 0.1
*
*
**/
function comprobar_args() {

	$args = func_get_args();

	if( ! array_key_exists(0, $args) )
		return false;

		foreach($args as $a)
			if( ! isset($a) || !is_string($a) ) //tiene que ser string lo que entre, o nada :'3
				return false;

	return true;
}
/**
*
* Comprueba que un string no esté vacío
*
* @since 0.1
*
**/
function vacio( $string ) {
	if( ! is_string($string) )
		return true;
	if( $string === "0")
		return false; 

	$string = trim($string);

	return empty( $string );
}
/**
*
* Comprueba que varios argumentos no estén vacíos
*
* @since 0.1
*
**/
function vacios() {

	$args = func_get_args();

	if( ! array_key_exists(0, $args) )
		return false;

	foreach($args as $a)
		if( vacio($a) )
			return true;

	return false;
}
/**
*
* Devuelve el título del sitio
*
* @since 0.1
*
**/
function titulo() {
	global $zerdb;
	if( ! comprobar_instalacion() )
		return false;
	$t = $zerdb->select($zerdb->config, "titulo");
	$t = $t->execute();
	return $t->titulo;
}
/**
*
* Alias para título
*
* @since 0.1
*
**/
function nombre() {
	return titulo();
}
/**
*
* Ayuda a la función typ_die
*
**/
function typ_die_helper( $error = null ) {
	if(! is_string($error) )
		return die();
	?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="<?php echo url() . INC . CSS . 'cyborg.css' ?>" rel="stylesheet">
  <style type="text/css">
    body { padding-top: 60px; 
      padding-bottom: 40px; 
    }
  </style>
  <link href="<?php echo url() . INC . CSS . 'cyborg.min.css' ?>" rel="stylesheet">
  <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body>
  <div class="container">
 	<div class="hero-unit">
	<?php agregar_error( $error, false, false ) ?>
	</div>
  </div> 
</body>
</html>
	<?php
}
/**
*
* Lanza un error y cancela el mensaje de abajo
*
* @param $error string
* @since 0.1
*
**/
function typ_die( $error = null ) {
	ob_clean();
	return exit( typ_die_helper( $error ) );
}
/**
*
* Comprueba si se inició sesión
* @param bool $inverso Si es True comprueba que NO se haya iniciado.
*
**/
function comprobar( $inverso = false) {
	global $sesion;
	switch($inverso) {
		case true:
		if( sesion_iniciada() )
			return header("Location: " . url() );
		break;
		case false:
		default:
		if( ! sesion_iniciada() )
			return  header("Location: " . url() . 'acceso.php?continuar=' . urlencode( url(true) ) );

		$u = obt_id( $_SESSION['id'] );
		if( (int) $u->estado !== 1 ) {
			$sesion->destruir();
			return header("Location: " . url() . 'acceso.php?continuar=' . urlencode( url(true) ) );
		}
	}
}
/**
*
* Comprueba si se instaló
*
* @since 0.1
* @return bool
*
**/
function comprobar_instalacion() {
	global $zerdb;
	if( ! $zerdb->ready )
		return false;
	$query = $zerdb->select($zerdb->usuarios, "*")->_();
	return ( isset($query) && @$query->nums > 0 );
}
/**
*
* Comprueba que se haya iniciado sesión
*
* @since 0.1
* @return bool
*
**/
function sesion_iniciada() {
	if(! comprobar_instalacion() )
		return header("Location: instalar.php");
	return $GLOBALS['sesiones']->sesion_iniciada();
}
/**
*
* Envía un email
*
* @since 0.1
* @param $email string
* @param $asunto string
* @param $mensaje string
*
**/
function enviar_email($email = null, $asunto = null, $mensaje = null) {
	if( is_null($email) or is_null($asunto) or is_null($mensaje) )
		return false;

	$admin = 'soporte@trackyourpenguin.com';
	$cabeceras = 'TrackYourPenguin' . " <" . $admin . ">";
	return mail($email, $asunto, $mensaje, $cabeceras);
}
/**
*
* Devuelve la URL
*
* @since 0.1
* @param $actual bool
*
**/
function url($actual = false) {
	global $zerdb;
	if(! comprobar_instalacion() )
		return 'http://' . $_SERVER['HTTP_HOST'] . dirname( $_SERVER['PHP_SELF'] ) . '/';

	$c = $zerdb->select($zerdb->config, "*")->_();
	$q = ( !empty($_SERVER['QUERY_STRING'] ) ) ? '?' . $_SERVER['QUERY_STRING'] : '';
	$slash = ! preg_match('/[\/]$/', $c->url) ? '/' : '';

	if( $actual )
		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $q;
	else
		return $c->url . $slash;
}
/**
*
* Lanza un mensaje de error
*
* @since 0.1
* @param $error string
* @param $cerrar bool
* @param $mensaje bool
*
**/
function agregar_error( $error = null, $cerrar = false, $mensaje = true) {
	if( is_null($error) )
		return false;

	$close = true == $cerrar ? '<a class="close" data-dismiss="alert" href="#">&times;</a>' : '';
	$msg = true == $mensaje ? '<i class="icon-remove"></i> <strong>Error:</strong> ' : '';
  echo '<div class="alert alert-error">' . $close . ' ' . $msg . $error . '</div>';
}
/**
*
* Lanza un mensaje de info
*
* @since 0.1
* @param $error string
* @param $cerrar bool
* @param $mensaje bool
*
**/
function agregar_info( $info = null, $cerrar = false, $mensaje = true ) {
	if( is_null($info) )
		return false;
	
	$close = true == $cerrar ? '<a class="close" data-dismiss="alert" href="#">&times;</a>' : '';
	$msg = true == $mensaje ? '<i class="icon-ok"></i> <strong>Info:</strong> ' : '';
  echo '<div class="alert alert-info">'. $close  . $info . '</div>';
}
/**
*
* Redirecciona vía HTML
*
* @since 0.1
* @param $a_sitio string
* @param $segundos bool | string
*
**/
function redireccion($a_sitio = false, $segundos = 2) {
	if( ! $a_sitio )
		return false;
	
	return '<meta http-equiv="refresh" content="' . $segundos . ';url=' . $a_sitio . '">';
}
/**
*
* Comprueba que el usuario esté en debido archivo
*
* @since 0.1
* @param $donde string
* @param $server string
*
**/
function es( $donde, $server = "PHP_SELF" ) {
	return preg_match("#" . $donde . "#", $_SERVER[ strtoupper($server) ] );
}
/**
*
* Hace exactamente la misma función que sleep(), pero manejando los buffers de salida. Evita que sleep duerma antes de lanzar todo
*
* @param $segundos int
* @return void
*
**/

function dormir( $segundos = 2 ) {
	ob_end_flush();
	flush();
	ob_flush();
	@sleep($segundos);
	ob_start();
}
/**
*
* Genera un hash aleatorio con la cantidad de $cantidad
*
* @param $cantidad int
* @param $simbolos bool
* @param $mas_simbolos bool
* @return string
* @since 0.2
*
*/
function generar_hash( $cantidad = 0, $simbolos = true, $mas_simbolos = false ) {
	if( ! (int) $cantidad > 0 )
		return;
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWYZ0123456789";
	if( $simbolos )
		$chars .= "\/!$&=¡";
	if( $mas_simbolos )
	$chars .= "{}`´+«»@~¬[]|\"";

	$sufijo = 'typ_'; // si te sientes seguro, puedes editar el sufijo que va antes de los hashes. PD: No afectará los ya creados.

	$clave = substr( str_shuffle($chars), 0, $cantidad);

	return substr($sufijo . $clave, 0, - strlen($sufijo) );
}
/**
*
* Reemplaza los parámetros del tuit y elimina los espacios en blanco
*
* @param $texto string
* @param $datos array
* @since 0.2
* @return string
*
**/
function reemplazar_tweet( $texto, $datos ) {
	$texto = trim($texto);
	$tuit = array(
			"%es" => $datos['estado'],
			"%se" => $datos['servidor'],
			"%sa" => $datos['sala'],
			"%cp" => "#ClubPenguin",
			"%r" => rand( 1, 100 ), // del 1 al 100 el RAND...
			"%pe" => preg_match("/^(\#\%pe)+/i", $texto ) ? //si es al principio el %pe, reemplaza los espacios por '' en el nombre del personaje para que no haya crash en el hashtag.
			preg_replace('\s', '', trim($datos['personaje']) ) : $datos['personaje']
		);

	return str_replace( array_keys( $tuit ), array_values( $tuit ), $texto );
}
function _f( $string, $echo = true ) {
	if( ! isset($string) || ! is_string($string) )
		return '';
	$value = sprintf('value="%s"', $string);
	if( $echo )
		echo $value;
	else
		return $value;
}

function modal_tweets() { ?>
<div id="cc" class="modal hide fade" role="dialog" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><u><?php _e("Códigos cortos") ?></u></h3>
  </div>
  <div class="modal-body">
	<?php _e('Son pequeños códigos que puedes usar para enviar al tweet cosas como la sala, servidor, estado, etc. Sólo tienes que añadirlos en los tweets'); echo '<br>';
	echo '<strong>%es</strong>&nbsp;<i>' . __('Esto imprime el estado. Ejemplo: El estado ahora es %es') . '</i><br>';
	echo '<strong>%se</strong>&nbsp;<i>' . __('Esto imprime el servidor. Ejemplo: #Alguien está en el servidor: %se') . '</i><br>';
	echo '<strong>%sa</strong>&nbsp;<i>' . __('Esto imprime la sala. Ejemplo: #Alguien está en la sala %sa') . '</i><br>';
	echo '<strong>%cp</strong>&nbsp;<i>' . __('Esto imprime "#ClubPenguin", sirve para indicar que tus tuits están relacionados con ello') . '</i><br>';
	echo '<strong>%r</strong>&nbsp;<i>' . __('Esto imprime un número del 1 al 100, para que los tweets no se repitan.') . '</i><br>';
	echo '<strong>%pe</strong>&nbsp;<i>' . __('Esto imprime el personaje (nombre del tracker) que asignaste. Ejemplo: #%pe ha sido encontrado en [...]') . '</i><br>';
	echo __('<strong>Ejemplo normal</strong><br>
	<i>#%pe ha sido encontrado en la sala %sa (%r) %cp</i> = #*nombredeltracker* ha sido encontrado en la sala *sala enviada* (*número del 1 al 100*) #ClubPenguin</i>');
	?>
  </div>
</div>
<?php
}