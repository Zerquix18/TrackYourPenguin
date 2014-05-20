<?php
/**
*
*
* @author Zerquix18
* @package TrackYourPenguin
* @link http://trackyourpenguin.com/
*
**/

require_once( dirname(__FILE__) . '/typ-load.php' );

construir( 'cabecera', __('Acerca de'), false );
?>
<h2><?php _e('Acerca de TrackYourPenguin') ?></h2>
<hr>
<?php
echo sprintf(
		__('Bienvenido a <strong>TrackYourPenguin %s</strong>. TrackYourPenguin (<i>abreviado como TYP</i>) es un sistema de trackers en PHP y MySQL desarrollado actualmente por <a href="%s" target"_blank">Zerquix18</a>. El objetivo es que puedas tracker fácilmente con múltiples trackers, tuits, usuarios y otras cualidades sin la necesidad de tocar código constantemente o poner varios códigos para varios trackers. Todo en uno. :)'), constant('VERSION'), 'http://zerquix18.com/'
	);
echo sprintf( __('<h3>Novedades de la versión %s</h3><hr><ul><li>Cierre de sesión automático si alguien la cierra desde otro lado.</li><li>Cambios de tema desde la <a href="%s">configuración</a>.</li><li>Ahora los parámetros los coge el formulario a penas cliqueas.</li><li>Actualización en la tabla de <a href="%s">usuarios</a>.</li><li>Esta página. :)</li></ul>'), constant("VERSION"), url() . 'ajustes.php', url() . 'usuarios.php');
echo sprintf( __('<h3>Créditos</h3><ul><li><strong>Bootstrap</strong>. <i>Todo el diseño y plugins javascript del sitio</i>.</li><li><strong>Bootswatch</strong>. <i>Temas adicionales de bootstrap</i>.</li><li><li><strong>Zerquix18</strong>. <i>Todo lo demás que ves aquí. :)</i></li></ul>') );
?>
<?php construir('pies'); 