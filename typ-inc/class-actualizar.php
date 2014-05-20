<?php

class actualizacion {

	public $archivo = 'actualizar.zip';

	private $host = 'http://trackyourpenguin.com/d/';

	public $dir = './';

	public function cambiar_modo() {
		$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir ) ); // todos los dirs y subsdirs..
		foreach($iterator as $item)
			if( ! preg_match('/[\/\.htaccess]$/', $item) || ! preg_match("/[\/\.htpasswd]$/", $item) && ! preg_match('/\/\./', $item) && ! preg_match('/\/\./', $item)  ) // quita los /.. y /. a EXEPCIÓN de /.htaccess y /.htpasswd
    			@chmod($item, 0777); // cambia el modo recursivamente a todos los ficheros nuevos.
	}
	public function __construct() {
		// algunos bugs...
		if( ! class_exists("ZipArchive") ) {
			echo agregar_error( __("No existe la clase <b>ZipArchive</b> para la descompresión de los archivos. No se puede actualizar :("), false, true );
			return false;
		}
		if( ! function_exists("curl_init") || ! function_exists("curl_close" ) ) {
			echo agregar_error( __("No existe <b>curl_init</b> para la descarga de los archivos. No se puede actualizar. :("), false, true);
			return false;
		}

		// it starts...

		/**
		*
		* Esta función sirve para cambiar la última fecha de modificación de un archivo.
		* touch, significa tocar, por eso es así.
		* En cambio lo uso porque si el archivo no existe, lo crea, y esto es lo útil.
		* Crea el archivo, que será reemplazado por el ya descargado.
		*
		*
		**/
		echo __("Creando archivo temporal...<br>"); // mensaje de aviso.
		if( false == ($arch = @touch( $this->archivo ) ) ) {
			echo agregar_error( __("No se puede crear el archivo temporal. Probablemente los permisos."), false, true);
			return false;
		}
		dormir( 1 );
		/**
		*
		* Intenta cambiarle el modo al archivo, desactiva el debugging automático con la @
		* Así veo que no haya problemas al abrir y/o editar el archivo
		*
		**/
		@chmod( $this->dir . $this->archivo, 0777 );
		/**
		*
		* Abre el archivo que ya fue creado, para su edición...
		*
		**/
		$this->f = fopen( $this->dir . $this->archivo, "w" ); // w = write = escribir

		/**
		*
		* Hace la solicitud para descargar el archivo con cURL.
		* 
		**/
		$ch = curl_init( $this->host . $this->archivo );

		// algunos parámetros para enviarle al archivo vía POST

		$params = array(
				"tipo" => urlencode("actualizacion") // el tipo de descarga es por actualización y no directa.
			);

		curl_setopt($ch, CURLOPT_POST, true); //cURL vía POST y no GET.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt($ch, CURLOPT_FILE, $this->f ); // donde guardará el archivo.
		echo sprintf( __("Descargando archivos desde <b>%s</b><br><br>"), $this->host . $this->archivo );
		dormir( 1 );
		$this->resultado = curl_exec($ch); // descarga y reemplaza (Ejecuta)

		// cierra lo que queda abierto...
		fclose( $this->f );
		curl_close( $ch );

		// descomprime...

		$this->zip = new ZipArchive();

		if( false !== ( $this->zip->open( $this->archivo ) ) ) { // abre zip si no resulta false.
			// borra los archivos que no pueden descomprimirse, en caso de existencia o de mala descarga.
			@$this->zip->deleteName("typ-config-sample.php");
			@$this->zip->deleteName("img/");
			@$this->zip->DeleteName("typ-config.php");
			############################################ OLÉ!
			echo __("Descomprimiendo <b>zip</b> ya descargado... <br>");
			dormir(1);
			if( false == ($this->zip->extractTo( $this->dir ) ) ) {
				echo agregar_error( __("No se pudo descomprimir el paquete descargado :(") );
				@unlink($this->archivo);
				return false;
			}
			// intenta cambiar el modo de los archivos en esa raíz...
			$this->cambiar_modo();
			// cierra zip...
			$this->zip->close();
			echo __("Borrando archivo temporal...<br><br>");
			dormir(1);
			// borra el zip descargado...
			@unlink( $this->archivo );
			echo __("Actualizando TrackYourPenguin...<br>");
			dormir(2); // a dormir :3
			if( file_exists( PATH . INC . 'actualizado.php' ) ) {
			require_once( PATH . INC . 'actualizado.php');
			@unlink( PATH . INC . 'actualizado.php'); //ya no es necesario.
			}
			//yup !
			ob_end_clean(); // get the fuck up
			agregar_info(
					sprintf(
							__("Bienvenido a TrackYourPenguin <b>%s</b> :)"),
							obt_version()
						)
				);
			echo redireccion( url( true ), 3); // GUALÁAAAAAAA!!!!!!!!1 SOY EL PUTO AMOOOOOOOOo!!!!!!!!!!!D ASLDKALÑJ
		}else{
			echo agregar_error( sprintf( __("No se puede abrir el archivo: %s"), $this->archivo ) ); // ay :(
			return false;
		}
	}
}

/**
* Obtiene la versión 
*
**/

function obt_version( $array = false ) {
	if( function_exists('curl_init') ) {
		$h = 'http://trackyourpenguin.com/v.txt';
		$ch = curl_init($h);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); //máximo 2 mins para responder la solicitud.
		$_v = curl_exec( $ch );
		if( ! $_v )
			return 0;
		curl_close($ch);
	}elseif( false !== ($_v = @file_get_contents($h) ) ) {
		$_v = $_v;
	}
	if( ! isset($_v) )
		return false; // no obtuvimos nada... ?

	if( false == ( $v = @explode('.', $_v ) ) )
		return false;

	preg_match_all("!\d+!", $v[0], $match);

	$v[0] = @$match[0][0];

	if( ! count($v) >= 2 )
		return false;

	if( $array ) {
		$v[2] = array_key_exists(2, $v) ? $v[2] : null;
		return array($v[0], $v[1], $v[2]);
	}else{
		if( ! array_key_exists(0, $v) || ! array_key_exists(1, $v) )
			return false;

		$v[2] = array_key_exists(2, $v) ? '.' . $v[2] : '';
		return sprintf("%d.%s%s", $v[0], $v[1], $v[2]);
	}
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

	return $v !== constant("VERSION");
}
/**
* Lanza el mensaje de aviso si se necesita actualizar
*
**/
function actualizaciones() {
	if( false == ( $v = obt_version( false ) ) )
		return false;

	$comparar = $v == constant("VERSION");

	if( ! $comparar && ! es('actualizaciones.php') )
		return agregar_error(
				sprintf(
						__('TrackYourPenguin <b>%s</b> ya está disponible, por favor <a href="%s">actualiza.</a> :)'),
						$v,
						url() . 'actualizaciones.php'
					), true, false
			);
}