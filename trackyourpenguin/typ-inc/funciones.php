<?php
/**
* Carga algunas funciones varias muy necesarias en el sistema
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.0.1
*
**/

/**
*
* Da seguridad a los argumentos
*
* Para que no nos trolleen desde afuera.
*
* @since 0.0.1
*
*
**/
function comprobar_args() {

	$args = func_get_args();

	if( ! array_key_exists(0, $args) )
		return false;

		foreach($args as $a) {
			if( ! isset($a) || !is_string($a) ) //tiene que ser string lo que entre, o nada :'3
				$false = true;
		}

	if( isset($false) )
		return false;

	return true;
}
/**
*
* Da seguridad a los argumentos que se encuentren dentro de un array
* @since 0.0.1
*
**/
function comprobar_args_array( $array ) {

	if( ! is_array($array) || empty($array) )
		return false;

	foreach($array as $c => $d) {
		if( ! isset($c) || ! isset($d) || !is_string($c) || !is_string($d) )
			$false = true;
	}

	if( isset($false) )
		return false;

	return true;
}
/**
*
* Comprueba que un string no esté vacío
*
* @since 0.0.1
*
**/
function vacio( $string ) {
	if( ! is_string($string) )
		return false;

	$string = preg_replace("/\s/", "", $string);

	return empty($string);
}
/**
*
* Comprueba que varios argumentos no estén vacíos
*
* @since 0.0.1
*
**/
function vacios() {

	$args = func_get_args();

	if( ! array_key_exists(0, $args) )
		return false;

	foreach($args as $a) {
		if( vacio($a) )
			return true;
	}

	return false;
}
/**
* Comprueba los argumentos vacíos de un array
*
* @since 0.0.1
*
**/
function vacios_array( $array ) {
	if( ! is_array($array) || ! comprobar_args_array( $array ) || empty($array) )
		return false;

	foreach($array as $a => $b) {
		if( vacio($b) )
			return true;
	}

	return false;
}
/**
*
* Devuelve el título del sitio
*
* @since 0.0.1
*
**/
function titulo() {
	global $zerdb;
	if( ! comprobar_instalacion() )
		return 0;

	$t = new extraer($zerdb->config, "titulo");
	return $t->titulo;
}
/**
*
* Devuelve el título del sitio
*
* @since 0.0.1
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
  <link href="<?php echo url() . INC . CSS . 'bootstrap2.css' ?>" rel="stylesheet">
  <style type="text/css">
    body { padding-top: 60px; 
      padding-bottom: 40px; 
    }
  </style>
  <link href="<?php echo url() . INC . CSS . 'bootstrap2.min.css' ?>" rel="stylesheet">
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
* @since 0.0.1
*
**/
function typ_die( $error = null ) {
	return exit( typ_die_helper( $error ) );
}
/**
*
* Comprueba si se inició sesión
*
**/
function comprobar( $inverso = false) {
	if( !$inverso && ! sesion_iniciada() )
		exit( header("Location: " . url() . 'acceso.php?continuar=' . urlencode( url(true) ) ) );
	elseif( $inverso && sesion_iniciada() )
		exit( header("Location: " . url() ) );
}
/**
*
* Comprueba si se instaló
*
* @since 0.0.1
* @return bool
*
**/
function comprobar_instalacion() {
	global $zerdb;
	if( ! $zerdb->listo )
		return false;

	$query = @new extraer($zerdb->usuarios, "*");
	$foo = ( isset($query) && @$query->nums > 0) ? true : false;
	return $foo;
}
/**
*
* Comprueba que se haya iniciado sesión
*
* @since 0.0.1
* @return bool
*
**/
function sesion_iniciada() {
	global $sesiones;
	if(! comprobar_instalacion() )
		return header("Location: instalar.php");

	if( $sesiones->sesion_iniciada() )
		return true;

	return false;
}
/**
*
* Envía un email
*
* @since 0.0.1
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
* @since 0.0.1
* @param $actual bool
*
**/
function url($actual = false) {
	global $zerdb;
	if(! comprobar_instalacion() )
		return false;

	$c = new extraer($zerdb->config, "*");
	$q = ( !empty($_SERVER['QUERY_STRING'] ) ) ? '?' . $_SERVER['QUERY_STRING'] : '';

	if( $actual )
		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $q;
	else
		return $c->url . '/';
}
/**
*
* Lanza un mensaje de error
*
* @since 0.0.1
* @param $error string
* @param $cerrar bool
* @param $mensaje bool
*
**/
function agregar_error( $error = null, $cerrar = false, $mensaje = true) {
	if( is_null($error) )
		return false;

	$close = ($cerrar == true) ? '<a class="close" data-dismiss="alert" href="#">&times;</a>' : '';
	$msg = ($mensaje == true) ? '<i class="icon-remove"></i> <b>Error:</b> ' : '';
  echo '<div class="alert alert-error">' . $close . ' ' . $msg . $error . '</div>';
}
/**
*
* Lanza un mensaje de info
*
* @since 0.0.1
* @param $error string
* @param $cerrar bool
* @param $mensaje bool
*
**/
function agregar_info( $info = null, $cerrar = false, $mensaje = true ) {
	if( is_null($info) )
		return false;
	
	$close = ($cerrar == true) ? '<a class="close" data-dismiss="alert" href="#">&times;</a>' : '';
	$msg = ($mensaje == true) ? '<i class="icon-ok"></i> <b>Info:</b> ' : '';
  echo '<div class="alert alert-info">'. $close  . $info . '</div>';
}
/**
*
* Redirecciona vía HTML
*
* @since 0.0.1
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
* Devuelve la fecha actual del servidor
*
* @since 0.0.1
* @param $hoy_es bool
*
**/
function retornar_fecha($hoy_es = false) {
	$dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
	$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
		'Octubre', 'Noviembre', 'Diciembre');
	$dia_de_la_semana = $dias[date('w')];
	$dia_del_mes = date('d');
	$mes = $meses[ date('n') - 1 ];
	$anio = date('Y');
	$hoy = (! $hoy_es ) ? '' : 'Hoy es ';

	return $hoy . $dia_de_la_semana . ' ' . $dia_del_mes . ' de ' . $mes . ' del ' . $anio;
}
/**
*
* Hace que el HTML no surja efecto
*
* @since 0.0.1
* @param $html string
*
**/
function reemplazar_html( $html = null ) {
	if( is_null($html) )
		return;

	$reemplazar = array(
			"<" => "&gt;",
			">" => "&lt;"
		);

	return str_replace( array_keys( $reemplazar), $reemplazar, $html);
}
/**
*
* Comprueba que el usuario esté en debido archivo
*
* @since 0.0.1
* @param $donde string
* @param $server string
*
**/
function es( $donde, $server = "PHP_SELF" ) {
	if( preg_match("#" . $donde . "#", $_SERVER[ strtoupper($server) ] ) )
		return true;

	return false;
}

