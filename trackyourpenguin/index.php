<?php
/**
*
* Archivo inicial
*
* Manipula los trackers, borra, edita, detalla, presenta y da paginación a los trackers
*
* @author Zerquix18
* @link http://trackyourpenguin.com/
* @since 0.0.1
*
**/
require_once( dirname(__FILE__) . '/typ-load.php' );
comprobar( false );
$accion = ( isset($_GET['accion']) && !empty($_GET['accion']) && is_string($_GET['accion']) ) ? $_GET['accion'] : '';
$post = ( "POST" == $_SERVER['REQUEST_METHOD'] );
switch( $accion ) {
	case "agregar":
	if( ! es_admin() )
		typ_die("Haciendo trampa, ¿eh?");

	construir('cabecera', 'Agregar tracker', true );

	?>
	<h3>Agregar tracker</h3><a href="<?php echo url() ?>" class="btn btn-link pull-right">Volver &rarr;</a><hr>
	<?php
		if( $post ) {
			$nombre = @$zerdb->proteger( $_POST['nombre'] );
			$fuente = @$zerdb->proteger( $_POST['fuente'] );
			$img = @$archivos->subir('img');
			$imgbg = @$archivos->subir('imgbg');

			if( ! comprobar_args( @$_POST['nombre'], @$_POST['fuente'] ) ) {
				agregar_error("Error en los datos HTML", true, true);
			}elseif( vacios( $_POST['nombre'], $_POST['fuente'] ) ) {
				agregar_error("No puedes dejar datos vacíos", true, true);
			}elseif( ! preg_match('/\./', $fuente ) ) {
				agregar_error("Estoy seguro de que la tipografía debe llevar un punto", true, true);
			}elseif( 'ttf' !== end( explode('.', $fuente) ) ) {
				agregar_error("La fuente no tiene formato correcto", true, true);
			}elseif( ! file_exists( INC . strtolower($fuente) ) ) {
				agregar_error("La fuente no existe en el directorio de incluidos", true, true);
			}elseif( $img->comp_error ) {
				agregar_error( $img->error, true, true );
			}elseif( $imgbg->comp_error ) {
				agregar_error( $imgbg->error, true, true );
			}else{
				agregar_tracker($nombre, $img->archivo, $imgbg->archivo, $fuente);
				agregar_info( "El tracker ha sido creado con éxito");
			}
		}
	?>
	<form method="POST" enctype="multipart/form-data" class="form-horizontal">
		<div class="control-group">
		<label class="control-label">
			Nombre del tracker
		</label>
		<div class="controls">
			<input type="text" id="nombre" name="nombre" <?php if($post) echo 'value="' . @$_POST['nombre'] . '"' ?> required="required">
			<span class="help-block">El nombre o personaje del tracker</span>
		</div>
	</div>
		<div class="control-group">
		<div class="controls">
			<input type="file" name="img" required="required">
			<span class="help-block">Imagen de resultado cuando actualices el tracker</span>
		</div>
	</div>
		<div class="control-group">
		<div class="controls">
			<input type="file" name="imgbg" required="required">
			<span class="help-block">Imagen desde la cual se creará la del resultado</span>
		</div>
	</div>
		<div class="control-group">
		<label class="control-label">
		Fuente
		</label>
		<div class="controls">
			<input type="text" name="fuente" id="fuente" value="<?php if($post) echo @$_POST['fuente']; else echo 'typ.ttf'; ?>" required="required">
			<span class="help-block">Fuente con la que se escribirá en la imagen</span>
		</div>
	</div>
	<hr><center><input type="submit" value="Enviar" class="btn btn-large btn-primary"></center>
</form>
	<?php
	break;
	case "editar":
	if( ! es_admin() )
		typ_die("¿Haciendo trampa, eh?");

	if( !isset($_GET['id']) || empty($_GET['id']) || ! is_numeric($_GET['id']) )
		typ_die("Debes especificar un id correcto");

	$t = obt_tracker( $zerdb->proteger($_GET['id']) );

	if( ! $t || ! $t->nums > 0)
		typ_die("No existe ese tracker");

	construir('cabecera', sprintf('Editar tracker: %s', ucwords($t->personaje) ) );

	if( $post ) {
		$nombre = @$zerdb->proteger( $_POST['personaje'] );
		$fuente = @$zerdb->proteger( $_POST['fuente'] );

		if( ! comprobar_args( @$_POST['personaje'], @$_POST['fuente']) ) {
			agregar_error("Error en los datos HTML");
		}elseif( vacios($_POST['personaje'], $_POST['fuente']) ) {
			agregar_error("No puedes dejar campos vacíos");
		}elseif( ! preg_match('/\./', $fuente) ) {
			agregar_error("¿Así que la fuente no lleva punto?");
		}elseif( ! file_exists( INC . strtolower($fuente) ) ) {
			agregar_error("El archivo de la fuente no existe en el directorio de incluidos");
		}else{
			actualizar_tracker( $t->id, $nombre, $fuente );
			agregar_info("Tracker editado exitosamente");
			echo redireccion( url() . '?id=' . $t->id, 2);
		}
	}
?>
	<h3>Editar el tracker de <i><?php echo ucwords($t->personaje) ?></i></h3><hr>
	<a href="<?php echo url() ?>index.php?id=<?php echo $t->id ?>" class="pull-right btn btn-link">Volver al tracker &rarr;</a>
<form method="POST" class="form-horizontal" class="<?php echo url( true ) ?>">
	<div class="control-group">
		<label class="control-label">
	Nombre
		</label>
		<div class="controls">
			<input type="text" name="personaje" value="<?php if($post) echo @$_POST['personaje']; else echo $t->personaje ?>" required="required" id="personaje">
			<span class="help-block">Nombre del tracker</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Fuente
		</label>
		<div class="controls">
			<input type="text" name="fuente" value="<?php if($post) echo @$_POST['fuente']; else echo $t->fuente ?>" required="required" id="fuente">
			<span class="help-block">La fuente del Tracker</span>
		</div>
	</div>
	<center><input type="submit" name="actualizar_tracker" value="Actualizar" required="required" id="actualizar_tracker" class="btn btn-primary"></center>
</form>
<ul class="pager">
<li class="next"><a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>">Editar parámetros &rarr;</a></li>
</ul>
	<?php
break;
case "eliminar":

if( ! isset($_GET['ids'] ) || empty($_GET['ids']) || ! preg_match('/^[\d\,]+$/', @$_GET['ids']) ) /* Je, je, je, fail (?) */
	typ_die("Mala forma de borrar, mala.");

if( ! es_admin() )
	typ_die("Haciendo trampa, ¿eh?");

$ids = explode(',', $_GET['ids']);

$borrados = array();

foreach( $ids as $id ) {
	eliminar_tracker( $id );
}

construir('cabecera', 'Eliminar trackers');

agregar_info("Han sido eliminados los siguientes trackers seleccionados");

echo sprintf("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>", url() . 'index.php');

construir('pies');
break;

case "ver":
case "detalles":
default:

$id = isset($_GET['id']) && !empty($_GET['id']) && is_string($_GET['id']) ? true : false;

switch( $id ) {
	case true:
	$id = $zerdb->proteger( $_GET['id'] );
	$t = obt_tracker( $id );

	if( ! $t || !$t->nums > 0)
		typ_die("El ID que especificas no existe");

	construir( 'cabecera', sprintf('Tracker de %s', ucwords($t->personaje) ), true );
	?>
	<h3>Detalles del tracker: <i><?php echo ucwords( $t->personaje ) ?></i></h3><hr>
	<p><b>Nombre</b>: <i><?php echo ucwords( $t->personaje ) ?></i></p>
	<p><b>Fuente</b>: <i><?php echo strtolower( $t->fuente ) ?></i><?php echo (! file_exists(INC.$t->fuente) ) ? '(no existe)' : '' ?></p>
	<?php if( es_admin() ): ?><a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>" class="pull-right btn btn-link"><i class="icon-wrench"></i>&nbsp; Editar parámetros</a><?php endif ?>
	<hr><center><b>Imagen de fondo</b>:</center><br>
	<center><img src="<?php echo url() . IMG . $t->imgbg ?>" title="<?php echo $t->imgbg ?>" alt="<?php echo $t->imgbg ?>" /></center>
	<hr><center><b>Imagen</b>:</center><br>
	<center><img src="<?php echo url() . IMG . $t->img ?>" title="<?php echo $t->img ?>" alt="<?php echo $t->img ?>" /></center><br>
	<?php
break;
	case false:
	default:

	construir( 'cabecera', 'Trackers' );

	$q = new extraer($zerdb->trackers, "*");

	if( ! $q || !$q->nums > 0) {
		agregar_error( sprintf( "No tienes trackers :( <a href=\"%s\">¿agregas uno?</a> :)", url() . '?accion=agregar'), false, false);
		construir( 'pies' );
		exit();
	}

	$pagina = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
	$limit = 'LIMIT '. ($pagina-1) * TRACKERS_PAG . ',' . TRACKERS_PAG;


	$fetch = mysql_query( $q->query . ' ' . $limit );
	$paginas = ceil( $q->nums / TRACKERS_PAG );

	if( isset($_POST['eliminar']) && es_admin() ) {
		if( ! isset($_POST['ids'] ) ) {
			agregar_error("Debes seleccionar un tracker", true, true);
		}elseif( ! is_array($_POST['ids']) ) {
			agregar_error("Debes seleccionar al menos uno", true, true);
		}else{
			$ids = $_POST['ids'];
			agregar_info(
					sprintf('¿Seguro que quieres eliminar %d %s? <hr> <a href="%s" class="btn btn-danger">Sí</a>',
						(int) count($ids), (int) count($ids) == 1 ? 'tracker' : 'trackers',
						url() . '?accion=eliminar&ids=' . implode(',', $ids) )
				, true, false);
		}
	}
?>
	<h3>Trackers</h3><hr><form method="POST"><?php if( es_admin() ) : ?>
	<a href="<?php echo url() ?>index.php?accion=agregar" class="pull-right btn btn-link"><i class="icon-plus"></i>&nbsp;Agregar nuevo</a><br>
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
		<a href="<?php echo url() . 'actualizar.php?id=' . $q->fetch['id'] ?>" class="btn btn-success" title="<?php echo sprintf("Actualizar el tracker de %s", ucwords($q->fetch['personaje'] ) ) ?>"><i class="icon-pencil"></i>&nbsp;Actualizar</a> |
		<a href="<?php echo url() . '?id=' . $q->fetch['id'] ?>" title="<?php echo sprintf('Ver detalles del tracker de %s', ucwords( $q->fetch['personaje']) ); ?>" class="btn btn-primary"><i class="icon-eye-open"></i>&nbsp;Detalles</a>
		<?php if( es_admin() ) : ?>
		| <a href="<?php echo url() . '?accion=editar&id=' . $q->fetch['id'] ?>" title="<?php echo sprintf('Editar el tracker de %s', ucwords($q->fetch['personaje'] ) )?>" class="btn btn-success"><i class="icon-edit"></i>&nbsp;Editar</a>
		<?php endif ?>
		<hr><br>
<?php
	}
	if($pagina > $paginas) {
		agregar_error("No existen más trackers...");
		construir('pies');
		exit();
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
<button type="submit" name="eliminar" class="btn btn-danger pull-right"/><i class="icon-trash"></i>&nbsp;Eliminar seleccionados</button>
<?php endif ?>
</form>
	<?php
	}
break;
}
construir('pies');