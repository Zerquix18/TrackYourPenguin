<?php
/**
* Archivo de actualizaciones :)
*
* @author Zerquix18
* @since 0.1
* @package TrackYourPenguin
*
**/

require_once( dirname(__FILE__) . '/typ-load.php');

if( ! es_super_admin() )
	typ_die( "Wooops! Tranquilo, eh.");

construir( 'cabecera' );

?>
<h3> Actualizaciones </h3>

<?php
if( "POST" == $_SERVER['REQUEST_METHOD'] ) {
	$n = new actualizacion() or die( $n->error );
	exit();
}
if( ! hay_actualizacion() && ! (isset($_POST['actualizar'])) ) {
	agregar_info("No hay actualizaciones. Tienes la última versión.", 0, 1);
}elseif( hay_actualizacion() && ! (isset($_POST['actualizar'])) ) {
	agregar_error( sprintf("TrackYourPenguin <b>%s</b> está disponible. :)", obt_version() ), false, false );
	?>
	<br><div style="text-align:center">
	<form method="POST">
	<button type="submit" class="btn btn-primary btn-large">Actualizar</button>
</form>
</div>
	<?php
}

construir('pies');