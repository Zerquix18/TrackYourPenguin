<?php
/**
*
* Clase y funciones de Twiter
*
* Manipula todo lo que tengan que ver con Tweets y OAuth en el sistema
*
* @package TrackYourPenguin
* @author Zerquix18 <http://trackyourpenguin.com/>
* @since 0.1
* 
*
**/

require_once(PATH . INC . 'twitteroauth.php'); //Lib de Abraham

class zer_twitter {

	/**
	*
	* Comprueba si existe un error en la petición
	*
	* @access public
	* @var bool
	* @since 0.1
	*
	**/
	public $comp_error = false;
	/**
	*
	* El error dato si $comp_error está en true
	*
	* @access public
	* @var string
	* @since 0.1
	*
	**/
	public $error = '';
	/**
	*
	* Último tweet enviado
	*
	* @access public
	* @var string
	* @since 0.1
	*
	**/
	public $ult_tweet = null;
	/**
	*
	* ID del último tweet enviado, también sirve $this->id
	*
	* @access public
	* @var string
	* @since 0.1
	*
	**/
	public $tweet_id = null;

	public function __construct($a, $b, $c, $d) {
		return $this->tw = new TwitterOAuth($a, $b, $c, $d);
	}
	/**
	*
	* Obtiene el error según el código dado por Twitter
	*
	* Los códigos los puedes encontrar en {@link https://dev.twitter.com/docs/error-codes-responses/}
	*
	* @access public
	* @var string
	* @since 0.1
	*
	**/
	function obt_error( $codigo ) {
		if( ! is_int($codigo) )
			return false;
		switch($codigo) {
			case 200:
			$error = __('200 - OK');
			break;
			case 304:
			$error = __('Error 304: No modificado - La URL de la petición no fue modificada');
			break;
			case 400:
			$error = __('Error 400: Mal solicitado - La solicitud hecha no es válida');
			break;
			case 401:
			$error = __('Error 401: No autorizado - La aplicación no cuenta que los permisos suficientes (lectura y escritura)');
			break;
			case 403:
			$error = __("Error 403: Prohibido - Acceso no permitido, la aplicación no puede conectar a Twitter");
			break;
			case 404:
			$error = __("Error 404: No encontrado - La URL no ha sido encontrada");
			break;
			case 406:
			$error = __("Error 406: No aceptable - El servidor no fue capaz de procesar la petición debido a los formatos");
			break;
			case 410:
			$error = __("Error 410: Ya no disponible - La URL de la petición ya no está disponible ni lo estará");
			break;
			case 420:
			$error = __("Error 420: Has superado el límite de búsquedas");
			break;
			case 422:
			$error = __("Error 422: No pasable - La imagen que intentas subir no puede ser procesada");
			break;
			case 429:
			$error = __("Error 429: Demasiadas solicitudes - Has llegado al límite de solicitudes");
			break;
			case 500:
			$error = __("Error 500: Error interno del servidor - Algo está roto. Twitter ha fallado en la solicitud");
			break;
			case 502:
			$error = __("Error 502: Pasarela incorrecta - Twitter está de baja o está siendo actualizado");
			break;
			case 503:
			$error = __("Error 503: Servicio no disponible - Twitter no está de baja, pero sí recargado de solicitudes");
			break;
			case 504:
			$error = __("Error 504: Tiempo agotado - El tiempo para hacer la petición ha tardado demasiado");
			break;
			case 32:
			$error = __("Error: No se pudo autenticar - La autenticación ha fallado");
			break;
			case 34:
			$error = __("Error (404): No encontrado - La página no existe");
			break;
			case 68:
			$error = __("El URL solicitado correspondía a la versión anterior de la API. Por favor actualiza");
			break;
			case 88:
			$error = __("Error: Límite de velocidad - Se alcanzó el límite de velocidad de este recurso");
			break;
			case 89:
			$error = __("Error: Clave de autenticación inválida o expirada - La clave de acceso usada está expirada o es inválida");
			break;
			case 64:
			$error = __("Error (403): Tu cuenta está suspendida y no le está permitida hacer esta acción");
			break;
			case 131:
			$error = __("Error (500): Error interno del servidor - Twitter ha fallado en la petición");
			break;
			case 135:
			$error = __("Error (401): No se puede autenticar - Quiere decir que la hora de este servidor está más adelante o detrás que la del rango aceptable de Twitter");
			break;
			case 187:
			$error = __("Error: El tweet ha sido duplicado");
			break;
			case 215:
			$error = __("Error (400): Hacer esto requiere una autenticación válida. Recuerda que necesitas 4 parámetros (consumer_key, consumer_secret, access_token y access_token_secret)");
			break;
			default:
			$error = __("Error no conocido :/");
		}
		return $error;
	}
	/**
	*
	* ¡Manda un tweet!
	*
	* @access public
	* @since 0.1
	* @param $tweet string
	*
	**/
	function tuitear( $tweet ) {
		if( ! is_string($tweet) )
			return false;

		$this->tweet = $this->tw->post('statuses/update', array("status" => $tweet ) );
		if( isset($this->tweet->errors) ) {
			$this->comp_error = true;
			$this->error = $this->obt_error( $this->tweet->errors[0]->code );
			return false;
		}elseif( isset($this->tweet->error)) {
			$this->comp_error = true;
			$this->error = __("Error: La aplicación no cuenta con los permisos suficientes. Recuerda que deben ser lectura y escritura y están en sólo lectura");
			return false;
		}
		/* valores retornados por twitter*/
		$this->fecha = $this->tweet->created_at; // fecha de creación
		$this->id = $this->tweet->id_str; //el ID del tweet
		$this->tweet_id = $this->id;
		$this->usuario = $this->tweet->user->screen_name; // (str) nombre del usuario
		$this->usuario_id = $this->tweet->user->id; // (int) ID del usuario
		$this->url = 'http://twitter.com/' . $this->usuario_id . '/status/' .  $this->tweet_id. '/';
		return true;
	}
	/* Fin de la clase */
}
/**
*
* Obtiene el OAuth de todas las tablas
*
* @access public
* @return array
* @since 0.1
*
**/
function obt_oauth() {
	global $zerdb;
	return new extraer($zerdb->twitter, "*");
}
/**
*
* @access public
* @return string
* @since 0.1
* @param $key string
*
**/
function obt_key( $key ) {
	global $zerdb;
	$keys = array("consumer_key", "consumer_secret", "access_token_secret", "access_token");
	if( ! is_string($key) || ! in_array($key, $keys) )
		return false;
	$k = new extraer($zerdb->twitter, $key);
	return $k->$key;
}
/**
*
* Inserta el OAuth
*
* @access public
* @since 0.1
* @param $a string
* @param $b string
* @param $c string
* @param $d string
*
**/
function insertar_oauth($a, $b, $c, $d) {
	global $zerdb;
	$args = func_get_args();
	foreach( $args as $arg )
		if( ! is_string($arg) )
			return false;
	return $zerdb->insertar($zerdb->twitter, array($a, $b, $c, $d ) );
}
/**
*
* Actualiza el OAuth
*
* @access public
* @since 0.1
* @param $a string
* @param $b string
* @param $c string
* @param $d string
*
**/
function actualizar_oauth($a, $b, $c, $d) {
	global $zerdb;
	$args = func_get_args();
	foreach( $args as $arg )
		if( ! is_string($arg) )
			return false;
		
	return $zerdb->actualizar($zerdb->twitter,
			array("consumer_key" => $a, "consumer_secret" => $b, "access_token" => $c, "access_token_secret" => $d)
		);
}
/**
*
* Comprueba si el OAuth ya fue configurado
*
* @return bool
* @since 0.1
* @access public
*
**/
function oauth_configurado() {
	$o = obt_oauth();

	return true == ($o && $o->nums > 0);
}
/**
* Obtiene un tweet desde su ID
*
* @param $id str | int
*
**/

