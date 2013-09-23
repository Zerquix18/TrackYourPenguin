<?php

require_once( dirname(__FILE__) . '/typ-load.php');

comprobar( false );

if( ! es_super_admin() )
	$dnd = array("id" => $_SESSION['id']);
elseif( es_super_admin() && isset($_GET['id']) && is_numeric($_GET['id']) )
	$dnd = array("id" => $zerdb->proteger($_GET['id'] ) );
elseif( es_super_admin() )
	$dnd = false;

$s = new extraer($zerdb->sesiones, "*", $dnd);

if( isset($_GET['id']) && !$s->nums > 0)
	typ_die("No hay sesiones para ese usuario");

construir('cabecera', 'Sesiones', true );

$u = obt_id( $s->id );

$pagina = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$limit = 'LIMIT '. ($pagina-1) * 5 . ',' . 5;
$fetch = mysql_query( $s->query . ' ' . $limit );
$paginas = ceil( $s->nums / 5 );

if( $pagina > $paginas ) {
	agregar_error("No existen más sesiones");
	construir( 'pies' );
	exit();
}

?>
<h3>Sesiones <?php
if( es_super_admin() && isset($_GET['id']) )
	echo 'del usuario: ' . ucfirst($u->usuario); ?></h3><hr>

<?php if( isset($_POST['borrar']) ) {
	if( isset($_POST['eliminar']) && !is_array($_POST['eliminar']) or empty($_POST['eliminar']) ) {
		agregar_error("Debes seleccionar una sesión para cerrar");
	}else{
		/** It's... time! **/
		foreach( $_POST['eliminar'] as $hash ) {
			$sesion->destruir_hash( $hash );
		}
		agregar_info("Las sesiones que seleccionaste han sido eliminadas (de ser existentes)");
		echo redireccion( url( true ), 2);
	}
}elseif( isset($_POST['eliminar_todo'] ) ) {
	$zerdb->eliminar($zerdb->sesiones, array("id" => $s->id) );
	echo redireccion( url( true ), 0);
}
?>
<form method="POST" action="<?php echo url( true ) ?>">
<table class="table">
	<tr>
		<th>#</th>
		<th>Usuario</th>
		<th>Fecha</th>
	</tr>
<?php
while($sesion = mysql_fetch_array($fetch) ) {
	?>
	<tr>
		<td><input type="checkbox" name="eliminar[]" value="<?php echo $sesion['hash'] ?>"></td>
		<td><?php echo ucfirst($u->usuario) ?></td>
		<td><?php echo $sesion['fecha']; if( $sesion['hash'] == $_SESSION['hash']) echo ' <b>(sesión actual)</b>' ?></td>
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

<?php $este_tu = ($u->id == $s->id) ? 'tu' : 'este' ?>
	<button type="submit" name="eliminar_todo" class="btn btn-danger text-right">
		<i class="icon-trash"></i>&nbsp;Cerrar todas las sesiones de <?php echo $este_tu ?> usuario
	</button> |
	<button type="submit" name="borrar" class="btn btn-danger text-right">
		<i class="icon-trash"></i>&nbsp;Cerrar sesiones seleccionadas
	</button>
</form>
<?php 
construir( 'pies' );