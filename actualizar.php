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

comprobar( false );

if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) ) 
	typ_die( __("Necesito un ID correcto") );

$t = obt_tracker( $_GET['id'] );

if( false == $t )
	typ_die( __("El tracker que especificas no existe") );

construir('cabecera', sprintf( __("Actualizar el tracker de: %s"), ucwords($t->personaje) ), false );
$post = "POST" == getenv('REQUEST_METHOD');
// seguridad...
if( ! file_exists( IMG . $t->img ) ) {
	agregar_error( __("La imagen final no existe en el directorio de imágenes") );
	exit( construir('pies') );
}elseif( ! file_exists( IMG . $t->imgbg ) ) {
	agregar_error( __("La imagen por la que se crea el tracker no existe en el directorio de imágenes") );
	exit( construir('pies') );
}elseif( ! file_exists( INC . $t->fuente ) ) {
	agregar_error( __("La fuente no existe") );
	exit( construir('pies') );
}
function arreglar_imagen( $img, $imgbg, $ext ) {
	global $zerdb;
	$img_ = $img;
	$imgbg_ = $imgbg;
	if( 'png' == $ext ) {
		$imgbg = preg_replace("/(png)$/", "jpg", $imgbg);
		$img = preg_replace("/(png)$/", "jpg", $img);
	}else{
		$imgbg = preg_replace("/(jpeg|jpg)$/",  "png", $imgbg);
		$img = preg_replace("/(jpeg|jpg)$/",  "png", $img);
	}
	$zerdb->update( $zerdb->trackers, array('img' => $img, 'imgbg' => $imgbg) )->where('id', $GLOBALS['t']->id )->_();
	$z = rename( PATH .  IMG .  $img_,  PATH . IMG . $img ) && rename( PATH . IMG . $imgbg_, PATH . IMG . $imgbg );
	if( !$z )
		typ_die( 
			sprintf( 
				__('Lo lamento. Tu archivo no es una imagen de formato <strong>%1$s</strong>, por lo que he de cambiar el formato a <strong>%$2s</strong> para que sí funcione. Por favor cambia el formato de %3$s y de %4$s a %2$s en el directorio de imágenes (img/). :)'),
				end( explode('.', $img_ ) ),
				$ext,
				$img_,
				$imgbg_
		 ) );
	else
		typ_die(
				sprintf(
						__('Tus imágenes no tenían el formato correcto. He decidido cambiarlos, por favor recarga la página e intenta de nuevo')
					)
			);
}
function actualizar_imagen( $tracker_id, $datos ) {
	global $zerdb;
	$t = obt_tracker( $tracker_id );
	$fuente = INC . $t->fuente;
	$status = obt_parametros( 1, $tracker_id);
	$server = obt_parametros( 2, $tracker_id );
	$room = obt_parametros( 3, $tracker_id );
	$estado = $datos['estado'];
	$servidor = $datos['servidor'];
	$sala = $datos['sala'];
	if( es_png( $t->imgbg ) && 'png' == getf( $t->imgbg ) )
		$im = @imagecreatefrompng( IMG . $t->imgbg );
	elseif( es_jpeg( $t->imgbg ) and 'jpeg' == getf( $t->imgbg ) || 'jpg' ==getf( $t->imgbg ) )
		$im = @imagecreatefromjpeg( IMG . $t->imgbg );
	elseif( es_png( $t->imgbg ) && 'jpeg'  == getf(  $t->imgbg ) || 'jpg' == getf( $t->imgbg ) )
		arreglar_imagen( $t->img, $t->imgbg, '.png' ); // el binario dice que es .jpeg, no png , then, cambiamos... 
	elseif( es_jpeg( $t->imgbg ) && 'png' == getf( $t->imgbg ) )
		arreglar_imagen( $t->img, $t->imgbg, '.jpg');
 	else
		return 0; // .l.

	$color = imagecolorallocate($im, 255, 255, 255);
	imagettftext($im, (int) $status['size'], (int) $status['angulo'], (int) $status['x'], (int) $status['y'], $color, $fuente, $estado );
	imagettftext($im, (int) $server['size'], (int) $server['angulo'], (int) $server['x'], (int) $server['y'], $color, $fuente, $servidor );
	imagettftext($im, (int) $room['size'], (int) $room['angulo'], (int) $room['x'], (int) $room['y'], $color, $fuente, $sala );
	if( es_png($t->img) )
		imagepng($im, IMG . $t->img );
	else
		imagejpeg($im, IMG . $t->img );

	imagedestroy($im);
	return true;
}
?>
<h2><?php echo sprintf( __("Actualizar el tracker de %s"), ucwords($t->personaje) ) ?></h1>
<ul class="pager">
	<li class="next"><a href="<?php echo url() ?>"><?php _e('Volver') ?>&rarr;</a></li>
</ul>
<hr>
<div class="page-header">
	<h2><?php _e('Actualizar') ?>&nbsp;<small><?php _e('Actualiza el tracker') ?></small></h2>
