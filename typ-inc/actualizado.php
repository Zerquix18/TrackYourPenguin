<?php
/**
* Archivo de actualización de TYP 0.1.1
*
**/
$archivo = 'typ-config.php';
if( ! file_exists($archivo) )
	exit( sprintf('El archivo <b>%s</b> no existe, duh?', $archivo ) );
if( defined("TYP_LANG") )
	exit( "defined" );
$contenido = file_get_contents($archivo) or die(var_dump($contenido));
$f = fopen($archivo, 'w') or die("._.");
$ult = "/* Listo ! */";
$contenido = str_replace($ult, "", $contenido);
$add = "/* Agrega el lenguaje para el sistema */\n\ndefine(\"TYP_LANG\", \"es_ES\");\n\n$ult";
$contenido = $contenido . $add;
$actualizar = fwrite($f, $contenido);
fclose($f);