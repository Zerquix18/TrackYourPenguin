<?php
/**
*
* Pone el cuerpo del documento.
*
* @author Zerquix18 <http://www.zerquix18.com/>
* @since 0.0.1
* @package TrackYourPenguin
*
**/
/**
*
* Comprueba si los buscadores están activados
*
* @return bool
*
**/

function buscadores_activados() {
	global $zerdb;
	$q = new extraer($zerdb->config, "robots");
	switch($q->robots) {
		case "1":
		return true;
		break;
		case "0":
		default:
		return false;
	}
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
	$t = new extraer($zerdb->trackers, "*");
	$trackers = mysql_query($t->query);
	switch($a_construir) {
		case "cabecera":
		?>
		<!DOCTYPE html>
		<html lang="es">
		<head>
			<title><?php if(!empty($titulo)) echo $titulo . ' - '; echo nombre() ?></title>
			<link rel="stylesheet" href="<?php echo url() . INC . CSS . 'bootstrap.css' ?>" />
			<link rel="stylesheet" href="<?php echo url() . INC . CSS . 'bootstrap.min.css' ?>" />
			<meta name="title" content="<?php if(!empty($titulo)) echo $titulo . ' - '; echo titulo() ?>" />
		    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php if( buscadores_activados() ) : ?>
		    <meta name="robots" content="index, follow">
		<?php else: ?>
			<meta name="robots" content="noindex, nofollow">
		<?php endif ?>
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
          			<i class="icon-plus"></i>&nbsp;Extra <b class="caret"></b>
          		</a>
				<ul class="dropdown-menu">
					<li><a href="<?php echo url() ?>acceso.php?salir=1"><i class="icon-off"></i>&nbsp;Cerrar sesión</a></li>
				</ul>
			</div>
            <ul class="nav">
              <li <?php if(es('index.php')) echo 'class="active"' ?>><a href="<?php echo url() ?>index.php">Inicio</a></li>
		              <li class="dropdown">
		              	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Trackers <b class="caret"></b></a>
		              	<ul class="dropdown-menu">
		              <?php
		              while($t->fetch = mysql_fetch_array($trackers) ) {
		              	$tracker_nombre = $t->fetch['personaje'];
		              	$id = $t->fetch['id'];
		              	$class = isset($_GET['id']) && $_GET['id'] == $id ? 'class="active"' : '';
		              	echo sprintf('<li %3$s><a href="%s">%s</a><li>', url() . 'actualizar.php?id=' . $id, $tracker_nombre, $class);
		              }
		              ?>
		           <?php if( es_admin() ) : ?>
		               <li><a href="<?php echo url() ?>?accion=agregar">Agregar nuevo</a></li>
		           <?php endif ?>
		          	   </ul>
	          	</li>
	          <?php if( es('actualizar.php') ) : ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Herramientas <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li>
							<a data-toggle="modal" href="#ver" role="button">
								<i class="icon-eye-open"></i>&nbsp;Ver imagen
							</a>
						</li>
						<li>
							<a data-toggle="modal" href="#herramientas" role="button">
								<i class="icon-share"></i>&nbsp;Compartir
							</a>
						</li>
						<?php if( es_admin() ) : ?>
						<li>
							<a href="<?php echo url() . 'parametros.php?id=' . $t->id ?>">
								<i class="icon-wrench"></i>&nbsp;Editar parámetros
							</a>
						</li>
						<?php endif; ?>
					</ul>
				</li>
			<?php elseif( es('parametros.php') && isset($_GET['posicion']) ) : ?>
			<li><a href="#obtparams" data-toggle="modal" role="button">Obtener parámetros</a></li>
			<?php endif ?>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <?php actualizaciones()  ?>
    <div class="container-fluid">
      <div class="row-fluid">
		<?php if( $barra ) : ?>
        <div class="span3">
          <div class="well sidebar-nav redo">
            <ul class="nav nav-list">
            	<li class="nav-header">
				<li <?php if( es('index.php' )) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>index.php"><i class="icon-search"></i>&nbsp;Trackers</a>
				</li>
				<?php if( es_admin() ) : ?>
				<li <?php if( es('usuarios.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>usuarios.php"><i class="icon-user"></i>&nbsp;Usuarios</a>
				</li>
				<?php if( es_super_admin() ) : ?>
				<li <?php if( es('ajustes.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>ajustes.php"><i class="icon-cog"></i>&nbsp;Ajustes</a>
				</li>
				<?php endif; ?>
				<li <?php if( es('oauth.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>oauth.php"><i class="icon-flag"></i>&nbsp;OAuth</a>
				</li>
				<li <?php if( es('tweets.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>tweets.php"><i class="icon-pencil"></i>&nbsp;Tweets</a>
				</li>
				<?php if( es_super_admin() ) : ?>
				<li <?php if( es('log.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>log.php"><i class="icon-list"></i>&nbsp;Registro</a>
				</li>
				<?php endif ?>
				<?php endif ?>	
				<li <?php if( es('sesiones.php') ) echo 'class="active"' ?>>
					<a href="<?php echo url() ?>sesiones.php"><i class="icon-lock"></i>&nbsp;Sesiones</a>
				</li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
    <?php endif; if( ! es('actualizar.php')) : ?>
        <div class="span9">
          <div class="hero-unit">
		<?php
		endif;
		break;
		case "pies":
		?>
		<?php if( ! es('actualizar.php')) : ?>
			</div>
		</div>
	<?php endif ?>
	</div>
				<footer><a href="http://trackyourpenguin.com/">&copy; TrackYourPenguin <b><?php echo $v ?></b></a></footer>
</div>
		<?php
		$js = array("jquery", "html5", "alerta", "dropdown", "modal");
		foreach($js as $a)
			echo "\n" . '<script type="text/javascript" language="javascript" src="' . url() . INC . JS . $a . '.js"></script>' .
		"\n";
		echo "</body>\n</html>";
	}
}