function obt_tweet( $id ) {
	global $zerdb;
	return new extraer($zerdb->tweets, "*", array("id" => $zerdb->proteger($id) ) );
}

/**
* Obtiene el texto del tweet
*
* @since 0.1
* @param $id str | int
*
**/

function obt_tuit( $id ) {
	$t = obt_tweet($id);
	if( false == ($t && $t->nums > 0 ) )
		return false;
	return $t->tweet;
}

/**
* Obtiene todos los tweets
**/
function obt_tweets() {
	global $zerdb;

	return new extraer($zerdb->tweets, "*");
}
/**
*
* Agrega un tweet
*
* @param $nombre str
* @param $texto str
*
**/
function agregar_tweet( $nombre, $texto ) {
	global $zerdb;

	if( ! is_string($nombre) || ! is_string($texto) ) // 0 truqueos eh...
		return false;

	if( strlen($nombre) > 10 || strlen($texto) > 140 )
		return false;

	return $zerdb->insertar( $zerdb->tweets, array($nombre, $texto) );

}
/**
*
* Actualiza un tweet
*
* @param $id int | str
*
* @param $parametros str | int
*
**/
function actualizar_tweet($id, $parametros) {
	global $zerdb;

	if( ! is_numeric($id) || ! is_array($parametros) || ! array_key_exists(0, $parametros) || ! array_key_exists(1, $parametros) )
		return false;

	if( ! is_string($parametros[0]) || ! is_string($parametros[1]) || array_key_exists(2, $parametros) )
		return false;

	$nombre = $parametros[0];
	$texto = $parametros[1];

	if( strlen($nombre) > 10 || strlen($texto) > 140)
		return false;

	$nombre = $zerdb->proteger($nombre);
	$texto = $zerdb->proteger($texto);

	$actualizar = $zerdb->actualizar( $zerdb->tweets,
			array("nombre" => $nombre, "tweet" => $texto),
			array("id" => $zerdb->proteger($id) )
		);

	return $actualizar;

}
/**
* Comprueba si existe un tweet
*
* @param $id str | int
*
**/
function existe_tweet( $id ) {
	global $zerdb;
	$id = $zerdb->proteger($id);
	$comprobar = new extraer($zerdb->tweets, "*", array("id" => $id) );
	return true == ( $comprobar && $comprobar->nums > 0 );
}
/**
*
* Eliminar un tweet
*
* @param $id str | int
*
**/
function eliminar_tweet( $id ) {
	global $zerdb;
	if( ! existe_tweet($id) )
		return false;
	$id = $zerdb->proteger($id);
	$eliminar = $zerdb->eliminar( $zerdb->tweets, array("id" => $id ) );
	return $eliminar;
}
/**
*
* Obtiene un tweet desde una columna
*
* @param $por str
* @param $donde array
*
**/
function obt_tweet_por($por, $donde) {
	global $zerdb;

	if( ! is_string($por) || !is_string($donde) )
		return false;

	$por_ = array("id", "nombre", "tweet");

	if( ! in_array($por, $por_) )
		return false;
	$d = array();

	foreach($donde as $a => $b) {
		$d[ $zerdb->proteger($a) ] = $zerdb->proteger($b);
	}

	$t = new extraer( $zerdb->trackers, "*", $d );

	if( $t && $t->nums > 0)
		return $t->$por;
	else
		return false;
}
/**
*
* Comprueba si el usuaro tiene tuits
*
**/
function tiene_tweets() {
	$t = obt_tweets();
	return true == ( $t && $t->nums > 0 );
}