<?php
/**
*
* Funciones de los parámetros
*
* @author Zerquxi18 <http://www.zerquix18.com/>
* @since 0.1
* @package TrackYourPenguin
*
**/

/**
*
* Obtiene el parámetro de un tracker y lo devuelve en integer
*
* @param $posicion str | int
* @param $tracker_id str | int
* @return bool | array
*
**/

function obt_parametros($posicion, $tracker_id) {
	global $zerdb;

	if( ! is_numeric( $posicion ) || ! is_numeric($tracker_id) || (int) $posicion > 3 || (int) $posicion == 0 )
		return false;

	$tracker = $zerdb->proteger($tracker_id);

	$p = new extraer($zerdb->parametros, "*", array("tracker" => $tracker ) );

	if( ! $p || !$p->nums > 0)
		return false;

	$p = new extraer($zerdb->parametros, "*", array("tracker" => $tracker, "posicion" => $posicion) );

	$datos = array(
			"size" => (int) $p->size,
			"x" => (int) $p->x,
			"y" => (int) $p->y,
			"angulo" => (int) $p->angulo
		);
	
	return $datos;

}

/**
*
* Actualiza los parámetros de un tracker
*
* @author Zerquix18
* @since 0.1
*
**/

class actualizar_parametros {

	/**
	* Comprueba si el existe un error
	*
	* @access public
	* @var bool
	* @since 0.1
	*
	**/
	public $comp_error = false;
	/**
	* El error devuelto
	*
	* @access public
	* @var string
	* @since 0.1
	*
	**/
	public $error = '';
	/**
	*
	* La query devuelta, para el debugging
	*
	* @access public
	* @var string
	* @since 0.1
	*
	**/
	public $query = '';
	/**
	*
	* Actualiza los parámetros
	*
	* @param $tracker_id int | str
	* @param $posicion int | str
	* @param $parametros array
	* @return bool
	*
	**/
	public function __construct( $tracker_id, $posicion, $parametros ) {
		global $zerdb;

		if( ! is_numeric( $tracker_id ) ) {
			$this->comp_error = true;
			$this->error = __("El tracker debe ser un ID");
			return false;
		}

		if( ! is_numeric($posicion) ) {
			$this->comp_error = true;
			$this->error = __("La posición debe ser un número del 1 al 3");
			return false;
		}

		if( !is_array($parametros ) ) {
			$this->comp_error = true;
			$this->error = __("Los parámetros deben ir en matriz");
			return false;
		}

		$t = obt_tracker( $tracker_id );

		if( $t && ! $t->nums > 0 ) {
			$this->comp_error = true;
			$this->error = __("El tracker no existe");
			return false;
		}

		$array = array("x", "y", "angulo", "size");

		foreach($array as $a) {
			if( ! comprobar_args_array($parametros) ) {
				$this->comp_error = true;
				$this->error = __("Los parámetros enviados son incorrectos");
				return false;
			}elseif( empty($parametros) ) {
				$this->comp_error = true;
				$this->error = __("No puedes dejar campos vacíos");
				return false;
			}
		}

		$actualizar = $zerdb->actualizar(
				$zerdb->parametros,
				$parametros,
				array("posicion" => $posicion )
			);

		if( ! $actualizar ) {
			$this->comp_error = true;
			$this->error = $zerdb->ult_err;
			$this->query = $zerdb->ult_sol; //debugging
			return false;
		}

		return true;
	}
}