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
	typ_die(__("Cheatin', uh?!") );

$accion = isset($_GET['accion']) && !empty($_GET['accion']) && is_string($_GET['accion']) ? $_GET['accion'] : '';

$post = 'POST' == getenv('REQUEST_METHOD');

if( ! oauth_configurado() ) {
	construir('cabecera');
	agregar_error( sprintf(
		__("Before setting tweets up, you have to set up the <a href=\"%s\">Twitter OAuth</a>"),
		url() . 'oauth.php'
		) );
	 exit( construir('pies') );
}

switch( $accion ) {
	case "agregar":

	construir( 'cabecera', __('Add tweet'), true );
	if( $post ) {
		$nombre = trim( $zerdb->real_escape(  @$_POST['nombre']) );
		$texto = trim( $zerdb->real_escape( @$_POST['texto']) );
		if( ! comprobar_args( @$_POST['nombre'], @$_POST['texto'] ) ) {
			typ_die( __("Cheatin', uh?!") );
		}elseif( vacio($nombre) || vacio($texto) ) {
			agregar_error( __("You can't leave empty fields.") );
		}elseif( strlen($nombre) > 10 || strlen($texto) > 140 ) {
			agregar_error( __("You can't overpass the limits.") );
		}else{
			$zerdb->insert( $zerdb->tweets, array($nombre, $texto) );
			agregar_info( __("Tweet added") );
			echo redireccion(url() . 'tweets.php', 2);
		}
	}
?>
<h3> <?php _e("Add tweet") ?> </h3><a href="<?php echo url() . 'tweets.php?accion=agregar' ?>" class="btn btn-link pull-right">
<?php _e("Back to tweets") ?> &rarr;</a><hr>
<form action="<?php echo url() . 'tweets.php?accion=agregar' ?>" method="POST" class="form-horizontal">
		<div class="control-group">
		<label class="control-label">
			<?php _e("Name") ?>
		</label>
		<div class="controls">
			<input type="text" name="nombre" <?php if($post) echo 'value="' . @$_POST['nombre'] . '"' ?> required="required"
			maxlength="10">
			<span class="help-block"><?php _e("Tweet's name to be chosen") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Text") ?>
		</label>
		<div class="controls">
			<textarea name="texto" class="redo" rows="5" maxlength="140" required="required"><?php if($post) echo @$_POST['texto'] ?></textarea>
			<span class="help-block"><?php _e('The text that will be tweeted') ?></span>
		</div>
	</div>
	<center><input type="submit" value="<?php _e('Send') ?>" class="btn btn-primary" required="required"></center>
</form>
<?php
break;

case "editar":
case "actualizar":


if( ! isset($_GET['id']) || ! is_numeric($_GET['id']) )
	typ_die( __("You must specify a valid ID") );
$t = obt_tweet( $_GET['id'] );
if( false == $t )
	typ_die( __("That tweet doesn't exist.") );

construir( 'cabecera', sprintf( __("Edit the tweet: %s"), $t->nombre ), true );

?>
<h3><?php _e("Edit the tweet") ?>: <i><?php echo ucwords( $t->nombre ) ?></i></h3>
<a href="<?php echo url() . 'tweets.php' ?>" class="btn btn-link pull-right"><?php _e("Back to tweets") ?> &rarr;</a>
<hr>
<?php
	if( $post ) {
		$nombre = trim( $zerdb->real_escape( @$_POST['nombre'] ) );
		$tweet = trim( $zerdb->real_escape( @$_POST['texto'] ) );

		if( ! comprobar_args(@$_POST['nombre'], @$_POST['texto'] ) ) {
			typ_die( __("Cheatin', uh?!") );
		}elseif( vacios($_POST['nombre'], $_POST['texto']) ) {
			agregar_error( __("You can't leave empty fields.") );
		}elseif( strlen($nombre) > 10 || strlen($tweet) > 140 ) {
			agregar_error( __("You can't overpass the limits.") );
		}else{
			$zerdb->update($zerdb->tweets, array(
					"nombre" => $nombre,
					"tweet" => $texto,
				) )->where("id", $t->id )->_();
			agregar_info( __("Updated. :)") );
		}
	}
?>
<form action="<?php echo url() . 'tweets.php?accion=editar&id=' . $_GET['id'] ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Name") ?>
		</label>
		<div class="controls">
			<input type="text" name="nombre" value="<?php if($post) echo @$_POST['nombre']; else echo $t->nombre ?>" required="required" maxlength="10" id="nombre">
			<span class="help-block"><small><?php _e("The name of the tweet to edit") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Text") ?>
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
	<center><input type="submit" name="actualizar" value="<?php _e("Update") ?>" id="actualizar" required="required" class="btn btn-primary"></center>
