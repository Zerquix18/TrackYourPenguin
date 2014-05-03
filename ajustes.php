<?php
/**
*
* Archivo de ajustes del sitio
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.1
*
**/

require_once( dirname(__FILE__) . '/typ-load.php' );

comprobar( false );

if( ! es_super_admin() )
	typ_die( __("Haciendo trampa, ¿eh?") );

construir( 'cabecera', __('Ajustes'), true );

$post = ( "POST" == $_SERVER['REQUEST_METHOD'] );
$c = new extraer($zerdb->config, "*");
?>
<h3><?php _e("Ajustes") ?></h3>
<?php

if( $post ) {
	$nombre = @$zerdb->proteger( $_POST['nombre'] );
	$url = isset($_POST['url']) ? @$_POST['url'] : url( false );
	$robots = isset($_POST['robots']) ? '1' : '0';
	$url = preg_replace('/[\/]$/', '', $url);
	$url = preg_replace('/^[https]/', $url);
	if( !preg_match('/^[http]/',  $url) )
		$url = 'http://' . $url;

	if( ! comprobar_args( @$_POST['nombre'], @$_POST['url'] ) ) {
		agregar_error( __("Haciendo trampa, ¿eh?"), true, true);
	}elseif( vacios($_POST['url'], $_POST['nombre'] ) ) {
		agregar_error( __("No puedes dejar campos vacíos"), true, true);
	}elseif( strlen($nombre) > 20 || strlen($url) > 100 ) {
		agregar_error( __("No puedes sobrepasar los caracteres"));
	}elseif( ! filter_var($url, FILTER_VALIDATE_URL) ) {
		agregar_error( __("El URL no parece ser válido"), true, true);
	}else{
		$zerdb->actualizar( $zerdb->config, 
				array("titulo" => $nombre, "url" => $url, "robots" => $robots )
			);
		agregar_info( __("La configuración ha sido actualizada") );
		echo redireccion( url( true ) , 2);
	}
}
?>
<form method="POST" class="form-horizontal" action="<?php echo url( true ) ?>">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Nombre") ?>
		</label>
		<div class="controls">
			<input type="text" name="nombre" value="<?php echo ($post) ? @$_POST['nombre'] : $c->titulo ?>" maxlength="20" required="required" id="nombre">
			<span class="help-block"><?php _e("Título del sitio :)") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("URL") ?>
		</label>
		<div class="controls">
			<input type="url" name="url" value="<?php echo ($post) ? @$_POST['url'] : $c->url ?>" id="url" required="required"
			 maxlength="100">
			<span class="help-block"><?php _e("URL del sitio, no es necesario cambiarla") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Buscadores") ?>
		</label>
		<div class="controls">
			<input type="checkbox" name="robots" <?php if( $c->robots == '1' ) echo 'checked="checked"' ?>>
			<span class="help-inline"><?php _e("¿Pueden los buscadores indexar esta administración?") ?></span>
		</div>
	</div>

	<hr><center><input type="submit" name="enviar" value="<?php _e('Actualizar') ?>" id="enviar" class="btn btn-primary"></center>

<?php
construir('pies');