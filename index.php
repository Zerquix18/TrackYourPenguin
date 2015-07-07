<?php
/**
*
* Archivo inicial
*
* Manipula los trackers, borra, edita, detalla, presenta y da paginación a los trackers
*
* @author Zerquix18
* @link http://trackyourpenguin.com/
* @since 0.1
*
**/
require_once( dirname(__FILE__) . '/typ-load.php' );

comprobar( false );

$accion = ( isset($_GET['accion']) && !empty($_GET['accion']) && is_string($_GET['accion']) ) ? $_GET['accion'] : ''; 
$post = ( "POST" == $_SERVER['REQUEST_METHOD'] );
switch( $accion ) {
	case "agregar":
	if( ! es_admin() )
		typ_die( __("Cheatin', uh?!") );
	construir('cabecera', __('Add tracker'), true );
	?>
	<h3><?php _e("Add tracker") ?></h3><a href="<?php echo url() ?>" class="btn btn-link pull-right"><?php _e("Back") ?> &rarr;</a><hr>
	<?php
		if( $post ) {
			$nombre = @$zerdb->real_escape( trim($_POST['nombre']) );
			$fuente = @$zerdb->real_escape( trim($_POST['fuente']) );
			$img = @$archivos->subir('img');

			if( ! comprobar_args( @$_POST['nombre'], @$_POST['fuente'] ) ) {
				typ_die( __("Cheatin', uh?!") );
			}elseif( vacios( $_POST['nombre'], $_POST['fuente'] ) ) {
				agregar_error( __("You can't leave empty fields."), true, true);
			}elseif( ! preg_match('-\.-', $fuente ) ) {
				agregar_error( __("I'm pretty sure that the font must have a point."), true, true);
			}elseif( 'ttf' !== end( explode('.', $fuente) ) ) {
				agregar_error( __("The font doesn't have the right format. It must be ttf (TrueType Font)"), true, true);
			}elseif( ! file_exists( PATH . INC . strtolower($fuente) ) ) {
				agregar_error( __("The fon't doesn't exist in the directory of included files."), true, true);
			}elseif( $img->comp_error ) {
				agregar_error( $img->error, true, true );
			}else{
				agregar_tracker($nombre, $img->archivo, $img->archivo2, $fuente);
				agregar_info( __("The tracker has been created successfully.") );
				echo redireccion( url(), 2 );
			}
		}
	?>
	<form method="POST" enctype="multipart/form-data" class="form-horizontal" action="<?php echo url() . 'index.php?accion=agregar' ?>">
		<div class="control-group">
		<label class="control-label">
			<?php _e("Tracker's name") ?>
		</label>
		<div class="controls">
			<input type="text" id="nombre" name="nombre" <?php if($post) echo 'value="' . @$_POST['nombre'] . '"' ?> required="required">
			<span class="help-block"><?php _e("The tracker's name or the character") ?></span>
		</div>
	</div>
		<div class="control-group">
		<div class="controls">
			<input type="file" name="img" required="required">
			<span class="help-block"><?php _e("Tracker's image") ?></span>
		</div>
	</div>
		<div class="control-group">
		<label class="control-label">
		<?php _e("Font") ?>
		</label>
		<div class="controls">
			<input type="text" name="fuente" id="fuente" value="<?php if($post) echo @$_POST['fuente']; else echo 'typ.ttf'; ?>" required="required">
			<span class="help-block"><?php _e("The font used to write in the image.") ?></span>
		</div>
	</div>
	<hr><center><input type="submit" value="<?php _e("Send") ?>" class="btn btn-large btn-primary"></center>
</form>
	<?php
	break;
	case "editar":
	if( ! es_admin() )
		typ_die( __("Cheatin', uh?!") );

	if( !isset($_GET['id']) || ! is_numeric($_GET['id']) ) // si lleva una comilla, guión, lo que sea, is_numeric devolverá false ;)
		typ_die( __("You must specify a valid ID.") );

	$t = obt_tracker( $_GET['id'] );

	if( true !== ($t && $t->nums > 0 ) )
		typ_die( __("That tracker doesn't exist.") );

	construir('cabecera', sprintf( __("Edit %s's tracker"), ucwords($t->personaje) ) );

	if( $post ) {
		$nombre = @$zerdb->real_escape( trim($_POST['personaje']) );
		$fuente = @$zerdb->real_escape( trim($_POST['fuente']) );

		if( ! comprobar_args( @$_POST['personaje'], @$_POST['fuente']) ) {
			typ_die(__("Cheatin', uh?!"));
		}elseif( vacios($_POST['personaje'], $_POST['fuente']) ) {
			agregar_error(__("You can't leave empty fields.") );
		}elseif( ! preg_match('/\./', $fuente) ) {
			agregar_error(__("So doesn't the font have a point?"));
		}elseif( ! file_exists( INC . strtolower($fuente) ) ) {
			agregar_error(__("The font file doesn't exist in the directory of included files.") );
		}else{
			$zerdb->update($zerdb->trackers, array("personaje" => $nombre, "fuente" => $fuente) )->where("id", $id)->_();
			agregar_info(__("Tracker edited successfully."));
			echo redireccion( url() . '?id=' . $t->id, 2);
		}
	}
?>
	<h3><?php echo sprintf( __("Edit <i>%s</i>'s tracker"), ucwords($t->personaje) ) ?></h3><hr>
	<a href="<?php echo url() ?>index.php?id=<?php echo $t->id ?>" class="pull-right btn btn-link"><?php _e('Back to tracker') ?> &rarr;</a>
<form method="POST" class="form-horizontal" class="<?php echo url( true ) ?>">
	<div class="control-group">
		<label class="control-label">
	<?php _e("Name") ?>
		</label>
		<div class="controls">
			<input type="text" name="personaje" value="<?php if($post) echo @$_POST['personaje']; else echo $t->personaje ?>" required="required" id="personaje">
			<span class="help-block"><?php _e("Tracker's name") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Font") ?>
		</label>
		<div class="controls">
			<input type="text" name="fuente" value="<?php if($post) echo @$_POST['fuente']; else echo $t->fuente ?>" required="required" id="fuente">
			<span class="help-block"><?php _e("The tracker's font") ?></span>
		</div>
	</div>
	<center><input type="submit" name="actualizar_tracker" value="<?php _e("Update") ?>" required="required" id="actualizar_tracker" class="btn btn-primary"></center>
</form>
<ul class="pager">
<li class="next"><a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>"><?php _e("Edit parameters") ?> &rarr;</a></li>
</ul>
	<?php
break;
case "eliminar":

if( ! es_admin() )
	typ_die(  __("Cheatin', uh?!") );

construir('cabecera',  __('Delete trackers') );

if( ! isset($_GET['ids'] ) || empty($_GET['ids']) || ! preg_match('/^[\d\,]+$/', @$_GET['ids']) ) /* Je, je, je, fail (?) */
	typ_die( __("Wrong way to delete. WRONG.") );
$ids = explode(',', $_GET['ids']);
foreach( $ids as $id ) {
	$t = obt_tracker($id);
	if( false == $t ) continue; // no existe? continuemos...
	$archivos->eliminar( IMG . $t->imgbg ); 
	$archivos->eliminar( IMG . $t->img );
	$zerdb->delete($zerdb->trackers)->where('id', $id)->_();
	$zerdb->delete($zerdb->parametros)->where('tracker', $id)->_();
}
agregar_info( __("The selected trackers have been deleted.") );
echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr;Back</a></li></ul>"), url() . 'index.php' );
break;
case "ver":
case "detalles":
default:

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? true : false;

switch( $id ) {
	case true:
	$t = obt_tracker( $_GET['id'] );

	if( false == $t )
		typ_die( __("The ID you specified doesn't exist.") );

	construir( 'cabecera', sprintf( __("%s's tracker") , ucwords($t->personaje) ), true );
	?>
	<h3><?php _e("Tracker details") ?>: <i><?php echo ucwords( $t->personaje ) ?></i></h3><hr>
	<p><strong><?php _e("Name") ?></strong>: <i><?php echo ucwords( $t->personaje ) ?></i></p>
	<p><strong><?php _e("Font") ?></strong>: <i><?php echo strtolower( $t->fuente ) ?></i><?php echo (! file_exists(INC.$t->fuente) ) ? __("(it doesn't exist)") : '' ?></p>
	<?php if( es_admin() ): ?><a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>" class="pull-right btn btn-link"><i class="icon-wrench"></i>&nbsp; <?php _e("Edit parameters") ?></a><?php endif ?>
	<hr><center><strong><?php _e("Background image") ?></strong>:</center><br>
	<center><img src="<?php echo url() . IMG . $t->imgbg ?>" title="<?php echo $t->imgbg ?>" alt="<?php echo $t->imgbg ?>" /></center>
	<hr><center><strong><?php _e("Image") ?></strong>:</center><br>
	<center><img src="<?php echo url() . IMG . $t->img ?>" title="<?php echo $t->img ?>" alt="<?php echo $t->img ?>" /></center><br>
	<?php
break;
	case false:
	default:

	construir( 'cabecera', __('Trackers') );

	$q = $zerdb->select($zerdb->trackers)->_();

	if( ! $q || !$q->nums > 0) {
		if( es_admin() )
			agregar_error( sprintf( __("You don't have trackers ... <a href=\"%s\">Would you like to add one?</a> :)"), url() . '?accion=agregar'), false, false);
		else
			agregar_error( __("There are not trackers. Tell the admin. :/") );
		construir( 'pies' );
		exit(0);
	}
	$pagina = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
	$limit = 'LIMIT '. ($pagina-1) * 5 . ',' . 5;
	$fetch = $zerdb->query( $zerdb->query . ' ' . $limit );
	$paginas = ceil( $q->nums / 5 );
	if( isset($_POST['eliminar']) ) {
		if( ! isset($_POST['ids'] ) ) {
			agregar_error( __("You must select a tracker."), true, true);
		}elseif( ! is_array($_POST['ids']) || empty($_POST['ids']) ) {
			agregar_error( __("You must select at least one."), true, true);
		}else{
			$nums = count($_POST['ids']);
			$singular = __("Are you sure you want to delete %s tracker?");
			$plural = __("Are you sure you want to delete %s trackers?");
			$eliminar_url =	url() . '?accion=eliminar&ids=' . implode(',', $_POST['ids']);
			echo sprintf(
				"<script type=\"text/javascript\">window.onload = function() { cambiar_modal('%s', '%s', '%s', {'abrir' :  true} );  };</script>",
					__('Confirmation'),
					sprintf( _n($singular, $plural, $nums), $nums ),
					'<a class="btn btn-danger" href="'. $eliminar_url .  '">' .  __('Delete') .'</a> | <button class="btn" data-dismiss="modal" aria-hidden="true">' . __('Close')  . '</button>'
				);
		}
	}
?>
	<h3><?php _e("Trackers") ?></h3><hr><form method="POST"><?php if( es_admin() ) : ?>
	<a href="<?php echo url() ?>index.php?accion=agregar" class="pull-right btn btn-link"><i class="icon-plus"></i>&nbsp;<?php _e("Add new") ?></a><br>
		<?php endif ?><center>
	<?php
	while( $q = $fetch->r->fetch_object() ) {
		if( es_admin() ) : // can't delete, bro ;)
		echo sprintf(
					"<input type=\"checkbox\" name=\"ids[]\" value=\"%s\">&nbsp;", $q->id
			);
		endif;
		/***********************/
		echo sprintf(
					"<a href=\"%s\">%s</a>" . "\n\n",
				url() . '?id=' . $q->id,
			ucwords($q->personaje)
		);
		/*********************************/
		echo sprintf(
					"<img src=\"%s\" class=\"thumbnail\" title=\"%s\" />\n\n",
				url() . IMG . $q->img,
			__('Image of') . ucwords( $q->personaje)
		);
?>
		<a href="<?php echo url() . 'actualizar.php?id=' . $q->id ?>" class="btn btn-success" title="<?php echo sprintf( __("Update %s's tracker"), ucwords($q->personaje) ) ?>"><i class="icon-pencil"></i>&nbsp;<?php _e("Update") ?></a> |
		<a href="<?php echo url() . '?id=' . $q->id ?>" title="<?php echo sprintf( __("View details of %s's tracker"), ucwords( $q->personaje) ); ?>" class="btn btn-primary"><i class="icon-eye-open"></i>&nbsp;<?php _e("Details")?></a>
		<?php if( es_admin() ) : ?>
		| <a href="<?php echo url() . '?accion=editar&id=' . $q->id ?>" title="<?php echo sprintf( __("Update %s's tracker"), ucwords($q->personaje) )?>" class="btn btn-warning"><i class="icon-edit"></i>&nbsp;<?php _e("Edit")?></a>
		<?php endif ?>
		<hr><br>
<?php
	}
	if($pagina > $paginas) {
		agregar_error( __("There are not more trackers.") );
		construir('pies');
		exit(0);
	}
	?>
<div class="pagination pagination-large">
	<ul>
		<?php
		$n2 = ($pagina == '1') ? '1' : $pagina - 1;
		echo '<li><a href="?p='. $n2 . '">&laquo;</a></li>';
		for($i = 1; $i <= $paginas; $i++) :
		$n = ($pagina == $i) ? 'class="disabled"' : '';
		echo '<li ' . $n . '><a href="?p=' . $i . '">' . $i . '</a></li>';
		endfor;
		$n3 = ($pagina < $paginas) ? $pagina + 1 : $pagina;
		echo '<li><a href="?p='. $n3 .'">&raquo;</a></li>';
		?>
	</ul>
</div>
</center>
<?php if( es_admin() ) : ?>
<button type="submit" name="eliminar" class="btn btn-danger pull-right"/><i class="icon-trash"></i>&nbsp;<?php _e("Delete selected") ?></button>
<?php endif ?>
</form>
	<?php
	}
break;
}
construir('pies');