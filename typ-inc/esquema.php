<?php
/**
* Archivo del SQL para la base de datos
*
* Contiene el SQL que crea las tablas.
*
* @author Zerquix18 <http://www.zerquix18.com/>
* @since 0.1
* @package TrackYourPenguin
*
**/

$preg = sprintf("#%s#", basename(__FILE__) );
if( preg_match($preg, $_SERVER['PHP_SELF'])) exit(0);

$sql = array();

/* Tabla de usuarios */

$sql['usuarios'] = "CREATE TABLE IF NOT EXISTS $zerdb->usuarios (
		id int(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		usuario varchar(12),
		clave char(32),
		email varchar(60),
		estado char(1),
		rango char(1),
		hash char(32)
	)";

/* Tabla de sesiones */

$sql['sesiones'] = "CREATE TABLE IF NOT EXISTS $zerdb->sesiones (
		id int(4),
		hash varchar(32),
		ip varchar(40),
		fecha int(32)
	)";

/*Tabla de trackers*/

$sql['trackers'] = "CREATE TABLE IF NOT EXISTS $zerdb->trackers (
		id int(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		personaje varchar(20),
		img varchar(40),
		imgbg varchar(40),
		fuente varchar(100)
	)";

/*Tabla de configuración*/

$sql['config'] = "CREATE TABLE IF NOT EXISTS $zerdb->config (
		titulo varchar(20),
		url varchar(100),
		extra text
	)";

/*Tabla del OAuth de Twitter */

$sql['twitter'] = "CREATE TABLE IF NOT EXISTS $zerdb->twitter (
		consumer_key varchar(40),
		consumer_secret varchar(50),
		access_token varchar(100),
		access_token_secret varchar(100)
	)";

/*Tabla del log*/

$sql['log'] = "CREATE TABLE IF NOT EXISTS $zerdb->log ( 		
		id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		accion varchar(300),
		fecha int(32)
	)";

/*Tabla de tuits*/

$sql['tweets'] = "CREATE TABLE IF NOT EXISTS $zerdb->tweets (
		id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		nombre varchar(10), 
		tweet varchar(140)
		)";

/*Tabla de parámetros*/

$sql['parametros'] = "CREATE TABLE IF NOT EXISTS $zerdb->parametros ( 
		tracker int(4),
		posicion int(1),
		size int(11),
		angulo int(11),
		x int(11),
		y int(11)
	)";