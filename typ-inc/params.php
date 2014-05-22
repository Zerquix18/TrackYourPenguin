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

	$p = $zerdb->select($zerdb->parametros, "*", array("tracker" => $tracker_id, "posicion" => $posicion) )->_();

	return array(
			"size" => $p->size,
			"x" => $p->x,
			"y" => $p->y,
			"angulo" => $p->angulo
		);

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
		$array = array("x", "y", "angulo", "size");
		foreach($array as $a) {
			if( ! comprobar_args($parametros[$a]) ) {
				$this->comp_error = true;
				$this->error = __("Los parámetros enviados son incorrectos");
				return false;
			}elseif( vacio($parametros[$a]) ) {
				$this->comp_error = true;
				$this->error = __("No puedes dejar campos vacíos");
				return false;
			}
		}
		$actualizar = $zerdb->update(
				$zerdb->parametros,
				$parametros)-> where( array('tracker' => $tracker_id, 'posicion' => $posicion) )->_();

		if( ! $actualizar ) {
			$this->comp_error = true;
			$this->error = $zerdb->error;
			return false;
		}
		return true;
	}
}