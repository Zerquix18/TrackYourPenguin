<?php
/**
*
* Archivo de los tweets
*
* Borra y edita los tweets
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.0.1
*
**/

require_once( dirname( __FILE__ ) . '/typ-load.php' );

if( ! oauth_configurado() ) {
	construir('cabecera');
	agregar_error( sprintf(
		"Antes de configurar los tweets, debes configurar el <a href=\"%s\">OAuth</a> de conexión a Twitter",
		url() . 'oauth.php'
		) );
	 exit( construir('pies') );
}

comprobar( false );

if( ! es_admin() )
	typ_die("Haciendo trampa, ¿eh?");

$accion = isset($_GET['accion']) && !empty($_GET['accion']) && is_string($_GET['accion']) ? $_GET['accion'] : '';

$post = ( "POST" == $_SERVER['REQUEST_METHOD'] );

switch( $accion ) {
	case "agregar":

	construir( 'cabecera', 'Agregar tweet', true );

	if( $post ) {

		$nombre = $zerdb->proteger(  @$_POST['nombre']);
		$texto = trim( $zerdb->proteger( @$_POST['texto']) );

		if( ! comprobar_args( @$_POST['nombre'], @$_POST['texto'] ) ) {
			agregar_error("Error en los datos HTML.");
		}elseif( vacio($nombre) || vacio($texto) ) {
			agregar_error("No puedes dejar campos vacíos");
		}elseif( strlen($nombre) > 10 || strlen($texto) > 140 ) {
			agregar_error("No puedes sobrepasar los límites");
		}else{
			$agregar = agregar_tweet( $nombre, $texto );
			agregar_info("Tweet agregado");
			echo redireccion(url() . 'tweets.php', 2);
		}
	}

?>
<h3> Agregar tweet </h3><a href="<?php echo url() . 'tweets.php' ?>" class="btn btn-link pull-right">Volver a los tweets &rarr;</a><hr>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
		<div class="control-group">
		<label class="control-label">
			Nombre
		</label>
		<div class="controls">
			<input type="text" name="nombre" <?php if($post) echo 'value="' . @$_POST['nombre'] . '"' ?> required="required"
			maxlength="10">
			<span class="help-block">Nombre del tweet a ser elegido</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Texto
		</label>
		<div class="controls">
			<textarea name="texto" class="redo" rows="5" maxlength="140" required="required"><?php if($post) echo 'value="' . @$_POST['texto'] . '"' ?></textarea>
			<span class="help-block">El texto que se publicará en el tweet</span>
		</div>
	</div>
	<center><input type="submit" value="Enviar" class="btn btn-primary" required="required"></center>
</form>
<?php
break;

case "editar":
case "actualizar":

if( ! es_admin() )
	typ_die("Haciendo trampa, ¿eh?");

if( ! isset($_GET['id']) || empty($_GET['id']) ||  ! is_numeric($_GET['id'] ) )
	typ_die("Debes especificar un ID válido");

$t = obt_tweet( $_GET['id'] );

if( ! $t || ! $t->nums > 0 )
	typ_die("Este tweet no existe");

construir( 'cabecera', sprintf("Editar el tweet: %s", $t->nombre ), true );

?>
<h3>Editar el tweet: <i><?php echo ucwords( $t->nombre ) ?></i></h3>
<a href="<?php echo url() . 'tweets.php' ?>" class="btn btn-link pull-right">Volver a los tweets &rarr;</a>
<hr>
<?php
	if( $post ) {
		$nombre = trim( $zerdb->proteger( @$_POST['nombre'] ) );
		$tweet = trim( $zerdb->proteger( @$_POST['texto'] ) );

		if( ! comprobar_args(@$_POST['nombre'], @$_POST['texto'] ) ) {
			agregar_error("Error en los datos HTML");
		}elseif( vacios($_POST['nombre'], $_POST['texto']) ) {
			agregar_error("No puedes dejar campos vacíos");
		}elseif( strlen($nombre) > 10 || strlen($tweet) > 140 ) {
			agregar_error("No puedes sobrepasar los límites");
		}else{
			$actualizar = actualizar_tweet( $t->id, array($nombre, $tweet) );
			agregar_info("Actualizado :)");
		}
	}
?>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			Nombre
		</label>
		<div class="controls">
			<input type="text" name="nombre" value="<?php if($post) echo @$_POST['nombre']; else echo $t->nombre ?>" required="required" maxlength="10" id="nombre">
			<span class="help-block"><small>El nombre del tracker a editar</small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Texto
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
	<center><input type="submit" name="actualizar" value="Actualizar" id="actualizar" required="required" class="btn btn-primary"></center>
</form>
<?php
break;
case "eliminar":

if( ! isset($_GET['ids'] ) || ! is_string($_GET['ids']) || ! preg_match('/^[\d]+$/', @$_GET['id']) ) /* Je, je, je, fail (?) */
	typ_die("Mala forma de borrar, mala.");

if( ! es_admin() )
	typ_die("Haciendo trampa, ¿eh?");

$id = $zerdb->proteger( $_GET['id'] );

eliminar_tracker($id);

construir('cabecera', 'Eliminar tweets');

agregar_info( sprintf("Han sido eliminados los tweets eleccionados. <b>%s</b>") );

echo sprintf("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>", url() . 'index.php');
break;
default:

$id = isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) ? true : false;

	switch($id) {
		case true:
		
		$t = obt_tweet( $_GET['id'] );

		if( ! $t || ! $t->nums > 0)
			typ_die("Este tweet no existe");

		construir( 'cabecera', sprintf('Tweet de %s', $t->nombre), true );
		?>
		<h3>Tweet de: <i><?php echo ucwords( $t->nombre ) ?></i></h3>
		<a href="<?php echo url() . 'tweets.php' ?>" class="btn btn-link pull-right">Volver a los tweets &rarr;</a>
		<hr>
		<?php

		if( isset($_POST['tuitear'] ) && oauth_configurado() ) {
			$obt = obt_oauth();
			$tw = new zer_twitter($obt->consumer_key, $obt->consumer_secret, $obt->access_token, $obt->access_token_secret);
			$tweet = $tw->tuitear( $t->tweet ) or agregar_error($tw->error, false, false);
			if(!$tw->comp_error)
				agregar_info("Tweet enviado exitosamente");
		}
		?>
		<div class="well"><?php echo $t->tweet ?></div>
		<form action="<?php echo url( true ) ?>" method="POST">
		<button type="submit" name="tuitear" value="true:D" class="btn btn-link">
			<i class="icon-pencil"></i>&nbsp; Tuitear</i>
		</button>
		</form>
		<?php
		break;
		case false:
		construir( 'cabecera', 'Tweets', true);

		$t = obt_tweets();

		?>
		<h3>Tweets</h3><a class="btn btn-link pull-right" href="<?php echo url() ?>tweets.php?accion=agregar"><i class="icon-plus"></i>&nbsp;Agregar nuevo</a><hr>
		<?php

		if( $t->nums == 0) {
			agregar_error( sprintf("No tienes trackers... <a href=\"%s\">agregar uno</a>", url() . 'tweets.php?accion=agregar' ) );
			construir('pies');
			exit();
		}

		$tweets = mysql_query( $t->query );

		while( $t->fetch = mysql_fetch_array($tweets) ) { ?>
			<p class="lead"><u><?php echo $t->fetch['nombre'] ?></u></p>
			<div class="well"><?php echo $t->fetch['tweet'] ?></div>
			<a href="<?php echo url() . 'tweets.php?id=' . $t->fetch['id'] ?>" class="btn btn-link text-center">
				<i class="icon-eye-open"></i>&nbsp;Ver
			</a> |
			<a href="<?php echo url() . 'tweets.php?accion=editar&id=' . $t->fetch['id'] ?>" class="btn btn-link text-center">
				<i class="icon-pencil"></i>&nbsp;Editar
			</a> |
			<a href="<?php echo url() . 'tweets.php?accion=eliminar&ids='. $t->fetch['id'] ?>" class="btn btn-link text-center">
				<i class="icon-trash"></i>&nbsp;Eliminar
			</a>
			<hr>
		<?php
		}
	}
}

construir('pies');