</form>
<?php
break;
case "eliminar":

if( ! isset($_GET['ids'] ) || ! is_string($_GET['ids']) || ! preg_match('/^[\d]+$/', @$_GET['ids']) ) /* Je, je, je, fail (?) */
	typ_die( __("Wrong way to delete. Wrong.") );

$zerdb->delete( $zerdb->twitter ) -> where("id", $_GET['ids'] )->_();

construir('cabecera', __('Delete tweets') );

agregar_info( __("The selected tweets have been deleted") );

echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Back</a></li></ul>"), url() . 'index.php');
break;
default:

$id = isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) ? true : false;

	switch($id) {
		case true:
		
		$t = obt_tweet( $_GET['id'] );

		if( ! $t || ! $t->nums > 0)
			typ_die( __("This tweet doesn't exist.") );

		construir( 'cabecera', sprintf( __('Tweet of %s'), $t->nombre), true );
		?>
		<h3><?php _e("Tweet of:") ?><i><?php echo ucwords( $t->nombre ) ?></i></h3>
		<a href="<?php echo url() . 'tweets.php' ?>" class="btn btn-link pull-right"><?php _e("Back to tweets") ?> &rarr;</a>
		<hr>
		<?php
		if( isset($_POST['tuitear']) ) {
			$obt = obt_oauth();
			$tw = new zer_twitter($obt->consumer_key, $obt->consumer_secret, $obt->access_token, $obt->access_token_secret);
			$tweet = $tw->tuitear( $t->tweet ) or agregar_error($tw->error, false, false);
			if(!$tw->comp_error)
				agregar_info( __("Tweet sent successfully") );
		}
		?>
		<div class="well"><?php echo $t->tweet ?></div>
		<form action="<?php echo url( true ) ?>" method="POST">
		<button type="submit" name="tuitear" value="true:D" class="btn btn-link">
			<i class="icon-pencil"></i>&nbsp; <?php _e("Tweet") ?></i>
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
		<a class="btn btn-link pull-right" href="<?php echo url() ?>tweets.php?accion=agregar"><i class="icon-plus"></i>&nbsp;<?php _e('Add new') ?></a><hr>
		<?php
		endif;
		if( false == $t) {
			agregar_error( sprintf( __("You don't have tweets... <a href=\"%s\">Would you like to add one?</a>"), url() . 'tweets.php?accion=agregar' ) );
			construir('pies');
			exit();
		}
		while( $r = $t->r->fetch_array() ) { ?>
			<p class="lead"><u><?php echo $r['nombre'] ?></u></p>
			<div class="well"><?php echo $r['tweet'] ?></div>
			<a href="<?php echo url() . 'tweets.php?id=' . $r['id'] ?>" class="btn btn-link text-center">
				<i class="icon-eye-open"></i>&nbsp;<?php _e("View") ?>
			</a> |
			<a href="<?php echo url() . 'tweets.php?accion=editar&id=' . $r['id'] ?>" class="btn btn-link text-center">
				<i class="icon-pencil"></i>&nbsp;<?php _e("Edit") ?>
			</a> |
			<a href="<?php echo url() . 'tweets.php?accion=eliminar&ids='. $r['id'] ?>" class="btn btn-link text-center">
				<i class="icon-trash"></i>&nbsp;<?php _e("Delete") ?>
			</a>
			<hr>
		<?php
		}
	}
}
modal_tweets();
construir('pies');