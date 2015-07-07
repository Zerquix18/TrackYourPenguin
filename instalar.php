<?php
/**
* Archivo de instalación de TrackYourPenguin
*
* @author Zerquix18
* @link http://trackyourpenguin.com/
* @since 0.1.0
*
**/
require_once( dirname(__FILE__) . "/typ-load.php"); // Requerimos todo.

if( version_compare(PHP_VERSION, '5.2.0', '<' ) )
  typ_die( __("I'm sorry! TrackYourPenguin needs a PHP version greater than 5.2.0 :(") );
if( file_exists( PATH . 'README.md') )
  unlink( PATH . 'README.md');

$paso = (isset($_GET['paso']) && is_string($_GET['paso'])) ? $_GET['paso'] : ''; // El paso por el que vamos.
/* Construye la cabecera */
function construir_cabecera() {
	?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <title><?php _e("Installation") ?> - TrackYourPenguin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="<?php echo  INC . CSS . 'cyborg.css' ?>" rel="stylesheet">
  <style type="text/css">
    body { padding-top: 60px;
      padding-bottom: 40px;
    }
  </style>
  <link href="<?php echo INC . CSS . 'cyborg.min.css' ?>" rel="stylesheet">
  <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body>
  <div class="container">
 	<div class="hero-unit">
	<?php
}
function construir_pies() { global $v;
	?>
</div>
	    <hr>
    <footer>
      <p>&copy; <a href="//trackyourpenguin.com" target="_blank">TrackYourPenguin, <?php _e("version") ?>&nbsp;<strong><?php echo $v ?></strong></a></p>
    </footer>

  </div>
</body>
</html>
	<?php
}

function error_renombrar() {
  echo __('There was an error when renaming the file. It seems like your hosting does not allow it. Please rename "<strong>typ-config-sample.php</strong>" to "<strong>typ-config.php</strong>"');
}

