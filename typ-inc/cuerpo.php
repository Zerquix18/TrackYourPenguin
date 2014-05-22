<?php
/**
*
* Pone el cuerpo del documento.
*
* @author Zerquix18 <http://www.zerquix18.com/>
* @since 0.1
* @package TrackYourPenguin
*
**/

/**
*
* Obtiene el tema
* @return string
**/
function tema() {
	global $zerdb;
	$t = $zerdb->select($zerdb->config, "extra")->_();
	$ext = json_decode($t->extra)->tema;
	return !empty($ext) ? $ext : 'bootstrap';
}

/**
*
* Construye el documento.
*
* @param $a_construir string
* @param $titulo string
* @param $barra bool
*
**/

function construir( $a_construir, $titulo = "", $barra = true ) {
	global $zerdb, $v;
	$a_construir = strtolower($a_construir);
	$t = $zerdb->select($zerdb->trackers, "*")->_();
	switch($a_construir) {
		case "cabecera":
		?>
		<!DOCTYPE html>
		<html lang="es">
		<head>
			<title><?php if(!empty($titulo)) echo $titulo . ' - '; echo nombre() ?></title>
			<link rel="stylesheet" id="estilo" href="<?php echo url() . INC . CSS . tema() . '.css' ?>" />
			<link rel="stylesheet" id="estilo_r" href="<?php echo url() . INC . CSS . tema() . '.min.css' ?>" />
			<link rel="stylesheet" href="<?php echo url() . INC . CSS . 'typ.css' ?>" />
			<meta charset="utf-8">
			<meta name="robots" content="noindex, nofollow">
		    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<style type="text/css">
			body{
				padding: 60px;
			}
			.redo {
				border-radius: 15px;
			}
			</style>
		</head>
	<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="<?php echo url() ?>"><?php echo nombre() ?></a>
          <div class="nav-collapse collapse">
          	<div class="btn-group pull-right">
          		<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-primary">
          			<i class="icon-plus"></i>&nbsp;<?php _e("Extra") ?> <b class="caret"></b>
          		</a>
				<ul class="dropdown-menu">
					<li><a href="<?php echo url() ?>about.php"><i class="icon-question-sign"></i>&nbsp;<?php _e('Acerca de') ?></a></li>
					<li><a href="<?php echo url() ?>acceso.php?salir=1"><i class="icon-off"></i>&nbsp;<?php _e("Cerrar sesión") ?></a></li>
				</ul>
			</div>
            <ul class="nav">
              <li <?php if(es('index.php')) echo 'class="active"' ?>><a href="<?php echo url() ?>index.php"><?php _e('Inicio') ?></a></li>
              <?php if( $t->nums > 0 || es_admin() ): ?>
		              <li class="dropdown">
		              	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _e("Trackers") ?> <b class="caret"></b></a>
		              	<ul class="dropdown-menu">
		              <?php
		              while($r = $t->r->fetch_object() ) {
		              	$class = isset($_GET['id']) && $_GET['id'] == $r->id ? ' class="active"' : '';
		              	echo sprintf('<li%3$s><a href="%s">%s</a><li>', url() . 'actualizar.php?id=' . $r->id, $r->personaje, $class);
		              }
		              ?>
		           <?php if( es_admin() ) : ?>
		               <li><a href="<?php echo url() ?>?accion=agregar"><?php _e("Agregar nuevo") ?></a></li>
		           <?php endif ?>
		          	   </ul>
	          	</li>
	          <?php endif ?>
	          <?php if( es('actualizar.php') ) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _e("Herramientas") ?> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li>
							<a data-toggle="modal" href="#ver" role="button">
								<i class="icon-eye-open"></i>&nbsp;<?php _e("Ver imagen") ?>
							</a>
						</li>
						<?php if( 'localhost' !== $_SERVER['HTTP_HOST'] ) : ?>
						<li>
							<a data-toggle="modal" href="#herramientas" role="button">
								<i class="icon-share"></i>&nbsp;<?php _e("Compartir") ?>
							</a>
						</li>
						<?php endif; if( es_admin() ) : ?>
						<li>
							<a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>">
								<i class="icon-wrench"></i>&nbsp;<?php _e("Editar parámetros") ?>
							</a>
						</li>
						<?php endif; if( es('actualizar.php') && oauth_configurado() ) : ?>
						<li>
							<a data-toggle="modal" href="#cc" role="button">
								<i class="icon-question-sign"></i>&nbsp;<?php _e("Códigos cortos") ?>
							</a>
						</li>
						<?php endif ?>
					</ul>
				</li>
			<?php elseif( es('parametros.php') && isset($_GET['posicion']) ) : ?>
			<li><a href="#obtparams" data-toggle="modal" role="button"><?php _e("Obtener parámetros") ?></a></li>
			<?php elseif( es('tweets.php') ): ?>
			<li><a href="#cc" data-toggle="modal" role="button"><?php _e('Códigos cortos') ?></a></li>
			<?php endif ?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <?php actualizaciones()  ?>
<noscript><?php agregar_error( __('Javascript está desactivado, más de la mitad de las funciones del sitio están destinadas a fallar...') ) ?></noscript>
    <div class="container-fluid">
      <div class="row-fluid">
		<?php if( $barra ) : ?>
        <div class="span3">
          <div class="well sidebar-nav redo">
            <ul class="nav nav-list">
            	<li class="nav-header">
				<li <?php if( es('index.php' )) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>index.php"><i class="icon-search"></i>&nbsp;<?php _e("Trackers") ?></a>
				</li>
				<li <?php if( es('usuarios.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>usuarios.php"><i class="icon-user"></i>&nbsp;<?php _e("Usuarios") ?></a>
				</li>
				<?php if( es_super_admin() ) : ?>
				<li <?php if( es('ajustes.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>ajustes.php"><i class="icon-cog"></i>&nbsp;<?php _e("Ajustes") ?></a>
				</li>
				<?php endif; ?>
				<?php if( es_admin() ) : ?>
				<li <?php if( es('oauth.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>oauth.php"><i class="icon-flag"></i>&nbsp;<?php _e("OAuth") ?></a>
				</li>
				<li <?php if( es('tweets.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>tweets.php"><i class="icon-pencil"></i>&nbsp;<?php _e("Tweets") ?></a>
				</li>
				<?php if( es_super_admin() ) : ?>
				<li <?php if( es('log.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>log.php"><i class="icon-list"></i>&nbsp;<?php _e("Registro") ?></a>
				</li>
				<li <?php if( es('actualizaciones.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>actualizaciones.php">
						<i class="icon-globe"></i>&nbsp;<?php _e('Actualizaciones'); if( hay_actualizacion() ) 
						echo '&nbsp;<span class="badge badge-important">1</span>' ?>
					</a>
				</li>
				<?php endif ?>
				<?php endif ?>	
				<li <?php if( es('sesiones.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>sesiones.php"><i class="icon-lock"></i>&nbsp;<?php _e("Sesiones") ?></a>
				</li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
    <?php endif; if( ! es('actualizar.php') ) : ?>
        <div class="span9">
          <div class="hero-unit">
		<?php
		endif;
		break;
		case "pies":
		?>
		<?php if( ! es('actualizar.php') ) : ?>
			</div>
		</div>
	<?php endif ?>
	<div id="typ" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"></h3>
  </div>
  <div class="modal-body">
    <p>One fine body… :)</p>
  </div>
  <div class="modal-footer">
  </div>
</div>
<script type="text/javascript">var uri = "<?php echo url(false) ?>", _tema = "<?php echo tema() ?>";</script>
	</div><hr>
				<footer><a target="_blank" href="http://trackyourpenguin.com/">&copy; TrackYourPenguin <strong><?php echo $v ?></strong></a></footer></div>
		<?php
		$js = array("jquery", "html5", "alerta", "dropdown", "modal", "tooltip", "typ");
		foreach($js as $a)
			echo "\n" . '<script type="text/javascript" language="javascript" src="' . url() . INC . JS . $a . '.js"></script>' .
		"\n";
		echo "</body>\n</html>";
	}
}