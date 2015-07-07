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
			$error = __('Error 304: Not Modified - There was no new data to return.');
			break;
			case 400:
			$error = __('Error 400: Bad Request - The request was invalid or cannot be otherwise served.');
			break;
			case 401:
			$error = __('Error 401: Unauthorized - Authentication credentials were missing or incorrect.');
			break;
			case 403:
			$error = __("Error 403: Forbidden - The request was understood, but it has been refused or access is not allowed.");
			break;
			case 404:
			$error = __("Error 404: Not found - The URI requested is invalid or the resource requested, such as a user, does not exists.");
			break;
			case 406:
			$error = __("Error 406: Not acceptable - Returned by the Search API when an invalid format is specified in the request.");
			break;
			case 410:
			$error = __("Error 410: Gone - This resource is gone.");
			break;
			case 420:
			$error = __("Error 420: Returned by the version 1 Search and Trends APIs when you are being rate limited.");
			break;
			case 422:
			$error = __("Error 422: Unprocessable Entity - Returned when an image uploaded to POST account / update_profile_banner is unable to be processed.");
			break;
			case 429:
			$error = __("Error 429: Too Many Requests - Returned in API v1.1 when a request cannot be served due to the application’s rate limit having been exhausted for the resource.");
			break;
			case 500:
			$error = __("Error 500: Internal Server Error - Something is broken. This is a Twitter error. Please, check Twitter Status.");
			break;
			case 502:
			$error = __("Error 502: Bad Gateway - Twitter is down or being upgraded.");
			break;
			case 503:
			$error = __("Error 503: Service Unavailable - The Twitter servers are up, but overloaded with requests. Try again later.");
			break;
			case 504:
			$error = __("Error 504: Gateway timeout - The Twitter servers are up, but the request couldn’t be serviced due to some failure within our stack. Try again later.");
			break;
			case 32:
			$error = __("Error: Could not authenticate you - Your call could not be completed as dialed.");
			break;
			case 34:
			$error = __("Error (404): Sorry, that page does not exist - The specified resource was not found.");
			break;
			case 68:
			$error = __("The Twitter REST API v1 is no longer active. Please update.");
			break;
			case 88:
			$error = __("Error: Rate limit exceeded - The request limit for this resource has been reached for the current rate limit window.");
			break;
			case 89:
			$error = __("Error: Invalid or expired token - The access token used in the request is incorrect or has expired. Used in API v1.1");
			break;
			case 64:
			$error = __("Error (403): Your account is suspended and is not permitted to access this feature");
			break;
			case 131:
			$error = __("Error (500): Internal error - Corresponds with an HTTP 500 - An unknown internal error occurred.");
			break;
			case 135:
			$error = __("Error (401): Could not authenticate you - Corresponds with a HTTP 401 - it means that your oauth_timestamp is either ahead or behind our acceptable range");
			break;
			case 187:
			$error = __("Error: Status is a duplicate - The status text has been Tweeted already by the authenticated account.");
			break;
			case 215:
			$error = __("Error (400): Bad authentication data - Typically sent with 1.1 responses with HTTP code 400. The method requires authentication but it was not presented or was wholly invalid.)");
			break;
			default:
			$error = __("Unknown error! ):");
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
			$this->error = __("Error: the application doesn't have enough permissions. Remember that both access tokens must have permissions to read and write.");
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
	$lel = $zerdb->select( $zerdb->twitter );
	return $lel->execute();
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
	return ($o && $o->nums > 0);
}
/**
* Obtiene un tweet desde su ID
*
* @param $id str | int
*
**/

function obt_tweet( $id ) {
	global $zerdb;
	$u = $zerdb->select($zerdb->tweets, "*", array("id" => $zerdb->real_escape($id) ) )->_();
	return $u && $u->nums > 0 ? $u : false;
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
	$u = $zerdb->select($zerdb->tweets)->_();
	return $u && $u->nums > 0 ? $u : false;
}
/**
* Comprueba si existe un tweet
*
* @param $id str | int
*
**/
function existe_tweet( $id ) {
	return obt_tuit($id) !== false ? true : false;
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
	$id = $zerdb->real_escape($id);
	$eliminar = $zerdb->eliminar( $zerdb->tweets, array("id" => $id ) );
	return $eliminar;
}