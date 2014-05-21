<?php
/**
*
* Archivo de los tweets
*
* Borra y edita los tweets
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.1
*
**/

require_once( dirname( __FILE__ ) . '/typ-load.php' );

comprobar( false );

if( ! es_admin() )
	typ_die(__("Haciendo trampa, ¿eh?") );

$accion = isset($_GET['accion']) && !empty($_GET['accion']) && is_string($_GET['accion']) ? $_GET['accion'] : '';

$post = 'POST' == getenv('REQUEST_METHOD');

if( ! oauth_configurado() ) {
	construir('cabecera');
	agregar_error( sprintf(
		__("Antes de configurar los tweets, debes configurar el <a href=\"%s\">OAuth</a> de conexión a Twitter"),
		url() . 'oauth.php'
		) );
	 exit( construir('pies') );
}

switch( $accion ) {
	case "agregar":

	construir( 'cabecera', __('Agregar tweet'), true );
	if( $post ) {
		$nombre = trim( $zerdb->real_escape(  @$_POST['nombre']) );
		$texto = trim( $zerdb->real_escape( @$_POST['texto']) );
		if( ! comprobar_args( @$_POST['nombre'], @$_POST['texto'] ) ) {
			typ_die( __("Haciendo trampa, ¿eh?") );
		}elseif( vacio($nombre) || vacio($texto) ) {
			agregar_error( __("No puedes dejar campos vacíos") );
		}elseif( strlen($nombre) > 10 || strlen($texto) > 140 ) {
			agregar_error( __("No puedes sobrepasar los límites") );
		}else{
			$zerdb->insert( $zerdb->tweets, array($nombre, $texto) );
			agregar_info( __("Tweet agregado") );
			echo redireccion(url() . 'tweets.php', 2);
		}
	}
?>
<h3> <?php _e("Agregar tweet") ?> </h3><a href="<?php echo url() . 'tweets.php?accion=agregar' ?>" class="btn btn-link pull-right">
<?php _e("Volver a los tweets") ?> &rarr;</a><hr>
<form action="<?php echo url() . 'tweets.php?accion=agregar' ?>" method="POST" class="form-horizontal">
		<div class="control-group">
		<label class="control-label">
			<?php _e("Nombre") ?>
		</label>
		<div class="controls">
			<input type="text" name="nombre" <?php if($post) echo 'value="' . @$_POST['nombre'] . '"' ?> required="required"
			maxlength="10">
			<span class="help-block"><?php _e("Nombre del tweet a ser elegido") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Texto") ?>
		</label>
		<div class="controls">
			<textarea name="texto" class="redo" rows="5" maxlength="140" required="required"><?php if($post) echo @$_POST['texto'] ?></textarea>
			<span class="help-block"><?php _e('El texto que se publicará en el tweet') ?></span>
		</div>
	</div>
	<center><input type="submit" value="<?php _e('Enviar') ?>" class="btn btn-primary" required="required"></center>
</form>
<?php
break;

case "editar":
case "actualizar":


if( ! isset($_GET['id']) || ! is_numeric($_GET['id']) )
	typ_die( __("Debes especificar un ID válido") );
$t = obt_tweet( $_GET['id'] );
if( false == $t )
	typ_die( __("Este tweet no existe") );

construir( 'cabecera', sprintf( __("Editar el tweet: %s"), $t->nombre ), true );

?>
<h3><?php _e("Editar el tweet") ?>: <i><?php echo ucwords( $t->nombre ) ?></i></h3>
<a href="<?php echo url() . 'tweets.php' ?>" class="btn btn-link pull-right"><?php _e("Volver a los tweets") ?> &rarr;</a>
<hr>
<?php
	if( $post ) {
		$nombre = trim( $zerdb->real_escape( @$_POST['nombre'] ) );
		$tweet = trim( $zerdb->real_escape( @$_POST['texto'] ) );

		if( ! comprobar_args(@$_POST['nombre'], @$_POST['texto'] ) ) {
			typ_die( __("Haciendo trampa, ¿eh?") );
		}elseif( vacios($_POST['nombre'], $_POST['texto']) ) {
			agregar_error( __("No puedes dejar campos vacíos") );
		}elseif( strlen($nombre) > 10 || strlen($tweet) > 140 ) {
			agregar_error( __("No puedes sobrepasar los límites") );
		}else{
			$zerdb->update($zerdb->tweets, array(
					"nombre" => $nombre,
					"tweet" => $texto,
				) )->where("id", $t->id )->_();
			agregar_info( __("Actualizado :)") );
		}
	}
?>
<form action="<?php echo url() . 'tweets.php?accion=editar&id=' . $_GET['id'] ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Nombre") ?>
		</label>
		<div class="controls">
			<input type="text" name="nombre" value="<?php if($post) echo @$_POST['nombre']; else echo $t->nombre ?>" required="required" maxlength="10" id="nombre">
			<span class="help-block"><small><?php _e("El nombre del tweet a editar") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Texto") ?>
		</label>
		<div class="controls">
			<textarea name="texto" class="redo" rows="5" maxlength="140" required="required"><?php
			if( $post )
				echo @$_POST['texto'];
			else
				echo $t->tweet;
			?></textarea>
		</div>
	</div>
	<center><input type="submit" name="actualizar" value="<?php _e("Actualizar") ?>" id="actualizar" required="required" class="btn btn-primary"></center>
