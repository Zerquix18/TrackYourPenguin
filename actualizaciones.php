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
	typ_die( __("Wooops! Just take it easy man.") ); // ;)
construir( 'cabecera' );
?>
<h3><?php _e('Updates') ?></h3>
<?php
$ha = hay_actualizacion();
if( $ha && isset($_GET['actualizar']) ) {
	echo __("Getting ready to update...<br><hr>");
	dormir(1);
	$n = new actualizacion();
	exit( construir('pies') );
}
if( ! $ha ) {
	agregar_info( __("There are not updates. You have the last version. :)"), false, true); //0,1
}elseif( $ha && ! isset($_GET['actualizar']) ) {
	agregar_error( sprintf( __("TrackYourPenguin <strong>%s</strong> is available. :)"), obt_version() ), false, false );
	?>
	<br><div style="text-align:center">
	<a href="<?php echo url( true ) . '?actualizar=1' ?>" class="btn btn-primary btn-large"><?php _e('Update') ?></a>
</form>
</div>
	<?php
}  
construir('pies');