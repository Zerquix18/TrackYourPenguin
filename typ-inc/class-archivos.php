<?php
/**
*
* Clase de archivos, ésta sube y manipula archivos al ser llamada
*
* @author Zerquix18
* @since 0.1.0
* @package TrackYourPenguin
* @subpackage TYP Archivos
*
**/

class archivos {
	
	public function __construct() {
		return true;
	}

	public function subir( $archivo ) {
		return new subir( $archivo );
	}

	public function eliminar( $archivo ) {
		return new eliminar( $archivo );
	}
}

class subir {

	public $comp_error = false;
	public $error = null;
	public $archivo = '';

	function __construct( $archivo ) {
		if( empty($_FILES[ $archivo ]['name'] ) ) {
			$this->comp_error = true;
			$this->error = __("No se ha seleccionado ningún archivo");
			return false;
		}

		$this->nombre = $_FILES[ $archivo ][ 'name' ];
		$this->tipo = $_FILES[ $archivo ][ 'type'];
		$this->size = $_FILES[ $archivo ][ "size" ];
		$this->temporal = $_FILES[ $archivo ][ "tmp_name"];
		$this->_error = $_FILES[ $archivo ]["error"];
		$this->tipos = array("png");
		$this->formato = end( explode(".", $this->nombre) );
		if( !( ($this->tipo == "image/png") ) || ! in_array($this->formato, $this->tipos) ) {
			$this->comp_error = true;
			$this->error = __("El tipo de archivo no es <b>png</b>, <b>jpg</b>, o <b>jpeg</b>");
		}else{
			$this->nuevo_nombre = md5( uniqid() . $this->nombre );
			$this->ext = '.' . $this->formato;
			$this->archivo = $this->nuevo_nombre . $this->ext;
			$this->path = 'img/' . $this->archivo;
			$this->mover = move_uploaded_file($this->temporal, $this->path);
			if( $this->mover ) :
			chmod( $this->path, 0777);
			else:
				switch( $this->_error ) {
					case "1":
					$this->comp_error = true;
					$this->error = __("El archivo excede el límite de 'upload_max_filesize' del php.ini");
					break;
					case "2":
					$this->comp_error = true;
					$this->error = __("El archivo excede el límite MAX_FILE_SIZE especificado en el formulario HTML");
					break;
					case "3":
					$this->comp_error = true;
					$this->error = __("El archivo subido fue solamente parcialmente cargado");
					break;
					case "4":
					$this->comp_error = true;
					$this->error = __("No hay archivo a subir");
					break;
					case "6":
					$this->comp_error = true;
					$this->error = __("No existe carpeta de archivos temporal");
					break;
					case "7":
					$this->comp_error = true;
					$this->error = __("Error al escribir el archivo en el disco");
					break;
					case "8":
					$this->comp_error = true;
					$this->error = __("Una extensión de PHP ha impedido que el archivo subiera. PHP no tiene una manera de saber cuál 
					extensión fue; puedes examinar la lista con <b>phpinfo()</b>");
					break;
				}
			endif;
		}
	}
}

class eliminar {

	public $comp_error = false;
	public $error = null;

	function __construct( $archivo ) {
		if( ! file_exists($archivo) ) {
			$this->comp_error = true;
			$this->error = __("El archivo no existe");
			return false;
		}
		chmod( $archivo, 0777); #por si las moscas, eh.
		if( !unlink( $archivo ) ) {
			$this->comp_error = false;
			$this->error = __("No se pudo eliminar el archivo, probablemente falta de permisos.");
		}else{
			unlink( $archivo );
		}
	}
}