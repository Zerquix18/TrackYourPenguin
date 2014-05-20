<?php
/**
* Archivo de actualizaciones :)
*
* @author Zerquix18
* @since 0.1
* @package TrackYourPenguin
*
**/

require ( dirname(__FILE__) . '/typ-load.php');

comprobar( false );

if( ! es_super_admin() )
	typ_die( __("Wooops! Tranquilo, eh.") );

construir( 'cabecera' );

?>
<h3> <?php _e('Actualizaciones') ?> </h3>

<?php
if( hay_actualizacion() && isset($_GET['actualizar']) ) {
	echo __("Preparando actualización...<br><hr>");
	dormir(1);
	$n = new actualizacion();
	exit( construir('pies') );
}
if( ! hay_actualizacion() && ! isset($_GET['actualizar']) ) {
	agregar_info( __("No hay actualizaciones. Tienes la última versión."), 0, 1);
}elseif( hay_actualizacion() && ! isset($_GET['actualizar']) ) {
	agregar_error( sprintf( __("TrackYourPenguin <b>%s</b> está disponible. :)"), obt_version() ), false, false );
	?>
	<br><div style="text-align:center">
	<a href="<?php echo url( true ) . '?actualizar=1' ?>" class="btn btn-primary btn-large"><?php _e('Actualizar') ?></a>
</form>
</div>
	<?php
}
construir('pies');