<?php
/**
* Clase para la base de datos
*
* @author Zerquix18
* @version 0.1
* @package TrackYourPenguin
* @subpackage ZerDB
* @link http://github.com/Zerquix18/ZerDB
*
**/
class zerdb {
/** todas las tablas... */
	public $tablas = array(
			"usuarios" => array(
					"usuario", "clave", "email", "estado", "rango", "hash"
				),
			"sesiones" => array(
					"id", "hash", "ip", "fecha"
				),
			"trackers" => array(
					"personaje", "img", "imgbg", "fuente"
				),
			"config" => array(
					"titulo", "url", "robots"
				),
			"twitter" => array(
					"consumer_key", "consumer_secret", "access_token", "access_token_secret"
				),
			"log" => array(
					"accion", "fecha"
				),
			"tweets" => array(
					"nombre", "tweet"
				),
			"parametros" => array(
					"tracker", "posicion", "size", "angulo", "x", "y"
				)
		);
	/**
	* @since 0.1
	**/
	private $dbhost;
	/**
	* @since 0.1
	**/
	private $dbusuario;
	/**
	* @since 0.1
	**/
	private $dbclave;
	/**
	* @since 0.1
	**/
	private $dbnombre;
	/**
	* Guarda la última solicitud
	*
	* @since 0.1
	* @access public
	*
	**/
	public $ult_sol = '';
	/**
	* Guarda el último error
	*
	* @since 0.1
	* @access public
	*
	**/
	public $ult_err = '';
	/**
	* Comprueba si la conexión está lista
	* 
	* @since 0.1
	* @access public
	*
	**/
	public $listo = false;
	/**
	* Comprueba si se puede añadir error tracking
	*
	* @since 0.1
	*
	**/
	public $err_track = false;
	/**
	*
	* Construye la clase
	*
	* @access public
	* @since 0.1
	*
	**/
	public $charset = 'utf8';
	public function __construct($dbhost, $dbusuario, $dbclave, $dbnombre) {
		$this->dbhost = $dbhost;
		$this->dbusuario = $dbusuario;
		$this->dbclave = $dbclave;
		$this->dbnombre = $dbnombre;
		if( ! empty($this->tablas ) ) {
			foreach($this->tablas as $a => $b) {
				$this->$a = $a;
			}
		}

		$this->conectar();
		$this->set_charset();
	}

