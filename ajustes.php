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
	typ_die( __("Haciendo trampa, ¿eh?") ); // ;)

construir( 'cabecera', __('Ajustes'), true );

$post = 'POST' == getenv('REQUEST_METHOD');
$c = $zerdb->select($zerdb->config, "*")->_();
$t_ = json_decode($c->extra)->tema;
?>
<h3><?php _e("Ajustes") ?></h3>
<?php
if( $post ) {
	$nombre = @$zerdb->real_escape( $_POST['nombre'] );
	$url = isset($_POST['url']) ? @$zerdb->real_escape($_POST['url']) : url( false );
	$url = preg_replace('/[\/]$/', '', $url); // quita el slash final
	$url = preg_replace('/^(https)/', 'http', $url); // reemplaza https por http
	if( ! preg_match('/^(http)/',  $url) ) //si.. no empieza por http... adds it. c:
		$url = 'http://' . $url;
	$temas = array("bootstrap", "cyborg", "slate", "flatly", "cosmo", "cerulean");
	$tema = comprobar_args( @$_POST['tema']) && in_array($_POST['tema'], $temas) ? $_POST['tema'] : 'bootstrap';
	if( ! comprobar_args( @$_POST['nombre'], @$_POST['url'] ) ) {
		typ_die( __("Haciendo trampa, ¿eh?") );
	}elseif( vacios($_POST['url'], $_POST['nombre'] ) ) {
		agregar_error( __("No puedes dejar campos vacíos"), true, true);
	}elseif( strlen($nombre) > 20 ) {
		agregar_error( __("No puedes sobrepasar los caracteres"));
	}elseif( ! filter_var($url, FILTER_VALIDATE_URL) ) {
		agregar_error( __("El URL no parece ser válido"), true, true);
	}else{
		$zerdb->update( $zerdb->config, 
				array("titulo" => $nombre, "url" => $url, "extra" => json_encode( array("tema" => $tema) ) )
			)->_();
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
			<?php _e('Tema') ?>
		</label>
		<div class="controls">
			<select name="tema" onchange="cambiar_tema(this.value)">
				<option <?php if( "bootstrap" == $t_ ) echo 'selected="selected"' ?> value="bootstrap">Bootstrap</option>
				<option <?php if( "cyborg" == $t_ ) echo 'selected="selected"' ?> value="cyborg">Cyborg</option>
				<option <?php if( "slate" == $t_ ) echo 'selected="selected"' ?> value="slate">Slate</option>
				<option <?php if( "flatly" == $t_ ) echo 'selected="selected"' ?> value="flatly">Flatly</option>
				<option <?php if( "cosmo" == $t_ ) echo 'selected="selected"' ?> value="cosmo">Cosmo</option>
				<option <?php if( "cerulean" == $t_ ) echo 'selected="selected"' ?> value="cerulean">Cerulean</option>
			</select>
			<span class="help-block"><?php _e('¿Qué tema te gusta más?') ?></span>
		</div>
	</div>
	<hr><center><input type="submit" name="enviar" value="<?php _e('Actualizar') ?>" id="enviar" class="btn btn-primary"></center>
<?php construir('pies');