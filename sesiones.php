<?php
/**
*
* Archivo de las sesiones
*
* Cierra y visualiza las sesiones abiertas
*
* @author Zerquix18
* @link http://trackyourpenguin.com/
* @since 0.1
*
**/

require_once( dirname(__FILE__) . '/typ-load.php');

comprobar( false );

if( ! es_super_admin() )
	$dnd = array("id" => $id = $_SESSION['id']);
elseif( es_super_admin() && isset($_GET['id']) && is_numeric($_GET['id']) )
	$dnd = array("id" => $id = $_GET['id'] ); // don't worry bro, if it's not a number, "is_numeric" will return false. ;)
elseif( es_super_admin() )
	$dnd = null;

construir('cabecera', __('Sesiones'), true );

if( es_super_admin() )
	$id = $_SESSION['id'];

$u = obt_id( $id );
$s = $zerdb->select($zerdb->sesiones, "*", $dnd)->_();

if( isset($_GET['id']) && !$s->nums > 0)
	typ_die( __("No hay sesiones para ese usuario") );

$pagina = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$limit = 'LIMIT '. ($pagina-1) * 5 . ',' . 5;
$fetch = $zerdb->query( $zerdb->query . ' ' . $limit );
$paginas = ceil( $s->nums / 5 );

if( $pagina > $paginas ) {
	agregar_error( __("No existen más sesiones") );
	construir( 'pies' );
	exit(0);
}
?>
<h3><?php _e("Sesiones");
$del = __(' del usuario: ');
if( es_super_admin() && isset($_GET['id']) )
	echo  $del . ucfirst($u->usuario);
?></h3><hr>
<?php if( isset($_POST['borrar']) ) {
	if( isset($_POST['eliminar']) && !is_array($_POST['eliminar']) or empty($_POST['eliminar']) ) {
		agregar_error( __("Debes seleccionar una sesión para cerrar") );
	}else{
		/** It's... time! **/
		foreach( $_POST['eliminar'] as $hash ) {
			$sesion->destruir_hash( $hash );
		}
		agregar_info( __("Las sesiones que seleccionaste han sido eliminadas (de ser existentes)") );
		echo redireccion( url( true ), 2);
	}
}elseif( isset($_POST['eliminar_todo'] ) ) {
	$zerdb->delete($zerdb->sesiones, array("id" => $s->id) )->_();
	echo redireccion( url( true ), 0);
}
?>
<form method="POST" action="<?php echo url( true ) ?>">
<table class="table">
	<tr>
		<th>#</th>
		<th><?php _e("Usuario") ?></th>
		<th><?php _e("Fecha") ?></th>
	</tr>
<?php
$actual = __(" <strong>(sesión actual)</strong>");
while($sesion = $fetch->r->fetch_array() ) {
	?>
	<tr>
		<td><input type="checkbox" name="eliminar[]" value="<?php echo $sesion['hash'] ?>"></td>
		<td><?php
			if( $sesion['id'] !== $_SESSION['id'] && es_super_admin() && ! isset($_GET['id']) )
				echo sprintf('<a href=\'%1$ssesiones.php?id=%3$d\'>%2$s</a>', url(), ucfirst( $u->usuario ), (int) $u->id );
			else
				echo ucfirst( $u->usuario );
		?></td>
		<td><?php echo mostrar_fecha($sesion['fecha']); if( $sesion['hash'] == $_SESSION['hash']) echo $actual ?></td>
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

<?php $este_tu = ($u->id == $id) ? __('tu') : __('este') ?>
	<button type="submit" name="eliminar_todo" class="btn btn-danger text-right">
		<i class="icon-trash"></i>&nbsp;<?php echo sprintf( __("Cerrar todas las sesiones de %s usuario"), $este_tu) ?>
	</button> |
	<button type="submit" name="borrar" class="btn btn-danger text-right">
		<i class="icon-trash"></i>&nbsp;<?php _e("Cerrar sesiones seleccionadas") ?>
	</button>
</form>
<?php 
construir( 'pies' );