<?php
/**
*
* Estas funciones ayudan con la geolocalización, dirección IP, zona horaria y fechas
* @author Zerquix18
* @since 0.2
* @package TrackYourPenguin
*
**/
//-----
/**
*
* Obtiene la dirección IP real del usuario, sin importar que esté bajo proxy a excepción de TOR Browser
*
* @return string
*
**/
function obt_ip() {
	if( ! empty($_SERVER['HTTP_CLIENT_IP']) )
		return $_SERVER['HTTP_CLIENT_IP'];
	if( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']) )
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	return $_SERVER['REMOTE_ADDR'];
}
/**
*
* Obtiene la hora actual, si $time no es null y es integer obtendrá la hora de esos segundos. 
* Si $segundos es true, pondrá los segundos también
*
* @param $time int
* @param $segundos bool
* @return string
**/
function obt_hora($time = '', $segundos = false) {
	if( empty($time) )
		$time = time();
	$tiempo = getdate($time);
	$hora = $tiempo['hours'];
	$minutos = strlen($tiempo['minutes']) < 2 ? '0' . $tiempo['minutes'] : $tiempo['minutes'];
	$segundos = ($segundos) ? ':' . $tiempo['seconds'] : '';
	return $hora . ':' . $minutos . $segundos;
}
/**
*
* Devuelve un array con el país y la ciudad del usuario actual según su IP
*
* @return string
*
**/
function obt_geo() {
	$ip = obt_ip();
	$ch = curl_init( sprintf("http://www.geoplugin.net/json.gp?ip=%s", $ip) );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$pais = curl_exec($ch);
	if( false == $pais )
		return false;
	curl_close($ch);
	$resultado = json_decode($pais);
	if( (int) $resultado->geoplugin_status !== 200 )
		return false;
	return array(
			"pais" => $resultado->geoplugin_countryName,
			"ciudad" => $resultado->geoplugin_city
		);
}
/**
*
* Obtiene la ciudad desde la función anterior
*
**/
function obt_ciudad() {
	if( false == ($geo = obt_geo() ) )
		return 'Desconocida';
	return $geo['ciudad'];
}
/**
*
* Obtiene la fecha actual, si $time no es null entonces obtendrá la fecha de esos segundos.
* Si $hoy_es se define como true, entonces lanzará un 'Hoy es' antes de la fecha.
*
* @return string
*
**/
function obt_fecha($time = '', $hoy_es = false) {
	if( empty($time) || is_null($time) )
		$time = time();
        $dias = array(
        	__('Domingo'),
        	__('Lunes'),
        	__('Martes'),
        	__('Miércoles'),
        	__('Jueves'),
        	__('Viernes'),
        	__('Sábado')
        );
        $meses = array(
        	__('Enero'),
        	__('Febrero'),
        	__('Marzo'),
        	__('Abril'),
        	__('Mayo'),
        	__('Junio'),
        	__('Julio'),
        	__('Agosto'),
        	__('Septiembre'),
            __('Octubre'),
            __('Noviembre'),
            __('Diciembre')
         );

        $dia_de_la_semana = $dias[ date('w', $time) ];
        $dia_del_mes = date('d', $time);
        $mes = $meses[ date('n', $time) - 1 ];
        $anio = date('Y', $time);
        $hoy = (false == $hoy_es ) ? '' : __('Hoy es ');
        if( 'en_US' == obt_lenguaje() )
        	return $hoy . $dia_de_la_semana . ', ' . $mes . ' ' . $dia_del_mes . ' of ' . $anio;
        return $hoy . $dia_de_la_semana . ' ' . $dia_del_mes . __(' de ') . $mes . __(' del ') . $anio;
}
/**
*
* Obtiene la zona horaria viendo si obt_ciudad() se incluye en el array de timezone_identifiers_list()
*
* @return string
*
**/
function obt_zonahoraria_automatica() {
	if( 'Desconocida' == ($ciudad = obt_ciudad()) )
		return "America/Santo_Domingo";
	$needle = str_replace(" ", "_", $ciudad);
	$haystack = timezone_identifiers_list();
		$america = sprintf("America/%s", $needle);
		$europa = sprintf("Europe/%s", $needle);
		if( in_array($america, $haystack) )
			return $america;
		elseif( in_array($europa, $haystack) )
			return $europa;
		else
			return "America/Santo_Domingo";
}
/**
*
* Obtiene la zona horaria actual si se definió la constante ZONA_HORARIA. De lo contrario, la busca automática
*
**/
function obt_zona_horaria() {
	global $zerdb;
	if( defined("ZONA_HORARIA") && in_array( constant("ZONA_HORARIA"), timezone_identifiers_list() ) )
		return constant("ZONA_HORARIA");
	return obt_zonahoraria_automatica();
}
// establece la zona horaria.
date_default_timezone_set( obt_zona_horaria() );