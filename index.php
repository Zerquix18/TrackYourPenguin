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
		typ_die( __("Haciendo trampa, ¿eh?") );
	construir('cabecera', __('Agregar tracker'), true );
	?>
	<h3><?php _e("Agregar tracker") ?></h3><a href="<?php echo url() ?>" class="btn btn-link pull-right"><?php _e("Volver") ?> &rarr;</a><hr>
	<?php
		if( $post ) {
			$nombre = @$zerdb->proteger( $_POST['nombre'] );
			$fuente = @$zerdb->proteger( $_POST['fuente'] );
			$img = @$archivos->subir('img');
			$imgbg = @$archivos->subir('imgbg');

			if( ! comprobar_args( @$_POST['nombre'], @$_POST['fuente'] ) ) {
				agregar_error( __("Error en los datos HTML"), true, true);
			}elseif( vacios( $_POST['nombre'], $_POST['fuente'] ) ) {
				agregar_error( __("No puedes dejar datos vacíos"), true, true);
			}elseif( ! preg_match('/\./', $fuente ) ) {
				agregar_error( __("Estoy seguro de que la tipografía debe llevar un punto"), true, true);
			}elseif( 'ttf' !== end( explode('.', $fuente) ) ) {
				agregar_error( __("La fuente no tiene formato correcto"), true, true);
			}elseif( ! file_exists( INC . strtolower($fuente) ) ) {
				agregar_error( __("La fuente no existe en el directorio de incluidos"), true, true);
			}elseif( $img->comp_error ) {
				agregar_error( $img->error, true, true );
			}elseif( $imgbg->comp_error ) {
				agregar_error( $imgbg->error, true, true );
			}else{
				agregar_tracker($nombre, $img->archivo, $imgbg->archivo, $fuente);
				agregar_info( __("El tracker ha sido creado con éxito") );
			}
		}
	?>
	<form method="POST" enctype="multipart/form-data" class="form-horizontal" action="<?php echo url( true ) ?>">
		<div class="control-group">
		<label class="control-label">
			<?php _e("Nombre del tracker") ?>
		</label>
		<div class="controls">
			<input type="text" id="nombre" name="nombre" <?php if($post) echo 'value="' . @$_POST['nombre'] . '"' ?> required="required">
			<span class="help-block"><?php _e("El nombre o personaje del tracker") ?></span>
		</div>
	</div>
		<div class="control-group">
		<div class="controls">
			<input type="file" name="img" required="required">
			<span class="help-block"><?php _e("Imagen de resultado cuando actualices el tracker") ?></span>
		</div>
	</div>
		<div class="control-group">
		<div class="controls">
			<input type="file" name="imgbg" required="required">
			<span class="help-block"><?php _e("Imagen desde la cual se creará la del resultado") ?></span>
		</div>
	</div>
		<div class="control-group">
		<label class="control-label">
		<?php _e("Fuente") ?>
		</label>
		<div class="controls">
			<input type="text" name="fuente" id="fuente" value="<?php if($post) echo @$_POST['fuente']; else echo 'typ.ttf'; ?>" required="required">
			<span class="help-block"><?php _e("Fuente con la que se escribirá en la imagen") ?></span>
		</div>
	</div>
	<hr><center><input type="submit" value="<?php _e("Enviar") ?>" class="btn btn-large btn-primary"></center>
</form>
	<?php
	break;
	case "editar":
	if( ! es_admin() )
		typ_die( __("¿Haciendo trampa, eh?") );

	if( !isset($_GET['id']) || ! is_numeric($_GET['id']) )
		typ_die( __("Debes especificar un id correcto") );

	$t = obt_tracker( $zerdb->proteger($_GET['id']) );

	if( ! $t || ! $t->nums > 0)
		typ_die( __("No existe ese tracker") );

	construir('cabecera', sprintf( __('Editar tracker: %s'), ucwords($t->personaje) ) );

	if( $post ) {
		$nombre = @$zerdb->proteger( $_POST['personaje'] );
		$fuente = @$zerdb->proteger( $_POST['fuente'] );

		if( ! comprobar_args( @$_POST['personaje'], @$_POST['fuente']) ) {
			agregar_error(__("Error en los datos HTML"));
		}elseif( vacios($_POST['personaje'], $_POST['fuente']) ) {
			agregar_error(__("No puedes dejar campos vacíos"));
		}elseif( ! preg_match('/\./', $fuente) ) {
			agregar_error(__("¿Así que la fuente no lleva punto?"));
		}elseif( ! file_exists( INC . strtolower($fuente) ) ) {
			agregar_error(__("El archivo de la fuente no existe en el directorio de incluidos") );
		}else{
			actualizar_tracker( $t->id, $nombre, $fuente );
			agregar_info(__("Tracker editado exitosamente"));
			echo redireccion( url() . '?id=' . $t->id, 2);
		}
	}
