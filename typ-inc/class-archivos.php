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
		if( ! isset($_FILES[$archivo]) || empty($_FILES[ $archivo ]['name'] ) ) {
			$this->comp_error = true;
			$this->error = __("No file was selected.");
			return false;
		}

		$this->nombre = $_FILES[ $archivo ][ 'name' ];
		$this->tipo = $_FILES[ $archivo ][ 'type'];
		$this->size = $_FILES[ $archivo ][ "size" ];
		$this->temporal = $_FILES[ $archivo ][ "tmp_name"];
		$this->_error = $_FILES[ $archivo ]["error"];
		$this->tipos = array("png", "jpeg", "jpg");
		$this->formato = end( explode(".", $this->nombre) );
		if( !( ($this->tipo == "image/png")  || ($this->tipo == "image/jpeg") || ($this->tipo == "image/jpg") ) || 
			! in_array($this->formato, $this->tipos) ) {
			$this->comp_error = true;
			$this->error = __("The filytype is not <strong>png</strong>, <strong>jpg</strong>, or <strong>jpeg</strong>");
		}elseif( ($this->size / 1024 ) / 1024 > 2 ) {
			$this->comp_error = true;
			$this->error = __("The file size can't be greather than 2 <strong>MB</strong>");
		}else{
			$this->nuevo_nombre = md5( uniqid() . $this->nombre );
			$this->ext = '.' . $this->formato;
			$this->archivo = $this->nuevo_nombre . $this->ext;
			$this->path =  IMG . $this->archivo;
			if( ! is_dir('img') && false == ( @mkdir('img') ) ) {
				$this->comp_error = true;
				$this->error = __("The directory 'img' doesn't exist, it couldn't be made... Please, make it.");
			}else
				@chmod('img/', 0777 );

			$this->mover = @move_uploaded_file($this->temporal, $this->path);

			if( $this->mover ) {
			@chmod( $this->path, 0777);
			
			$this->archivo2 = $this->nuevo_nombre . '-bg' . $this->ext;
			if( !@copy($this->path, IMG . $this->archivo2 ) ) {
				$this->error = __('Unable to duplicate the file. Please update the permissions.');
				$this->comp_error = true;
				return false;
			}
			@chmod( IMG . $this->archivo2, 0777 );
			}else{
				switch( $this->_error ) {
					case "1":
					$this->comp_error = true;
					$this->error = __("The uploaded file exceeds the upload_max_filesize directive in php.ini.");
					break;
					case "2":
					$this->comp_error = true;
					$this->error = __("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
					break;
					case "3":
					$this->comp_error = true;
					$this->error = __("The uploaded file was only partially uploaded.");
					break;
					case "4":
					$this->comp_error = true;
					$this->error = __("No file was uploaded.");
					break;
					case "6":
					$this->comp_error = true;
					$this->error = __("Missing a temporary folder.");
					break;
					case "7":
					$this->comp_error = true;
					$this->error = __("Failed to write file to disk.");
					break;
					case "8":
					$this->comp_error = true;
					$this->error = __("A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with <strong>phpinfo()</strong> may help.");
					break;
				}
			}
		}
	}
}
class eliminar {

	public $comp_error = false;
	public $error = null;

	function __construct( $archivo ) {
		if( ! file_exists($archivo) ) {
			$this->comp_error = true;
			$this->error = __("The file doesn't exist.");
			return false;
		}
		chmod( $archivo, 0777); #por si las moscas, eh.
		if( !@unlink( $archivo ) ) {
			$this->comp_error = false;
			$this->error = __("Unable to delete the file. Probably the permissions.");
		}
		return true;
	}
}
function es_jpeg( $archivo ) {
	return 2 == exif_imagetype( PATH . IMG . $archivo );
}
function es_png($archivo) {
	return 3 == exif_imagetype( PATH . IMG . $archivo );
}
function getf( $archivo ) {
	return strtolower( end( explode('.', $archivo) ) );
}