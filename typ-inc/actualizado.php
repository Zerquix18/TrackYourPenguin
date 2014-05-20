<?php
/**
* Archivo de actualización para las versiones de TYP
*
**/

/** version 0.1.1 **/ 
function typ_0_1_1() {
$archivo = 'typ-config.php';
if( false == file_exists($archivo) )
	return false;
if( false !== defined("TYP_LANG") )
	return false;
if( false == ($contenido = @file_get_contents($archivo) ) )
	return false;
if( false == ($f = @fopen($archivo, 'w') ) )
	return false;
$ult = "/* Listo ! */"; // ult. linea.
$contenido = str_replace($ult, "", $contenido); //la quita.
$add = "/* Agrega el lenguaje para el sistema */\n\ndefine(\"TYP_LANG\", \"es_ES\");\n\n$ult"; // acualiza.
$contenido = $contenido . $add; // pone lo anterior, mas, lo nuevo.
$actualizar = fwrite($f, $contenido); // reescribe eso.
fclose($f); //cierra.
return true;
}

typ_0_1_1();