</div>
<hr>
<?php
	if( $post ) {
		$args = ! comprobar_args( @$_POST['estado'], @$_POST['servidor'], @$_POST['sala'] );
		$vacios = vacios( @$_POST['estado'], @$_POST['servidor'], @$_POST['sala'] );
		if( $args ) {
			typ_die( __("Haciendo trampa, ¿eh?") );
		}elseif( $vacios ) {
			agregar_error( __("No puedes dejar datos vacíos") );
		}else{
			$datos = array(
					"estado" => trim($_POST['estado']),
					"servidor" => trim($_POST['servidor']),
					"sala" => trim($_POST['sala']),
				);
			actualizar_imagen($t->id, $datos);
			$_SESSION['ult_act_' . $t->id ] = time();
			$log = array(
				"usuario" => $_SESSION['usuario'],
				"tracker" => $t->id,
				);
			$log = array_merge( $datos, $log );
			if( isset($_POST['tweet']) || isset($_POST['tweetp']) ) {
				$tweet__ = trim($_POST['tweetp']);
				if( isset($_POST['tweetp']) && ! empty($tweet__) && is_string($_POST['tweetp']) )
					$tuit = $_POST['tweetp'];
				elseif( isset($_POST['tweet']) && is_numeric($_POST['tweet']) )
					$tuit = obt_tuit( (int) $_POST['tweet'] );
				else
					$tuit = 0;
				if( $tuit )
					$log['tweet'] = reemplazar_tweet( $zerdb->real_escape($tuit), $datos );
			}
			$log = json_encode( $log );
			agregar_log( $log, time() );
			agregar_info( __("El tracker ha sido actualizado..."), true, true);
		}
	}
?>
<form method="post" action="<?php echo url() . sprintf('actualizar.php?id=%s', $_GET['id']) ?>" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Estado")?>
		</label>
		<div class="controls">
			<input type="text" name="estado" <?php if( $post ) _f( @$_POST['estado']) ?> required="required">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Servidor") ?>
		</label>
		<div class="controls">
			<input type="text" name="servidor" <?php if( $post ) _f( @$_POST['servidor']) ?> required="required">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Sala") ?>
		</label>
		<div class="controls">
			<input type="text" name="sala" <?php if( $post ) _f( @$_POST['sala']) ?> required="required">
		</div>
	</div>
<?php if( isset($_SESSION['ult_act_' . $t->id]) ) : ?>
	<strong><?php _e("Última actualización") ?></strong>: <?php echo mostrar_fecha($_SESSION['ult_act_' . $t->id]) ?>
<?php endif ?>
<hr>
<?php
if(  oauth_configurado() ) : ?>
<div class="page-header">
	<h2><?php _e("Tuitear") ?>&nbsp;<small><?php _e("Envía lo que actualices a Twitter") ?></small></h2>
</div>
<?php
	$tuits = obt_tweets();
	$o = obt_oauth();
	if( $post ) {
		if( true == ( isset($_POST['tweet']) && is_numeric( $_POST['tweet']) ) ||
			 true == ( isset($_POST['tweetp']) && is_string($_POST['tweetp']) && ! vacio($_POST['tweetp']) ) ) {
			$twitter = new zer_twitter($o->consumer_key, $o->consumer_secret, $o->access_token, $o->access_token_secret);
			if( isset($_POST['tweetp']) && ! vacio($_POST['tweetp']) )
				$tuit = $_POST['tweetp'];
			else
				$tuit = obt_tuit( $_POST['tweet'] );

			$tw = $twitter->tuitear( reemplazar_tweet( stripslashes($tuit), $datos ) );
			if( $tw )
				agregar_info( sprintf( __('El tweet ha sido enviado. <strong><a href="%s" target="_blank">Ver</a></strong>'), $twitter->url ), true, true );
			else
				agregar_error( $twitter->error );
		}else{
			agregar_error( __("No has seleccionado/escrito ningún tweet"), false, false );
		}
	}
	if( false == $tuits && ! $post  )
		_e('No tienes tuits. Aun así, puedes enviar un tweet personalizado.');
	?>
	<hr><textarea name="tweetp" class="redo input-xxlarge" cols="2" placeholder="<?php _e('Envía tu tweet personalizado...') ?>"></textarea><hr>
	<?php
	while($tuits !== false && $tw_ = $tuits->r->fetch_array() ) {
		?>
	<label class="radio">
		<input type="radio" name="tweet" value="<?php echo $tw_['id'] ?>"><?php echo ucwords($tw_['nombre']) ?>
	</label>
		<?php
		echo "\n";
	}
?>
<?php elseif( ! oauth_configurado() && es_admin() ):
	agregar_error( sprintf( __("No has configurado el <a href=\"%s\">OAuth de Twitter</a>."), url() . 'oauth.php'), false, true);
	endif;
?>
<center><input type="submit" class="btn btn-primary btn-large" value="<?php _e("Actualizar tracker") ?>"></center>
</form>

<!-- ... -->


<div id="ver" class="modal hide fade" role="dialog" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><u><?php _e("Ver imagen") ?></u></h3>
  </div>
  <div class="modal-body">
    <p align="center"><a href="<?php echo $img = url() . IMG . $t->img ?>"><img src="<?php echo $img ?>"></a></p>
  </div>
</div>
<?php if( 'localhost' !== $_SERVER['HTTP_HOST'] ) : ?>
<div id="herramientas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php _e("Compartir imagen") ?></h3>
  </div>
  <div class="modal-body">
	<p><?php _e("Comparte la imagen en tu sitio web copiando el siguiente HTML") ?></p><br>
	<div class="well redo">
		<code>&lt;img src="<?php echo url() . IMG . $t->img ?>" title="<?php echo $t->img ?>"&gt;</code>
  </div>
  <div class="modal-footer">
    <button class="btn btn-inverse" data-dismiss="modal" aria-hidden="true"><?php _e("Cerrar") ?></button>
  </div>
</div>
<?php endif; if( oauth_configurado() ) modal_tweets(); construir('pies') ?>