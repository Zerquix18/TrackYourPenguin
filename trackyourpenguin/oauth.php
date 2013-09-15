<?php
/**
*
* Archivo de configuración del OAuth de Twitter
*
* Agrega y actualiza los datos de la aplicación de Twitter
*
* @package TrackYourPenguin
* @author Zerquix18 <http://zerquxi18.com/>
* @since 0.0.1
*
**/

require_once( dirname(__FILE__) . '/typ-load.php');

if( ! es_admin() )
	typ_die("Haciendo trampa, ¿eh?");

comprobar( false );

construir('cabecera', 'Configurar OAuth', true);

$o = obt_oauth();

$datos = array(
		"consumer_key" => (isset($o->consumer_key)) ? $o->consumer_key : "",
		"consumer_secret" => (isset($o->consumer_secret ) ) ? $o->consumer_secret : "",
		"access_token" => (isset($o->access_token) ) ? $o->access_token : "",
		"access_token_secret" => (isset($o->access_token_secret)) ? $o->access_token_secret : ""
	);

?>
<h3>Configurar OAuth de Twitter</h3>
<?php
	if( "POST" == $_SERVER['REQUEST_METHOD']) {
		$consumer_key = @$zerdb->proteger( $_POST['consumer_key'] );
		$consumer_secret = @$zerdb->proteger( $_POST['consumer_secret'] );
		$access_token = @$zerdb->proteger( $_POST['access_token'] );
		$access_token_secret = @$zerdb->proteger( $_POST['access_token_secret'] );

		$args = ! comprobar_args( @$_POST['consumer_key'], @$_POST['consumer_secret'], @$_POST['access_token'], @$_POST['access_token_secret']);
		$args2 = vacios(@$_POST['consumer_key'], @$_POST['consumer_secret'], @$_POST['access_token'], @$_POST['access_token_secret']);

		if( $args ) {
			agregar_error("Haciendo trampa, ¿eh?");
		}elseif( $args2 ) {
			agregar_error("No puedes dejar datos vacíos");
		}else{
			if( ! oauth_configurado() )
				insertar_oauth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
			else
				actualizar_oauth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

			agregar_info("Datos de conexión a Twitter actualizados");
			echo redireccion( url( true ), true);
		}
	}
?>
<form method="POST" action="<?php echo url( true ) ?>" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			Consumer Key
		</label>
		<div class="controls">
			<input type="text" name="consumer_key" value="<?php echo $datos['consumer_key'] ?>" required="required">
			<span class="help-block"><small><code>consumer_key</code> de tu aplicación de Twitter</small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Consumer Secret
		</label>
		<div class="controls">
			<input type="text" name="consumer_secret" value="<?php echo $datos['consumer_secret'] ?>" required="required">
			<span class="help-block"><small><code>consumer_secret</code> de tu aplicación de Twitter</small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Access Token
		</label>
		<div class="controls">
			<input type="text" name="access_token" value="<?php echo $datos['access_token'] ?>" required="required">
			<span class="help-block"><small><code>access_token</code> de tu aplicación de Twitter (Read and write)</small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Access Token Secret
		</label>
		<div class="controls">
			<input type="text" name="access_token_secret" value="<?php echo $datos['access_token_secret'] ?>" required="required">
			<span class="help-block"><small><code>access_token_secret</code> de tu aplicación de Twitter (Read and write)</small></span>
		</div>
	</div>
	<center><input type="submit" class="text-center btn btn-primary" value="Actualizar" name="enviar" id="enviar"></center>
</form>
<?php
construir('pies');