/* ¿Ya hemos instalado */
function comp() {
  if( comprobar_instalacion() ) {
    agregar_error( __("<h2><strong>You already installated.</strong></h2><hr> TrackYourPenguin was already installed, and there's not need to install it again."), false, false);
    construir_pies();
    exit();
  }
}
construir_cabecera();
switch( $paso ) {
	case "1":
	comp();
	$file = 'typ-config-sample.php';
	$file2 = 'typ-config.php';
	if( !file_exists($file) && !file_exists($file2) )
		typ_die( __("Missing both files: typ-config.php and typ-config-sample.php") );
	if( !file_exists( PATH . $file2 ) ) :  //si no existe typ-config.php
		require_once("./" . $file);
		$test = new zerdb(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
		if( $test->ready ) :
			if( @rename( PATH . $file, PATH . $file2) )
				header("Location: " . url() . 'instalar.php?paso=2');
			else
    				echo agregar_error( error_renombrar() );
		else:
			$post = "POST" == getenv('REQUEST_METHOD');
			if( $post ) {
                    if( comprobar_args($_POST['language']) && $_POST['language'] !== TYP_LANG )
                      actualizar_lenguaje( $_POST['language'] );
				$test = new zerdb( @$_POST['host'], @$_POST['usuario'], @$_POST['clave'], @$_POST['db']);
				if( $test && $test->ready ) {
					$data = file_get_contents($file);
					$reemplazar = array(
					"dbhost" => trim($_POST['host']),
					"dbname" => trim($_POST['db']),
					"dbuser" => trim($_POST['usuario']),
					"dbpass" => trim($_POST['clave'])
					);
					$actualizar = str_replace( array_keys($reemplazar), array_values($reemplazar), $data); // aquí el nuevo archivo (typ-config.php)
					if( false == ($f = @fopen($file, "w") ) ):
						echo agregar_error( sprintf( __("Can't open file :/, please replace <strong>typ-config-sample.php</strong> for the code below and rename it to <strong>typ-config.php</strong>") ) );
						echo '<br><br><textarea rows="25" readonly="readonly" style="width:100%">' . $actualizar . '</textarea>';
						construir_pies();
						exit;
					else:
						$write = @fwrite($f, $actualizar);
						$renombrar = @rename( PATH . $file, PATH . $file2);
						fclose($f);
						if( ! $write ) :
							echo agregar_error( __("Can't rewrite file :/, please replace <strong>typ-config-sample.php</strong> by the code below and rename it to <strong>typ-config.php</strong>, or, or if it already exists <strong>typ-config.php</strong>, update it with this:") . '<br><br><textarea rows="25" readonly="readonly">' . $actualizar . '</textarea>');
							construir_pies();
							exit;
						elseif( ! $renombrar ) :
							agregar_error( error_renombrar() );
						else:
							exit( header("Location: instalar.php?paso=2") );
					endif;
				endif;
			}else{
				agregar_error( sprintf( __("MySQL connect failed. Error MySQL: %s "), $test->error ) );
			}
		}
      ?>
        <h2><?php _e("There was an error connecting to the database") ?></h2>
        <p><?php _e("There was an error while connectiong to the database. You can make the connection from here. The data you will type here will be provided by your hosting.") ?></p><br>
        <form class="form-horizontal" method="POST">
          <div class="control-group">
            <label class="control-label">
              <?php _e("Host") ?>
            </label>
            <div class="controls">
              <input type="text" name="host" required="required" <?php if($post) : ?>value="<?php echo $_POST['host'] ?>"<?php endif ?>>
              <span class="help-block"><?php _e("MySQL database host, most of the time it is localhost") ?></span>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label">
              <?php _e("Name") ?>
            </label>
          <div class="controls">
            <input type="text" name="db" required="required" <?php if($post) : ?>value="<?php echo $_POST['db'] ?>"<?php endif ?>>
            <span class="help-block"><?php _e("Database name") ?></span>
          </div>
        </div>
        <div class="control-group">
         <label class="control-label">
            <?php _e("User") ?>
          </label>
        <div class="controls">
          <input type="text" name="usuario" required="required" <?php if($post) : ?>value="<?php echo $_POST['usuario'] ?>"<?php endif ?>>
          <span class="help-block"><?php _e("Database user") ?></span>
        </div>
      </div>
      <div class="control-group">
       <label class="control-label">
      <?php _e("Password") ?>
      </label>
        <div class="controls">
         <input type="text" name="clave" required="required" <?php if($post) : ?>value="<?php echo $_POST['clave'] ?>"<?php endif ?>>
          <span class="help-block"><?php _e("Database password") ?></span>
      </div>
     </div>
      <div class="control-group">
       <label class="control-label">
      <?php _e("Language") ?>
      </label>
        <div class="controls">
        <select name="language">
          <?php foreach($lenguajest as $a => $b): ?>
            <option <?php if($a ==  TYP_LANG ) echo 'selected="selected"' ?> value="<?php echo $a ?>"><?php echo $b ?></option>
          <?php endforeach ?>
        </select>
          <span class="help-block"><?php _e("Language for the site") ?></span>
      </div>
     </div>
     <center><input type="submit" class="btn btn-primary btn-large" value="<?php _e("Connect") ?>"></center>
   </form>
      <?php
    endif;
else: // si existe typ-config.php
  require_once( './' . $file2 );
  if( $zerdb->ready ) :
    exit( header("Location: instalar.php?paso=2") );
  else:
    agregar_error( sprintf( __("<h2>Error establishing a database connection.</h2><p>It can't connect. Please check your <strong>typ-config.php</strong>, and here is the error returned by MySQL: <strong>%s</strong>"), $zerdb->error) );
  endif;
endif;
break;
case "2":
if( ! ( file_exists('./typ-config.php') &&  $zerdb->ready) )
	exit( header("Location: instalar.php?paso=1") );
if( !$zerdb->select($zerdb->usuarios, "*")->_() )
	header("Location: instalar.php" );

comp(); //here we're...
/** TRAIGAN A LAS TABLAAAAAAAAAAAAAAS **/
require_once( PATH . INC . 'esquema.php');
foreach($sql as $a => $b)
	$zerdb->query($b); //inserta las tablas :3

$post = 'POST' == getenv('REQUEST_METHOD');
?>
<h2><?php _e("Welcome to the installation") ?></h2>
<hr>
<?php if( ! $post ) { ?>
<?php _e("With this guide you will be able to install TrackYourPenguin easily, you just have to fill the following fields... :)") ?>
<?php
}else{

	$usuario = @$zerdb->real_escape( strtolower($_POST['usuario']) );
	$clave = md5( @$zerdb->real_escape($_POST['clave']) );
	$email = @$zerdb->real_escape( $_POST['email'] );

	if( ! comprobar_args( @$_POST['usuario'], @$_POST['clave'], @$_POST['email'] ) ) {
		agregar_error( __("Cheatin', uh?!") );
	}elseif( vacios( $_POST['usuario'], $_POST['clave'], $_POST['email'] ) ) {
		agregar_error( __("You can't leave empty fields.") );
	}elseif( ! preg_match('/^[a-z0-9_-]{3,12}$/', $usuario) ) {
		agregar_error( __("The user doesn't look valid. Remember it must have 3-12 characters.") );
	}elseif( strlen($_POST['email']) > 60) {
		agregar_error( __("Are you sure that your email is so long?") );
	}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
		agregar_error( __("The email you typed is too long.") );
	}elseif( $_POST['clave'] !== $_POST['clave2'] ) {
		agregar_error( __("The passwords don't match." ) );
      }elseif( $_POST['dbp'] !== DB_CLAVE ) {
            agregar_error( __("The typed password don't match with the database password.") );
	}else{
		$path = dirname($_SERVER['PHP_SELF']) !== '/' ? dirname($_SERVER['PHP_SELF']) : '';
		$url = 'http://' . $_SERVER['HTTP_HOST'] . $path;
		$insertar = $zerdb->insert($zerdb->usuarios, array($usuario, $clave, $email, 1, 1, '') ) or die( $zerdb->error );
		$insertar2 = $zerdb->insert($zerdb->config, array("TrackYourPenguin", $url, json_encode( array("tema" => "bootstrap") ) ) ) or die( $zerdb->error );
		agregar_info( sprintf( __('<h2> Installation completed </h2><br> You have installed TrackYourPenguin successfully. Now you can <a href="%s">log in</a> to continue. :)'), url() . 'acceso.php' ) );
		construir_pies();
		exit();
	}
}
?>
<hr>
<form method="POST" class="form-horizontal">
  <div class="control-group">
    <label class="control-label">
      <?php _e("User") ?>:
    </label>
    <div class="controls">
      <input type="text" name="usuario" id="usuario" required="required" <?php echo ($post) ? 'value="' . $_POST['usuario'] . '"' : '' ?>>
      <span class="help-block"><?php _e("Your username for this site") ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">
      <?php _e("Password") ?>
    </label>
    <div class="controls">
      <input type="password" name="clave" id="clave" required="required">
      <span class="help-block"><?php _e("Your password to log in") ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">
      <?php _e("Re-type password") ?>
    </label>
    <div class="controls">
      <input type="password" name="clave2" id="clave2" required="required">
      <span class="help-block"><?php _e("Make sure this is the password that you want") ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">
      <?php _e("Email") ?>
    </label>
    <div class="controls">
      <input type="email" name="email" id="email" required="required" <?php echo ($post) ? 'value="' . $_POST['email'] . '"' : '' ?>>
      <span class="help-block"><?php _e("Your email, the one you use the most") ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">
      <?php _e("Database password") ?>
    </label>
    <div class="controls">
      <input type="text" name="dbp" id="dbp" required="required">
      <span class="help-block"><?php _e("Just to be sure") ?></span>
    </div>
  </div>
  <hr>
  <center><input type="submit" name="enviar" value="<?php _e("Send") ?>" class="btn btn-large btn-primary"></center>
<?php
break;
default:
header("Location: instalar.php?paso=1");
}
construir_pies();