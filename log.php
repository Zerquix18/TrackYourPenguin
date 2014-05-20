<?php
/**
*
* Archivo del log
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.1
*
**/

require_once( dirname(__FILE__) . '/typ-load.php' );

comprobar( false );

if( ! es_super_admin() )
	typ_die("Haciendo trampa, ¿eh?");
construir('cabecera', __('Registro de cambios'), true );

if( isset($_GET['id']) && is_numeric($_GET['id'] ) )
	$dnd = array("id" => $zerdb->proteger($_GET['id'] ) );
else
	$dnd = false;

$order = isset($_GET['id']) && is_numeric($_GET['id']) ? '' : "ORDER BY id DESC";
$l = new extraer( $zerdb->log, "*", $dnd, $order );

if( isset($_GET['id']) && is_numeric($_GET['id'] ) ) {
	if( ! $l || !$l->nums > 0 ) {
		agregar_error( __("No existe ese ID en el log") );
		exit( construir('pies') );
	}
	?>
	<h3> <?php _e("Log ID") ?>: <?php echo $l->id ?></h3><hr>
	<div class="well">
		<?php echo mostrar_log($l->accion) ?>
	</div>
	<b><?php _e("Fecha") ?>:</b>&nbsp;<i><?php echo mostrar_fecha($l->fecha, true) ?></i>
	<ul class="pager">
		<li class="previous"><a href="<?php echo url() . 'log.php' ?>">&larr; <?php _e("Volver al log") ?></a></li>
	</ul>
	<?php
	exit( construir('pies') );
}
?>
<h3> <?php _e("Registro de cambios") ?> </h3><hr>
<?php
	if( !$l || $l->nums == "0") {
		agregar_error( __("No se ha registrado nada aún en el registro, vuelve más tarde. :)") );
		exit( construir('pies') );
	}
	$pagina = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
	$limit = 'LIMIT '. ($pagina-1) * 5 . ',' . 5;
	$fetch = mysql_query( $l->query . ' ' . $limit );
	$paginas = ceil( $l->nums / 5 );
if( $pagina > $paginas ) {
	agregar_error( __("No existen más registros") );
	construir( 'pies' );
	exit();
}
?>
<form method="POST" action="<?php echo url( true ) ?>">
	<?php if( isset($_POST['borrar']) ) {
	if( isset($_POST['eliminar']) && !is_array($_POST['eliminar']) or empty($_POST['eliminar']) ) {
		agregar_error( __("Debes seleccionar un log para borrar") );
	}else{
		foreach( $_POST['eliminar'] as $id ) {
			$zerdb->eliminar( $zerdb->log, array("id" => $zerdb->proteger($id) ) );
		}
		agregar_info( __("Los registros seleccionados han sido eliminados") );
		echo redireccion( url( true ), 2);
	}
}elseif( isset($_POST['eliminar_todo'] ) ) {
	$zerdb->eliminar($zerdb->log );
	echo redireccion( url( true ), 0);
}
?>
<table class="table">
	<tr>
		<th>#</th>
		<th><?php _e("Acción") ?></th>
		<th><?php _e("Fecha") ?></th>
	</tr>
<?php
while($log = mysql_fetch_array($fetch) ) {
	?>
	<tr>
		<td><input type="checkbox" name="eliminar[]" value="<?php echo $log['id'] ?>"></td>
		<td><?php
				if( strlen($log['accion']) > 100 )
					echo substr( mostrar_log($log['accion']), 0, 100) . '...<a href="' . url() . 'log.php?id=' . $log['id'] . '">' . __('más') . '</a>';
				else echo mostrar_log($log['accion']);
		?></td>
		<td><?php echo '<b>' . mostrar_fecha($log['fecha'])  . '</b>' ?></td>
	</tr>
<?php } ?>
</table>
<hr>
<div class="pagination pagination-large text-center">
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
	<button type="submit" name="eliminar_todo" class="btn btn-danger text-right">
		<i class="icon-trash"></i>&nbsp;<?php _e("Vaciar el log") ?>
	</button> |
	<button type="submit" name="borrar" class="btn btn-danger text-right">
		<i class="icon-trash"></i>&nbsp;<?php _e("Limpiar elementos seleccionados") ?>
	</button>
</form>

<?php construir('pies') ?>