</form>
<?php
break;
case "eliminar":

if( ! isset($_GET['ids'] ) || ! is_string($_GET['ids']) || ! preg_match('/^[\d]+$/', @$_GET['id']) ) /* Je, je, je, fail (?) */
	typ_die( __("Mala forma de borrar, mala.") );

$zerdb->delete( $zerdb->twitter ) -> where("id", $_GET['id'] )->_();

construir('cabecera', __('Eliminar tweets') );

agregar_info( __("Han sido eliminados los tweets seleccionados.") );

echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>"), url() . 'index.php');
break;
default:

$id = isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) ? true : false;

	switch($id) {
		case true:
		
		$t = obt_tweet( $_GET['id'] );

		if( ! $t || ! $t->nums > 0)
			typ_die( __("Este tweet no existe") );

		construir( 'cabecera', sprintf( __('Tweet de %s'), $t->nombre), true );
		?>
		<h3><?php _e("Tweet de:") ?><i><?php echo ucwords( $t->nombre ) ?></i></h3>
		<a href="<?php echo url() . 'tweets.php' ?>" class="btn btn-link pull-right"><?php _e("Volver a los tweets") ?> &rarr;</a>
		<hr>
		<?php
		if( isset($_POST['tuitear']) ) {
			$obt = obt_oauth();
			$tw = new zer_twitter($obt->consumer_key, $obt->consumer_secret, $obt->access_token, $obt->access_token_secret);
			$tweet = $tw->tuitear( $t->tweet ) or agregar_error($tw->error, false, false);
			if(!$tw->comp_error)
				agregar_info( __("Tweet enviado exitosamente") );
		}
		?>
		<div class="well"><?php echo $t->tweet ?></div>
		<form action="<?php echo url( true ) ?>" method="POST">
		<button type="submit" name="tuitear" value="true:D" class="btn btn-link">
			<i class="icon-pencil"></i>&nbsp; <?php _e("Tuitear") ?></i>
		</button>
		</form>
		<?php
		break;
		case false:
		construir( 'cabecera', __('Tweets'), true);

		$t = obt_tweets();

		?>
		<h3>Tweets</h3>
		<?php if( false !== $t ): ?>
		<a class="btn btn-link pull-right" href="<?php echo url() ?>tweets.php?accion=agregar"><i class="icon-plus"></i>&nbsp;Agregar nuevo</a><hr>
		<?php
		endif;
		if( false == $t) {
			agregar_error( sprintf( __("No tienes tweets... <a href=\"%s\">agregar uno</a>"), url() . 'tweets.php?accion=agregar' ) );
			construir('pies');
			exit();
		}
		while( $r = $t->r->fetch_array() ) { ?>
			<p class="lead"><u><?php echo $r['nombre'] ?></u></p>
			<div class="well"><?php echo $r['tweet'] ?></div>
			<a href="<?php echo url() . 'tweets.php?id=' . $r['id'] ?>" class="btn btn-link text-center">
				<i class="icon-eye-open"></i>&nbsp;<?php _e("Ver") ?>
			</a> |
			<a href="<?php echo url() . 'tweets.php?accion=editar&id=' . $r['id'] ?>" class="btn btn-link text-center">
				<i class="icon-pencil"></i>&nbsp;<?php _e("Editar") ?>
			</a> |
			<a href="<?php echo url() . 'tweets.php?accion=eliminar&ids='. $r['id'] ?>" class="btn btn-link text-center">
				<i class="icon-trash"></i>&nbsp;<?php _e("Eliminar") ?>
			</a>
			<hr>
		<?php
		}
	}
}
modal_tweets();
construir('pies');