?>
	<h3><?php echo sprintf( __("Edita el tracker de <i>%s</i>"), ucwords($t->personaje) ) ?></h3><hr>
	<a href="<?php echo url() ?>index.php?id=<?php echo $t->id ?>" class="pull-right btn btn-link">Volver al tracker &rarr;</a>
<form method="POST" class="form-horizontal" class="<?php echo url( true ) ?>">
	<div class="control-group">
		<label class="control-label">
	<?php _e("Nombre") ?>
		</label>
		<div class="controls">
			<input type="text" name="personaje" value="<?php if($post) echo @$_POST['personaje']; else echo $t->personaje ?>" required="required" id="personaje">
			<span class="help-block"><?php _e("Nombre del tracker") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Fuente") ?>
		</label>
		<div class="controls">
			<input type="text" name="fuente" value="<?php if($post) echo @$_POST['fuente']; else echo $t->fuente ?>" required="required" id="fuente">
			<span class="help-block"><?php _e("La fuente del Tracker") ?></span>
		</div>
	</div>
	<center><input type="submit" name="actualizar_tracker" value="<?php _e("Actualizar") ?>" required="required" id="actualizar_tracker" class="btn btn-primary"></center>
</form>
<ul class="pager">
<li class="next"><a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>"><?php _e("Editar parámetros") ?> &rarr;</a></li>
</ul>
	<?php
break;
case "eliminar":

if( ! es_admin() )
	typ_die(  __("Haciendo trampa, ¿eh?") );

if( ! isset($_GET['ids'] ) || empty($_GET['ids']) || ! preg_match('/^[\d\,]+$/', @$_GET['ids']) ) /* Je, je, je, fail (?) */
	typ_die( __("Mala forma de borrar, mala.") );

$ids = explode(',', $_GET['ids']);

$borrados = array();

foreach( $ids as $id ) {
	eliminar_tracker( $id );
}

construir('cabecera',  __('Eliminar trackers') );

agregar_info( __("Han sido eliminados los trackers seleccionados") );

echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr;Volver</a></li></ul>", url() . 'index.php' ) );

construir('pies');
break;

case "ver":
case "detalles":
default:

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? true : false;

