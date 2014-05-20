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
$paso = (isset($_GET['paso']) && is_string($_GET['paso'])) ? $_GET['paso'] : ''; // El paso por el que vamos.
/* Construye la cabecera */
function construir_cabecera() {
	?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <title><?php _e("Instalación") ?> - TrackYourPenguin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="<?php echo  INC . CSS . 'bootstrap2.css' ?>" rel="stylesheet">
  <style type="text/css">
    body { padding-top: 60px; 
      padding-bottom: 40px; 
    }
  </style>
  <link href="<?php echo INC . CSS . 'bootstrap2.min.css' ?>" rel="stylesheet">
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
      <p>&copy; <a href="//trackyourpenguin.com" target="_blank">TrackYourPenguin, <?php _e("versión") ?>&nbsp;<b><?php echo $v ?></b></a></p>
    </footer>

  </div> 
</body>
</html>
	<?php
}

function error_renombrar() {
  echo __('Hubo un error renombrando el archivo, al parecer tu hosting no lo permite. Por favor, renómbralo de "<b>typ-config-sample.php</b>"  a "<b>typ-config.php</b>"');
}

/* ¿Ya hemos instalado */
function comp() {
  if( comprobar_instalacion() ) {
    agregar_error( __("<h2><b>Ya has instalado</b></h2><hr> La instalación de TrackYourPenguin ya fue completada, por lo que, ¿no piensas hacerla de nuevo, verdad?"), false, false);
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
	if( !file_exists( PATH . $file2 ) ) :  //si no existe typ-config.php
    require_once("./" . $file);
  $test = new zerdb(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
  if( $test->listo ) :
    if( @rename( PATH . $file, PATH . $file2) )
      header("Location: " . url() . 'instalar.php?paso=2');
    else
      echo agregar_error(error_renombrar());
    else:
      $post = "POST" == getenv('REQUEST_METHOD');
      if( $post ) {
        $test = new zerdb( @$_POST['host'], @$_POST['usuario'], @$_POST['clave'], @$_POST['db']);
        if( $test && $test->listo ) {
          $data = file_get_contents($file);
          $reemplazar = array(
            "defineaquituhost" => trim($_POST['host']),
            "defineaquielnombre" => trim($_POST['db']),
            "defineaquituusuario" => trim($_POST['usuario']),
            "defineaquituclave" => trim($_POST['clave'])
            );
          $actualizar = str_replace( array_keys($reemplazar), array_values($reemplazar), $data); // aquí el nuevo archivo (typ-config.php)
          if( !@fopen($file, "w") ) :
            agregar_error( sprintf( __("No se puede abrir el archivo :/, por favor copia este archivo y reemplázalo por tu <b>typ-config-sample.php</b>
              y cambia el nombre a <b>typ-config.php</b>") ) );
          echo '<br><br><textarea rows="25" readonly="readonly">' . $actualizar . '</textarea>';
          construir_pies(); exit();
          else:
            $f = fopen(PATH . $file, "w");
            $write = @fwrite($f, $actualizar);
            $renombrar = @rename( PATH . $file, PATH . $file2);
            fclose($f);
            if( ! $write ) :
              echo agregar_error( __("No se puede reescribir el archivo :/, por favor copia este archivo y reemplázalo por tu <b>typ-config-sample.php</b> y cambia el nombre a <b>typ-config.php</b>, ó, si ya existe <b>typ-config.php</b>, actualizálo con esto:") . '<br><br><textarea rows="25" readonly="readonly">' . $actualizar . '</textarea>');
              construir_pies() . exit(0);
            elseif( ! $renombrar ) :
              agregar_error( error_renombrar() );
            else: 
              exit( header("Location: instalar.php?paso=2") );
            endif;
          endif;
        }else{
          agregar_error( sprintf( __("La conexión no está bien hecha... Error MySQL: %s "), mysql_error() ) );
        }
      }
      ?>
        <h2><?php _e("Error conectando a la base de datos") ?></h2>
        <p><?php _e("Hubo un error conectando a la base de datos, por favor, conecta desde aquí. Los datos te los dará tu proveedor de hosting.") ?></p><br>
        <form class="form-horizontal" method="POST">
          <div class="control-group">
            <label class="control-label">
              <?php _e("Host") ?>
            </label>
            <div class="controls">
              <input type="text" name="host" required="required" <?php if($post) : ?>value="<?php echo $_POST['host'] ?>"<?php endif ?>>
              <span class="help-block"><?php _e("El host de la base de datos MySQL, a veces suele ser localhost") ?></span>
            </div>
          </div>
          <div class="control-group">
            <label class="control-label">
              <?php _e("Nombre") ?>
            </label>
          <div class="controls">
            <input type="text" name="db" required="required" <?php if($post) : ?>value="<?php echo $_POST['db'] ?>"<?php endif ?>>
            <span class="help-block"><?php _e("Nombre de la base de datos") ?></span>
          </div>
        </div>
        <div class="control-group">
         <label class="control-label">
            <?php _e("Usuario") ?>
          </label>
        <div class="controls">
          <input type="text" name="usuario" required="required" <?php if($post) : ?>value="<?php echo $_POST['usuario'] ?>"<?php endif ?>>
          <span class="help-block"><?php _e("Usuario de la base de datos") ?></span>
        </div>
      </div>
      <div class="control-group">
       <label class="control-label">
      <?php _e("Clave") ?>
      </label>
        <div class="controls">
         <input type="text" name="clave" required="required" <?php if($post) : ?>value="<?php echo $_POST['clave'] ?>"<?php endif ?>>
          <span class="help-block"><?php _e("Clave de la base de datos") ?></span>
      </div>
     </div>
     <center><input type="submit" class="btn btn-primary btn-large" value="<?php _e("Conectar") ?>"></center>
   </form>
      <?php
    endif;
else: // si existe typ-config.php
  require_once( './' . $file2 );
  if( $zerdb->listo ) :
    exit( header("Location: instalar.php?paso=2") );
  else:
    agregar_error( sprintf( __("<h2>Error estableciendo conexión con la base de datos</h2><p>Al parecer, no se puede conectar, por favor revisa tu <b>typ-config.php</b>, y he aquí el error devuelto por MySQL: <b>%s</b>"), $zerdb->ult_err) );
  endif;
endif;
break;
case "2":
if( ! ( file_exists('./typ-config.php') &&  $zerdb->listo) )
  exit( header("Location: instalar.php?paso=1") );
if( !@new extraer($zerdb->usuarios, "*") ) 
  header("Location: instalar.php" );
comp(); //here we're...
/** TRAIGAN A LAS TABLAAAAAAAAAAAAAAS **/
require_once( PATH . INC . 'esquema.php');
foreach($sql as $a => $b)
  $zerdb->query($b); //inserta las tablas :3
$post = 'POST' == getenv('REQUEST_METHOD');
?>
<h2><?php _e("Bienvenido a la instalación") ?></h2>
<hr>
<?php if( ! $post ) { ?>
<?php _e("Desde esta guía podrás instalar TrackYourPenguin fácilmente, sólo tienes que llenar los siguientes campos... :)") ?>
<?php 
}else{

  $usuario = @$zerdb->proteger( strtolower($_POST['usuario']) );
  $clave = md5( @$zerdb->proteger($_POST['clave']) );
  $email = @$zerdb->proteger( $_POST['email'] );

  if( ! comprobar_args( @$_POST['usuario'], @$_POST['clave'], @$_POST['email'] ) ) {
    agregar_error( __("Haciendo trampa, ¿eh?") );
  }elseif( vacios( $_POST['usuario'], $_POST['clave'], $_POST['email'] ) ) {
    agregar_error( __("No puedes dejar datos vacíos") );
  }elseif( ! preg_match('/^[a-z0-9_-\S]{3,12}$/', $usuario) ) {
    agregar_error( __("El usuario no parece ser válido. Recuerda que debe ser de 3 a 12 caracteres.") );
  }elseif( strlen($_POST['email']) > 60) {
    agregar_error( __("¿Seguro que tienes un email tan largo?") );
  }elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
    agregar_error( __("El email que ingresas no parece ser válido") );
  }elseif( $_POST['clave'] !== $_POST['clave2'] ) {
    agregar_error( __("Las claves no coinciden" ) );
  }else{
  $path = dirname($_SERVER['PHP_SELF']) !== '/' ? dirname($_SERVER['PHP_SELF']) : '';
  $url = 'http://' . $_SERVER['HTTP_HOST'] . $path;
  $insertar = $zerdb->insertar($zerdb->usuarios, array($usuario, $clave, $email, 1, 1, '') ) or die( $zerdb->ult_err );
  $insertar2 = $zerdb->insertar($zerdb->config, array("TrackYourPenguin", $url, 1) ) or die( $zerdb->ult_err );
  agregar_info( 
    __('<h2> Instalación completada </h2><br> Has instalado TrackYourPenguin correctamente, ya puedes <a href="%s">iniciar sesión</a> para continuar. :)'), 
  url() . 'acceso.php' );
  construir_pies();
  exit();
}
}
?>
<hr>
<form method="POST" class="form-horizontal">
  <div class="control-group">
    <label class="control-label">
      <?php _e("Usuario") ?>:
    </label>
    <div class="controls">
      <input type="text" name="usuario" id="usuario" required="required" <?php echo ($post) ? 'value="' . $_POST['usuario'] . '"' : '' ?>>
      <span class="help-block"><?php _e("Tu nombre de usuario para el sitio") ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">
      <?php _e("Clave") ?>
    </label>
    <div class="controls">
      <input type="password" name="clave" id="clave" required="required">
      <span class="help-block"><?php _e("Tu clave para el inicio de sesión") ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">
      <?php _e("Confirmar clave") ?>
    </label>
    <div class="controls">
      <input type="password" name="clave2" id="clave2" required="required">
      <span class="help-block"><?php _e("Confirma que la clave que pones es la que quieres") ?></span>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label">
      <?php _e("Email") ?>
    </label>
    <div class="controls">
      <input type="email" name="email" id="email" required="required" <?php echo ($post) ? 'value="' . $_POST['email'] . '"' : '' ?>>
      <span class="help-block"><?php _e("Tu email, el que más utilices") ?></span>
    </div>
  </div><hr>
  <center><input type="submit" name="enviar" value="<?php _e("Enviar") ?>" class="btn btn-large btn-primary"></center>
<?php
break;
default:
header("Location: instalar.php?paso=1");
}
construir_pies();