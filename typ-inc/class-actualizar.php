<?php

class actualizacion {

	private $archivo;

	private $host = 'https://github.com/Zerquix18/TrackYourPenguin/archive/';

	// Le cambia el modo a 777 a todos los archivos excepto a los .ht(.*)
	private function cambiar_modo() {
		$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( "./" ) ); // todos los dirs y subsdirs..
		foreach($iterator as $item)
			if( ! preg_match('/^\/[\.]/', $item) ) // evita archivos empezando por un punto (.)
    			@chmod($item, 0777); // cambia el modo recursivamente a todos los ficheros nuevos.
	}
	/** Thanks you!! : http://php.net/manual/en/function.rmdir.php#98622 **/ 
	private function rrmdir( $dir, $except = array() ) {
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != ".." && ! in_array($object, $except) ) { 
					if( filetype($dir."/".$object) == "dir" )
						$this->rrmdir($dir."/".$object);
					else 
						unlink($dir."/".$object); 
				}
			} 
			reset($objects); 
		rmdir($dir); 
  		} 
	}
	public function __construct() {
		global $zerdb;
		// El único requisito 100% necesario.
		if( ! class_exists("ZipArchive") ) {
			echo agregar_error( __("The class <strong>ZipArchive</strong> doesn't exist to unzip the files. It can't update. :("), false, true );
			return false;
		}

		$v = obt_version();
		$this->archivo = $v . '.zip';
		// it starts...

		echo __("Creating temporary file...<br>"); // mensaje de aviso.
		if( false == touch( $this->archivo ) ) {
			echo agregar_error( __("Unable to create the temporary file. Probably the permissions."), false, true);
			return false;
		}
		chmod( $this->archivo, 0777 );
		$this->d_url = $this->host . $this->archivo;
		echo sprintf( __("Downloading files from <strong>%s</strong><br><br>"), $this->d_url );
		if( false === (file_put_contents($this->archivo, file_get_contents($this->d_url ) ) ) ) {
			if( ! function_exists('curl_init') ) {
				echo agregar_error( __("Unable to download the update file. The files don't have permissions and/or the cURL extension is not installed.") );
				return false;
			}
			$this->f = fopen("./".$this->archivo, "w" ); // w = write = escribir
			$ch = curl_init( $this->host . $this->archivo );
			curl_setopt($ch, CURLOPT_FILE, $this->f ); // donde guardará el archivo.
			dormir( 1 );
			$this->resultado = curl_exec($ch); // descarga y reemplaza (Ejecuta)
			// cierra lo que queda abierto...
			fclose( $this->f );
			curl_close( $ch );
		}
		// descomprime...
		$this->zip = new ZipArchive();
		if( true === ( $this->zip->open( $this->archivo ) ) && $this->zip->numFiles !== 0) { // abre zip si resulta true
			############################################ OLÉ!
			echo __("Decompressing the TrackYourPenguin update file... <br>");
			dormir(1);
			chmod( $this->archivo, 0777 ) or die("error");
			if( false == ($this->zip->extractTo("./") ) ) {
				echo agregar_error( __("Unable to decompress the TrackYourPenguin update file :(") );
				unlink($this->archivo) or die("fuck");
				return false;
			}
			// intenta cambiar el modo de los archivos en esa raíz...
			$this->cambiar_modo();
			// cierra zip...
			$this->zip->close();
			echo __("Deleting temporary file...<br><br>");
			// borra el zip descargado...
			@unlink( $this->archivo );
			// actualiza los archivos:
			echo __("Updating TrackYourPenguin...<br>");
			$this->src = "TrackYourPenguin-{$v}/";
			$this->dir_ = array_slice( scandir( $this->src ), 2);
			$ftd = array("README.md", "typ-config-sample.php");
			$except = array();
			$qe = $zerdb->select("trackers", "fuente")->_();
			// para no eliminar fuentes añadidas (it won't delete added fonts)
			while( $res = $qe->r->fetch_array() )
				if( $res['fuente'] !== 'typ.ttf' )
					$except[] = $res['fuente'];
			foreach($ftd as $a => $b)
				unlink($this->src . $b);
			foreach($this->dir_ as $a => $b)
				if( ! in_array($b, $ftd) ):
					if( is_dir($this->src . $b) )
						$this->rrmdir( "./" . $b, $except );
					rename($this->src . $b, "./" . $b);
				endif;
			rmdir($this->src);
			if( file_exists( PATH . INC . 'actualizado.php' ) ) {
				require_once( PATH . INC . 'actualizado.php');
				@unlink( PATH . INC . 'actualizado.php'); //ya no es necesario.
			}
			//yup ! finally 
			ob_end_clean(); // get the fuck up
			agregar_info(
					sprintf(
							__("Welcome to TrackYourPenguin <strong>%s</strong> :)"),
							$v
						)
				);
			echo redireccion( url() . 'about.php', 3); // GUALÁAAAAAAA!!!!!!!!1 SOY EL PUTO AMOOOOOOOOo!!!!!!!!!!!D ASLDKALÑJ
		}else{
			echo agregar_error( sprintf( __("It couldn't open the file: %s :("), $this->archivo ) ); // ay :(
			unlink( $this->archivo );
			return false;
		}
	}
}

/**
* Obtiene la versión 
*
**/

function obt_version( $array = false ) {
	$url = "https://raw.githubusercontent.com/Zerquix18/TrackYourPenguin/master/typ-load.php";
	$content = file_get_contents($url);
	$regex = '/define\(\"VERSION\"\, \"(.*)?\"/';
	if( preg_match($regex, $content, $matches) )
		return $matches[1];
	return false;

}
/**
*
* Comprueba si se necesita actualizar
*
*
**/
function hay_actualizacion() {
	if( defined("NO_ACTUALIZACIONES") && TRUE == constant("NO_ACTUALIZACIONES") || false == ( $v = obt_version( false ) ) )
		return false;

	return version_compare($v, constant('VERSION'), '>');
}
/**
* Lanza el mensaje de aviso si se necesita actualizar
*
**/
function actualizaciones() {
	if( hay_actualizacion() )
		return agregar_error(
				sprintf(
						__('TrackYourPenguin <strong>%s</strong> is available, please <a href="%s">update.</a> :)'),
						$v,
						url() . 'actualizaciones.php'
					), true, false
			);
	return;
}