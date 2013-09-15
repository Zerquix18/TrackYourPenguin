<?php
/**
*
* Finalmente, el que actualiza los trackers...!
*
* @author Zerquix18 :')
* @package TrackYourPenguin
* @since 0.1
*
**/

require_once( dirname(__FILE__) . '/typ-load.php' );

if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) ) 
	typ_die("Necesito un ID correcto");

if( ! es_actualizador() )
	typ_die("No existen permisos suficientes");

$t = obt_tracker( $zerdb->proteger($_GET['id'] ) );

if( !$t || ! $t->nums > 0)
	typ_die("El tracker que especificas no existe");


$p = array(
	"estado" => obt_parametros(1, $t->id),
	"servidor" => obt_parametros(2, $t->id),
	"sala" => obt_parametros(3, $t->id)
	);

construir('cabecera', sprintf("Actualizar el tracker %s", ucwords($t->personaje) ), false );
$post = ( "POST" == $_SERVER['REQUEST_METHOD'] );

// seguridad...
if( !file_exists( IMG . $t->img ) ) {
	agregar_error("La imagen final no existe en el directorio de imágenes");
	exit( construir('pies') );
}elseif( ! file_exists( IMG . $t->imgbg ) ) {
	agregar_error("La imagen por la que se crea el tracker no existe en el directorio de imágenes");
	exit( construir('pies') );
}elseif( ! file_exists( INC . $t->fuente ) ) {
	agregar_error("La fuente no existe");
	exit( construir('pies') );
}
function actualizar_imagen( $tracker_id, $datos ) {
	global $zerdb;
	$t = obt_tracker( $tracker_id );
	$im = imagecreatefrompng( IMG . $t->imgbg );
	$color = imagecolorallocate($im, 255, 255, 255);
	$fuente = INC . $t->fuente;
	$status = obt_parametros( 1, $tracker_id);
	$server = obt_parametros( 2, $tracker_id );
	$room = obt_parametros( 3, $tracker_id );
	$estado = $datos['estado'];
	$servidor = $datos['servidor'];
	$sala = $datos['sala'];
	imagettftext($im, (int) $status['size'], (int) $status['angulo'], (int) $status['x'], (int) $status['y'], $color, $fuente, $estado );
	imagettftext($im, (int) $server['size'], (int) $server['angulo'], (int) $server['x'], (int) $server['y'], $color, $fuente, $servidor );
	imagettftext($im, (int) $room['size'], (int) $room['angulo'], (int) $room['x'], (int) $room['y'], $color, $fuente, $sala );
	imagepng($im, IMG . $t->img );
	imagedestroy($im);
	return true;
}
?>
<h2>Actualizar el Tracker de <?php echo ucwords( $t->personaje ) ?></h1>
<ul class="pager">
	<li class="next"><a href="<?php echo url() ?>">Volver &rarr;</a></li>
</ul>
<hr>
<div class="page-header">
	<h2>Actualizar&nbsp;<small>Actualiza el tracker</small></h2>
</div>
<hr>
<?php
	if( $post ) {
		$args = ! comprobar_args( @$_POST['estado'], @$_POST['servidor'], @$_POST['sala'] );
		$vacios = vacios( @$_POST['estado'], @$_POST['servidor'], @$_POST['sala'] );

		if( $args ) {
			agregar_error("Haciendo trampa, ¿eh?");
		}elseif( $vacios ) {
			agregar_error("No puedes dejar datos vacíos");
		}else{
			$datos = array(
					"estado" => $_POST['estado'],
					"servidor" => $_POST['servidor'],
					"sala" => $_POST['sala']
				);

			actualizar_imagen($t->id, $datos);
			$_SESSION['ult_act_' . $t->id] = retornar_fecha();
			$log = ucfirst($_SESSION['usuario']) . ' actualizó el tracker de ' . ucwords($t->personaje) . ' a ';
			$log .= "Estado: " . $datos['estado'];
			$log .= " Servidor:" . $datos['servidor'];
			$log .= " Sala:" . $datos['sala'];
			if( isset($_POST['tweet']) && is_numeric($_POST['tweet']) && existe_tweet( $_POST['tweet'] ) )
				$log .= " y eligió el tweet de " . $tweet->nombre;

			agregar_log( $log ) or die($zerdb->ult_err);
			agregar_info("El tracker ha sido actualizado...", true, true);
		}
	}
