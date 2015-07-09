<?php
/**
*
*
* @author Zerquix18
* @package TrackYourPenguin
* @link http://github.com/Zerquix18/TrackYourPenguin
*
**/
require_once( dirname(__FILE__) . '/typ-load.php' );
comprobar( false );

construir( 'cabecera', __('About'), false );
?>
<h2><?php _e('About TrackYourPenguin') ?></h2>
<hr>
<?php
echo sprintf(
		__('Welcome to <strong>TrackYourPenguin %s</strong>. TrackYourPenguin (<i>shorten like TYP</i>) is a trackers system coded in PHP and MySQL, currently developed by <a href="%s" target"_blank">Zerquix18</a>. The goal is that you can track using multiple trackers, tweets, users and other features without having the need to code them or even touch code. All in one. :)'), constant('VERSION'), 'http://github.com/zerquix18'
	);
$changelogURL = array(
		"es_ES" => "2VAJ53dg",
		"en_US" => "UNiykW7w"
	);
$content = @file_get_contents("http://pastebin.com/raw.php?i=" . $changelogURL[ obt_lenguaje() ] );
$vv = str_replace('.', '\.', $v);
$x = @preg_match("/(--$vv--)[.A-Za-z0-9\(\)\*\s\&\pL]*(--\/$vv--)/iu", $content, $matches);
if( $content && $x ):
$text = $matches[0];
$text = str_replace( array("--$v--", "\n--/$v--"), "", $text);
$text = explode("\n", $text);
array_shift($text);
?>
<h3><?php _e("News of the version"); ?>&nbsp;<strong><?php echo $v ?></strong></h3>
<hr>
<ul>
	<?php foreach($text as $a => $b)
		echo '<li>'. $b . '</li>';
	?>
</ul>
<?php else:
	agregar_error( __("Unable to check the changelog") );
endif;
?>
<h3><?php _e('Credits') ?></h3>
<ul>
	<li>
		<strong>Bootstrap</strong>. <i><?php _e('The whole design and javascript plugins') ?></i>.</li>
		<li><strong>Bootswatch</strong>. <i><?php _e('Aditional bootstrap themes') ?></i>.</li>
		<li><strong>Danilo Segan</strong>. <i><?php _e('Creator of i18n') ?></i></li>
		<li><strong>Abraham</strong>. <i><?php _e('Twitter libraries') ?></i></li>
		<li><strong>Zerquix18</strong>. <i><?php _e('Everything else you can see here! :)') ?></i></li>
</ul>
<?php construir('pies');