/**
* Ayuda a la función de las actualizaciones
*
**/

function comprobar_actualizacion( $medio ) {
	$url = 'http://trackyourpenguin.com/v.txt';
	switch($medio) {
		case "curl":
		$ch = curl_init('http://trackyourpenguin.com/v.txt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		$v2 = constant("VERSION");
		$v1 = curl_exec( $ch );
		curl_close( $ch );
		break;
		default:
		$v1 = @file_get_contents( $url );
		if( ! $v1 )
			return false;
		
		$v2 = constant("VERSION");
	}

	$v_actual = explode('.', $v2);
	$v_nueva = explode('.', $v1);
	if( count($v_actual) !== 3 || count($v_nueva) !== 3) 
		return false;

	preg_match_all("!\d+!", $v_nueva[0], $match);
	$v_nueva[0] = $match[0][0];
	$comparar = $v_actual[0] == $v_nueva[0] && $v_actual[1] == $v_nueva[1] && $v_actual[2] == $v_nueva[2];

	if( $v_actual[0] !== $v_nueva[0] || $v_actual[1] !== $v_nueva[1] || $v_actual[2] !== $v_nueva[2] )
	return 	agregar_error(
				sprintf(
						'TrackYourPenguin %s ya está disponible, por favor <a target="_blank" href="%s">actualiza.</a> :)',
						$v1,
						'//trackyourpenguin.com/actualizar/'
					), true, false
			);

}

/**
*
* Comprueba si se necesita actualizar
*
*
**/

function actualizaciones() {
	if( function_exists('curl_init') )
		comprobar_actualizacion('curl');
	else
		comprobar_actualizacion( 'ola k ase ??');
}