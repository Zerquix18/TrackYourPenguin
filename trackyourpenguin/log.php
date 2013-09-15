<?php
/**
*
* Archivo del log
*
* @package TrackYourPenguin
* @author Zerquix18
* @since 0.0.1
*
**/

require_once( dirname(__FILE__) . '/typ-load.php' );

if( ! es_super_admin() )
	typ_die("Haciendo trampa, ¿eh?");
construir('cabecera', 'Registro de cambios', true );
if( isset($_GET['id']) && is_numeric($_GET['id'] ) )
	$dnd = array("id" => $zerdb->proteger($_GET['id'] ) );
else
	$dnd = false;

$l = new extraer( $zerdb->log, "*", $dnd );

if( isset($_GET['id']) && is_numeric($_GET['id'] ) ) {
	if( ! $l || !$l->nums > 0 ) {
		agregar_error("No existe ese ID en el log");
		exit( construir('pies') );
	}
	?>
	<h3> Log ID: <?php echo $l->id ?></h3><hr>
	<div class="well">
		<?php echo $l->accion ?>
	</div>
	<b>Fecha:</b>&nbsp;<i><?php echo $l->fecha ?></i>
	<ul class="pager">
		<li class="previous"><a href="<?php echo url() . 'log.php' ?>">&larr; Volver al log</a></li>
	</ul>
	<?php
	exit( construir('pies') );
}

?>
<h3> Registro de cambios </h3><hr>
<?php
	if( !$l || $l->nums == "0") {
		agregar_error("No se ha registrado nada aún en el registro, vuelve más tarde. :)");
		exit( construir('pies') );
	}

$pagina = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$limit = 'LIMIT '. ($pagina-1) * 5 . ',' . 5;
$fetch = mysql_query( $l->query . ' ' . $limit );
$paginas = ceil( $s->nums / 5 );

if( $pagina > $paginas ) {
	agregar_error("No existen más registros");
	construir( 'pies' );
	exit();
}
?>
<form method="POST" action="<?php echo url( true ) ?>">
	<?php if( isset($_POST['borrar']) ) {
	if( isset($_POST['eliminar']) && !is_array($_POST['eliminar']) or empty($_POST['eliminar']) ) {
		agregar_error("Debes seleccionar un log para borrar");
	}else{
		foreach( $_POST['eliminar'] as $id ) {
			$zerdb->eliminar( $zerdb->log, array("id" => $zerdb->proteger($id) ) );
		}
		agregar_info("Los registros seleccionados han sido eliminados");
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
		<th>Acción</th>
		<th>Fecha</th>
	</tr>
<?php
while($log = mysql_fetch_array($fetch) ) {
	?>
	<tr>
		<td><input type="checkbox" name="eliminar[]" value="<?php echo $log['id'] ?>"></td>
		<td><?php
				if( strlen($log['accion']) > 100 )
					echo substr($log['accion'], 0, 100) . '...<a href="' . url() . 'log.php?id=' . $log['id'] . '">más</a>';
				else echo $log['accion'];
		?></td>
		<td><?php echo $log['fecha'] ?></td>
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
		<i class="icon-trash"></i>&nbsp;Vaciar el log
	</button> |
	<button type="submit" name="borrar" class="btn btn-danger text-right">
		<i class="icon-trash"></i>&nbsp;Limpiar elementos seleccionados
	</button>
</form>

<?php construir('pies') ?>