switch( $id ) {
	case true:
	$id = $zerdb->proteger( $_GET['id'] );
	$t = obt_tracker( $id );

	if( ! $t || !$t->nums > 0)
		typ_die( __("El ID que especificas no existe") );

	construir( 'cabecera', sprintf( __('Tracker de %s') , ucwords($t->personaje) ), true );
	?>
	<h3><?php _e("Detalles del tracker") ?>: <i><?php echo ucwords( $t->personaje ) ?></i></h3><hr>
	<p><b><?php _e("Nombre") ?></b>: <i><?php echo ucwords( $t->personaje ) ?></i></p>
	<p><b><?php _e("Fuente") ?></b>: <i><?php echo strtolower( $t->fuente ) ?></i><?php echo (! file_exists(INC.$t->fuente) ) ? __('(no existe)') : '' ?></p>
	<?php if( es_admin() ): ?><a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>" class="pull-right btn btn-link"><i class="icon-wrench"></i>&nbsp; <?php _e("Editar parámetros") ?></a><?php endif ?>
	<hr><center><b><?php _e("Imagen de fondo") ?></b>:</center><br>
	<center><img src="<?php echo url() . IMG . $t->imgbg ?>" title="<?php echo $t->imgbg ?>" alt="<?php echo $t->imgbg ?>" /></center>
	<hr><center><b><?php _e("Imagen") ?></b>:</center><br>
	<center><img src="<?php echo url() . IMG . $t->img ?>" title="<?php echo $t->img ?>" alt="<?php echo $t->img ?>" /></center><br>
	<?php
break;
	case false:
	default:

	construir( 'cabecera', __('Trackers') );

	$q = new extraer($zerdb->trackers, "*");

	if( ! $q || !$q->nums > 0) {
		agregar_error( sprintf( __("No tienes trackers :( <a href=\"%s\">Quieres agregar uno?</a> :)"), url() . '?accion=agregar'), false, false);
		construir( 'pies' );
		exit(0);
	}
	$pagina = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
	$limit = 'LIMIT '. ($pagina-1) * TRACKERS_PAG . ',' . TRACKERS_PAG;
	$fetch = mysql_query( $q->query . ' ' . $limit );
	$paginas = ceil( $q->nums / TRACKERS_PAG );
	if( isset($_POST['eliminar']) && es_admin() ) {
		if( ! isset($_POST['ids'] ) ) {
			agregar_error( __("Debes seleccionar un tracker"), true, true);
		}elseif( ! is_array($_POST['ids']) || empty($_POST['ids']) ) {
			agregar_error( __("Debes seleccionar al menos uno"), true, true);
		}else{
			$nums = count($_POST['ids']);
			$singular = __("¿Seguro que quieres eliminar %s tracker? <hr> <a href=\"%s\" class=\"btn btn-danger\">Sí</a>");
			$plural = __("¿Seguro que quieres eliminar %s trackers? <hr> <a href=\"%s\" class=\"btn btn-danger\">Sí</a>");
			$eliminar_url =	url() . '?accion=eliminar&ids=' . implode(',', $_POST['ids']);
			agregar_info(
					sprintf( _n($singular, $plural, $nums),
					$nums, $eliminar_url),
					false, true
				);
		}
	}
?>
	<h3><?php _e("Trackers") ?></h3><hr><form method="POST"><?php if( es_admin() ) : ?>
	<a href="<?php echo url() ?>index.php?accion=agregar" class="pull-right btn btn-link"><i class="icon-plus"></i>&nbsp;<?php _e("Agregar nuevo") ?></a><br>
		<?php endif ?><center>
	<?php
	while($q->fetch = mysql_fetch_array($fetch) ) {
		if( es_admin() ) :
		echo sprintf(
					"<input type=\"checkbox\" name=\"ids[]\" value=\"%d\">&nbsp;",
				(int) $q->fetch['id']
			);
		endif;
		/***********************/
		echo sprintf(
					"<a href=\"%s\">%s</a>" . "\n\n",
				url() . '?id=' . $q->fetch['id'],
			ucwords($q->fetch['personaje'])
		);
		/*********************************/
		echo sprintf(
					"<img src=\"%s\" class=\"thumbnail\" title=\"%s\" />\n\n",
				url() . IMG . $q->fetch['img'],
			'Imagen de ' . ucwords( $q->fetch['personaje'])
		);
?>
	<center>
		<a href="<?php echo url() . 'actualizar.php?id=' . $q->fetch['id'] ?>" class="btn btn-success" title="<?php echo sprintf( __("Actualizar el tracker de %s"), ucwords($q->fetch['personaje'] ) ) ?>"><i class="icon-pencil"></i>&nbsp;<?php _e("Actualizar") ?></a> |
		<a href="<?php echo url() . '?id=' . $q->fetch['id'] ?>" title="<?php echo sprintf( __('Ver detalles del tracker de %s'), ucwords( $q->fetch['personaje']) ); ?>" class="btn btn-primary"><i class="icon-eye-open"></i>&nbsp;<?php _e("Detalles")?></a>
		<?php if( es_admin() ) : ?>
		| <a href="<?php echo url() . '?accion=editar&id=' . $q->fetch['id'] ?>" title="<?php echo sprintf( __('Editar el tracker de %s'), ucwords($q->fetch['personaje'] ) )?>" class="btn btn-success"><i class="icon-edit"></i>&nbsp;<?php _e("Editar")?></a>
		<?php endif ?>
		<hr><br>
<?php
	}
	if($pagina > $paginas) {
		agregar_error( __("No existen más trackers...") );
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
<button type="submit" name="eliminar" class="btn btn-danger pull-right"/><i class="icon-trash"></i>&nbsp;<?php _e("Eliminar seleccionados") ?></button>
<?php endif ?>
</form>
	<?php
	}
break;
}
construir('pies');