	/**
	* Hace la conexión a la base de datos
	*
	* @access private
	* @since 0.1
	*
	**/
	private	function conectar() {

		if( $this->err_track ) // error tracking
		$this->conexion = mysql_connect($this->dbhost, $this->dbusuario, $this->dbclave);
		else
		$this->conexion = @mysql_connect($this->dbhost, $this->dbusuario, $this->dbclave);

		if( $this->conexion ) {
			$this->seleccionar_db();
		}else{
			$this->ult_err = mysql_error();
		}

	}
	/**
	*
	* Selecciona la base de datos
	*
	* @access public
	* @since 0.1
	*
	**/
	public function seleccionar_db() {

		if( $this->err_track )
		$this->seleccionar_db = mysql_select_db($this->dbnombre, $this->conexion);
		else
		$this->seleccionar_db = @mysql_select_db($this->dbnombre, $this->conexion);


		if( $this->seleccionar_db )
			$this->listo = true;
		else
			$this->ult_err = mysql_error();

		if( $this->listo )
			return true;
	}
	/**
	*
	* Pone el charset el UTF-8
	* @return bool
	* @access private
	* @since 0.1
	*
	**/
	private function set_charset() {
		if( ! $this->listo )
			return false;

		return mysql_set_charset($this->charset, $this->conexion);
	}
	/**
	*
	* Limpia las variables que guardan algunos datos
	*
	**/
	function flushear() {
		$this->ult_err = '';
		$this->ult_sol = '';
		return true;
	}
	/**
	*
	* Hace una solicitud
	*
	* @access public
	* @since 0.1
	* @param $query string
	*
	**/
	function query( $query = null ) {

		if( is_null($query) )
			return false;

		if( $this->err_track )
			$this->resultado = mysql_query($query, $this->conexion);
		else
			$this->resultado = @mysql_query($query, $this->conexion);

		$this->flushear();

		$this->ult_sol = $query;

		if( ! $this->resultado )
			$this->ult_err = mysql_error();

		return $this->resultado;
	}
	/**
	*
	* Crea una tabla
	*
	* @access public
	* @since 0.1
	* @param $nombre string
	* @param $data array
	* @param $add string
	*
	**/
	function crear_tabla( $nombre = null, $data = false, $add = '' ) {

		if( ! is_null($nombre) || ! is_array($data) )
			return false;

		$query = "CREATE TABLE IF NOT EXISTS `$nombre` (\n";

		foreach($data as $a => $b){
			$cols[] = "`$a` $b";
		}

		$query .= implode( ",\n", $cols );

		if( ! empty($add) ) {
			$query .= $add;
		}

		$query .= "\n )";

		return $this->query($query);

	}
	/**
	*
	* Ayuda a las funciones insert o replace
	*
	* @access public
	* @since 0.1
	* @param $tabla string
	* @param $datos array
	* @param $accion string
	* 
	**/
	function _insertar_y_reemplazar($tabla = null, $datos = false, $accion = null) {

		if( !in_array( strtoupper($accion), array("INSERT", "REPLACE") ) or !is_array($datos) ) {
			return FALSE;
		}

		$tablas = $this->tablas[ $tabla ];

		$acc = strtoupper($accion);

		$query = "{$acc} INTO {$tabla} (" . implode(', ', $tablas) . ") VALUES ('" . implode("','", $datos) . 
			"')";

		return $this->query($query);

	}
	/**
	*
	* Inserta una tabla...
	* @param $tabla string
	* @param $datos array
	* @since 0.1
	* @access public
	*
	**/
	function insertar($tabla, $datos) {
		return $this->_insertar_y_reemplazar($tabla, $datos, "INSERT");
	}
	/**
	*
	* Selecciona un dato
	*
	* @since 0.1
	* @param $tabla string
	* @param $dato string
	* @param $donde bool | string
	* @param $extra bool | string
	* @access public
	*
	**/
	function seleccionar( $tabla = null, $dato = "*", $donde = false, $extra = false) {
		return new extraer($tabla, $dato, $donde, $extra);
	}
	/**
	*
	* Reemplaza una tabla...
	* @param $tabla string
	* @param $datos array
	* @since 0.1
	* @access public
	*
	**/
	function reemplazar($tabla, $datos) {
		return $this->_insertar_y_reemplazar($tabla, $datos, "REPLACE");
	}
	/**
	*
	* Actualiza una tabla
	*
	* @param $tabla string
	* @param $datos array
	* @param $donde bool
	* @access public
	* 
	*
	**/
	function actualizar($tabla = null, $datos = false, $donde = false ) {
		if( !is_array($datos) || is_null($tabla) ) 
			return false;

		$tablas = $this->tablas[ $tabla ];

		foreach($datos as $a => $b)
			$set[] = "$a = '$b'";

		$query = "UPDATE {$tabla} SET " . implode(", ", $set);

		if( isset($donde) && is_array($donde) ) {
			foreach($donde as $a => $b)
				$where[] = "$a = '$b'";

			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $this->query($query);


	}
	/**
	*
	* Elimina una tabla
	*
	* @access public
	* @since 0.1
	* @param $tabla string
	* @param $donde array
	*
	**/
	function eliminar($tabla = null, $donde = null) {
		if( is_null($tabla) )
			return false;

		$query = "DELETE FROM {$tabla}";

		if( isset($donde) && is_array($donde) ) {
			foreach($donde as $a => $b)
				$where[] = "$a = '$b'";

			$query .= " WHERE " . implode(" AND ", $where);
		}

		$this->query($query);
	}
	/**
	*
	* Protege la string ante las comillas
	*
	* @param $string string
	*
	**/
	function proteger( $string ) {
		if( ! is_string($string) )
			return false;
		
		if( $this->listo )
			return mysql_real_escape_string( $string );
		else
			return addslashes( $string );
	}
}

class extraer {

	public $query;

	public $obt_error;

	public $error;

	public function __construct($tabla = null, $dato = null, $donde = false, $extra = false) {
		global $zerdb;

		if( is_null($tabla) || is_null($dato) ){
			return;
		}

		if($dato = "todo")
			$dato = "*";

		$query = "SELECT {$dato} FROM {$tabla}";

		if( isset($donde) && is_array($donde) ) {
			foreach($donde as $a => $b) 
					$where[] = "$a = '$b'";
				
			$query.= " WHERE " . implode(" AND ", $where);
		}
		if( isset($extra) ) {
			$query .= " " . $extra;
		}
	if($zerdb->err_track){
		$this->resultado = mysql_query($query);
	}else{
		$this->resultado = @mysql_query($query);
	}

	$this->query = $query;

		if( $this->resultado ) {
			$this->fetch = mysql_fetch_array($this->resultado);
			$this->nums = mysql_num_rows($this->resultado);

			if( $this->nums > 0 ) {
				foreach($this->fetch as $a => $b )
					$this->$a = $b;
			}
			$this->error = false;
		}else{
			$this->error = true;
			$this->obt_error = mysql_error();
		}
	}
}