?>
<form method="post" action="<?php echo url( true ) ?>" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			Estado:
		</label>
		<div class="controls">
			<input type="text" name="estado" <?php if( $post ) echo 'value="' . @$_POST['estado'] . '"' ?> required="required">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Servidor:
		</label>
		<div class="controls">
			<input type="text" name="servidor" <?php if( $post ) echo 'value="' . @$_POST['servidor'] . '"' ?> required="required">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Sala:
		</label>
		<div class="controls">
			<input type="text" name="sala" <?php if( $post ) echo 'value="' . @$_POST['sala'] . '"' ?> required="required">
		</div>
	</div>
<?php if(isset($_SESSION['ult_act_' . $t->id]) ) : ?>
	<b>Última actualización</b>: <?php echo $_SESSION['ult_act_' . $t->id] ?>
<?php endif ?>
<hr>
<?php
if(  oauth_configurado() && tiene_tweets() ) : ?>
<div class="page-header">
	<h2>Tuitear&nbsp;<small>Envía lo que actualices a Twitter</small></h2>
</div>
<hr>
<?php
	$tuits = obt_tweets();
	$q = mysql_query( $tuits->query );
	$o = obt_oauth();

	if( $post ) {
		if( isset($_POST['tweet']) && is_numeric($_POST['tweet']) ) {
			$twitter = new zer_twitter($o->consumer_key, $o->consumer_secret, $o->access_token, $o->access_token_secret);
			$tweet_id = $zerdb->proteger( $_POST['tweet'] );
			$tweet = obt_tuit( $tweet_id );
			$tuit = array(
					"%es" => $datos['estado'],
					"%se" => $datos['servidor'],
					"%sa" => $datos['sala'],
					"%cp" => "#ClubPenguin",
					"%r" => rand( 1, 100 ),
				);
			$tweet = str_replace( array_keys( $tuit ) , $tuit , $tweet );
			$tw = $twitter->tuitear( $tweet );
			if( $tw )
				agregar_info(sprintf('El tweet ha sido enviado. <b><a href="%s">Ver</a></b>', $twitter->url ). true, true );
			else
				agregar_error( $twitter->error );
		}else{
			agregar_error("No has seleccionado ningún tweet");
		}
	}
?>
<?php
	while($tw = mysql_fetch_array($q) ) {
		?>
	<label class="radio">
		<input type="radio" name="tweet" value="<?php echo $tw['id'] ?>"><?php echo ucwords($tw['nombre']) ?>
	</label>
		<?php
		echo "\n";
	}
?>
<?php elseif( ! oauth_configurado() && es_admin() ):
	agregar_error( sprintf("No has configurado el <a href=\"%s\">OAuth de Twitter</a>.", url() . 'oauth.php'), false, true);
	elseif( ! tiene_tweets() && es_admin() ) :
	agregar_error( sprintf("No has configurado <a href=\"%s\">los tweets</a>.", url() . 'tweets.php'), false, true);
	endif;

?>
<hr />
<center><input type="submit" class="btn btn-primary btn-large" value="Actualizar tracker"></center>
</form>

<div id="ver" class="modal hide fade" role="dialog" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><u>Ver imagen</u></h3>
  </div>
  <div class="modal-body">
    <p align="center"><img src="<?php echo url() . IMG . $t->img ?>"></p>
  </div>
</div>


<div id="herramientas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Compartir imagen</h3>
  </div>
  <div class="modal-body">
	<p>Comparte la imagen en tu sitio web copiando el siguiente HTML</p><br>
	<div class="well redo">
		<code>&lt;img src="<?php echo url() . IMG . $t->img ?>"&gt;</code>
  </div>
  <div class="modal-footer">
    <button class="btn btn-inverse" data-dismiss="modal" aria-hidden="true">Cerrar</button>
  </div>
</div>
<?php

construir('pies');