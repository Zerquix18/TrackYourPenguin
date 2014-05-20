<?php
/**
* Archivo de acceso 
*
* Accede y recupera clave perdida
*
* @author Zerquix18 <http://www.zerquix18.com/>
* @since 0.1
* @link http://trackyourpenguin.com/
* 
*
**/

require_once( dirname(__FILE__) . '/typ-load.php');
$post = "POST" == getenv('REQUEST_METHOD');
$accs = array('acceder', 'co', 'rc');
$acc = isset($_GET['accion']) && is_string($_GET['accion']) && 
	in_array( strtolower($_GET['accion']), $accs) ? strtolower($_GET['accion']) : '';

/**
*
* Carga la cabecera del archivo de acceso
*
* @param $titulo string
* @param $mensaje bool
* @param $error bool
*
**/

function cabecera($titulo = '', $mensaje = false, $error = false ) {
	$titulo = vacio($titulo) ? titulo() : $titulo . ' - ' . titulo();
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<title><?php echo $titulo ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="<?php echo url() . INC . CSS . 'bootstrap.css' ?>" rel="stylesheet">
	<link href="<?php echo url() . INC . CSS . 'bootstrap.min.css' ?>" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }
    </style>
  <body>
    <div class="container">
	<?php
	if( $mensaje && ! $error )
		agregar_info( $mensaje );
	elseif( $mensaje && $error )
		agregar_error($mensaje);
}

/**
*
* Pone los pies de la página de acceso
*
**/
function pies() {
	global $acc;
	?>
	     <ul class="pager">
        <?php if($acc == 'acceder' || vacio($acc) ): ?>
			<li><a href="<?php echo url() . 'acceso.php?accion=co' ?>"><?php _e('¿Olvidaste tus datos?') ?>&rarr; </li>
        <?php elseif($acc == 'co') : ?>
			<li><a href="<?php echo url() . 'acceso.php?accion=acceder' ?>">&larr; <?php _e('Volver al acceso') ?> </li>
		<?php endif; ?>
	     </ul>
    </center>
    </div> <!-- /container -->
    <script src="<?php echo url() . INC . JS . 'jquery.js'; ?>"></script>
    <script src="<?php echo url() . INC . JS . 'alerta.js'; ?>"></script>
  </body>
</html>
	<?php
}
switch( $acc ) {
	case "co": // Clave Olvidada

	if( isset($_GET['error']) ): 
		$mensaje = __('El hash o el usuario son incorrectos.');
		$error = true;
	elseif( ! $post ) :
		$mensaje = __('Agrega tu email para recuperar tu clave');
		$error = false;
	else:
		$mensaje = $error = false;
	endif;

	cabecera( __('Clave olvidada'), $mensaje, $error);

	if( $post ) {
		$email = @$zerdb->proteger( trim($_POST['email']) );
		$usuario = new extraer($zerdb->usuarios, "*", array("email" => $email) );

		if( ! comprobar_args( @$_POST['email'] ) ) {
			agregar_error( __("Haciendo trampa, ¿eh?") );
		}elseif( vacio($_POST['email']) ) {
			agregar_error( __("El email no puede estar vacío"), true, true);
		}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
			agregar_error( __("El email no parece válido"), true, true);
		}elseif(! $usuario || 0 == (int) $usuario->nums ) {
			agregar_error( __("El email que buscas no está registrado"), true, true);
		}else{
			if( empty($usuario->hash) ) {
				$hash = md5( uniqid() );
				$zerdb->actualizar($zerdb->usuarios, array("hash" => $hash), array("email" => $email) );
			}else{
				$hash = $usuario->hash;
			}
			$asunto = sprintf( __('Clave olvidada [%s]'), titulo() );
			$texto = sprintf( __("<html><body>
					¡Hola, <b>%s</b>! \n\n
					Se ha enviado una solicitud para cambiar la clave de tu usuario, si t&uacute; la enviaste, haz
					<a href=\"%s\"><b>clic aqu&iacute;</b></a>\n\n
					Si no la hiciste, d&eacute;jalo todo como est&aacute; y no pasar&aacute; nada. :)</body></html>"), 
			$usuario->usuario, url() . 'acceso.php?accion=rc&hash=' . $hash . '&usuario=' . $usuario->usuario);
			$mail = enviar_email($email, $asunto, $texto);
			if( $mail )
				agregar_info( __("Ha sido enviado un email a tu correo electrónico, por favor revísalo.") );
			else
				agregar_error( __("Lamentablemente no se pudo enviar el email, por favor revisa que tu hosting tenga la función <b>mail()</b> activa.") );
		}
	}
	?>
<form method="POST" class="form-signin" action="<?php echo url() . 'acceso.php?accion=co' ?>">
  <h2 class="form-signin-heading"><?php _e('Clave olvidada') ?></h2>
  <input type="email" name="email" class="input-block-level" placeholder="<?php _e('Ingresa tu email') ?>" id="email" 
  required="required" <?php if($post) _f(@$_POST['email'], 1) ?> maxlength="60">
  <center><input type="submit" value="<?php _e('Enviar') ?>" class="btn btn-primary"></center>
</form>
	<?php
break;
	case "rc": // Recuperar clave
	$hash_ = isset($_GET['hash']) && ! empty($_GET['hash']) && is_string($_GET['hash']) ? $_GET['hash'] : '';
	$usuario_ = isset($_GET['usuario']) && !empty($_GET['usuario']) && is_string($_GET['usuario']) ? $_GET['usuario'] : '';
	if( empty($usuario_) || empty($hash_) ) 
		exit( header("Location: acceso.php?accion=co&error=1") );

	$usuario = $zerdb->proteger( $_GET['usuario'] );
	$hash = $zerdb->proteger( $_GET['hash'] );
	$u = new extraer($zerdb->usuarios, "*", array("hash" => $hash, "usuario" => $usuario) );
	if( ! $u  || !$u->nums > 0)
		exit( header("Location: acceso.php?accion=co&error=1" ) );

	cabecera( __('Cambiar clave'), (!$post) ? __('Ya puedes poner tu nueva clave para actualizarla') : false, false );

	if( $post ) {
		if( ! comprobar_args(@$_POST['clave'], @$_POST['clave2']) ) {
			agregar_error( __("Haciendo trampa, ¿eh?"));
		}elseif( vacios( $_POST['clave'], $_POST['clave2'] ) ) {
			agregar_error( __("No puedes dejar campos vacíos") );
		}elseif( ! ($_POST['clave'] == $_POST['clave2']) ) {
			agregar_error( __("Las claves no coinciden"));
		}else{
			$clave = md5( $zerdb->proteger($_POST['clave']) );
			$zerdb->actualizar($zerdb->usuarios, array("clave" => $clave, "hash" => "") );
			agregar_info( __("Tu clave ha sido actualizada"));
		}
	}
?>
<form method="POST" class="form-signin" action="<?php echo url() . sprintf('acceso.php?accion=rc&usuario=%s&hash=%s', $_GET['usuario'], $_GET['hash']) ?>">
  <h2 class="form-signin-heading"><?php _e('Actualizar contraseña') ?></h2>
  <input type="password" name="clave" class="input-block-level" placeholder="<?php _e('Ingresa tu nueva clave') ?>" id="clave" 
  required="required">
  <input type="password" name="clave2" class="input-block-level" placeholder="<?php _e('Confirma tu clave') ?>" id="clave2"
  required="required">
  <center><input type="submit" value="<?php _e('Actualizar clave') ?>" class="btn btn-primary"></center>
</form>
	<?php
	break;
	case "acceder":
	default:
	if( ! isset($_GET['salir']) ) comprobar( true );

	if( isset($_GET['continuar']) && is_string($_GET['continuar']) && ! empty( $_GET['continuar']) && ! $post ) {
		$mensaje = __("Por favor, inicia sesión para continuar");
		$error = true;
	}else{
		$mensaje = false;
		$error = false;
	}

	cabecera( __('Acceder' ), $mensaje, $error );

	if( $post ) {
		$usuario = @$zerdb->proteger( strtolower( trim($_POST['usuario']) ) );
		$clave = md5( @$zerdb->proteger( $_POST['clave']) );
		$query = new extraer($zerdb->usuarios, "*", array("usuario" => $usuario, "clave" => $clave) );
		$recordar = isset($_POST['recuerdame']) ? true : false;

		if( ! comprobar_args( @$_POST['usuario'], @$_POST['clave'] ) ) {
			agregar_error( __("¿Haciendo trampa, eh?") );
		}elseif( vacios($_POST['usuario'], $_POST['clave']) ) {
			agregar_error( __("No puedes dejar campos vacíos") );
		}elseif( ! $query || ! $query->nums > 0) {
			agregar_error( __("El usuario o la clave no coinciden"), true, true);
		}elseif( 1 !== (int) $query->estado ) {
			agregar_error( __("Este usuario se encuentra suspendido") );
		}else{
		$sesion->crear( $usuario, $recordar );
        $uri = urlencode(url());
        $decoded = (isset($_GET['continuar'])) ? urldecode( $_GET['continuar']) : '';
        if( isset($_GET['continuar']) && preg_match("#" . url() . "#", $decoded ) )
          header("Location: " . urldecode($_GET['continuar']) );
        else
          header("Location: " . url() );
		}
	}elseif( isset($_GET['salir']) && (int) $_GET['salir'] == 1) {
		$sesion->destruir();
		agregar_info( __("Has cerrado sesión") );
	}elseif( sesion_iniciada() ) {
		header("Location: " . url() );
	}
?>
<form method="POST" class="form-signin" action="<?php
	echo url() . 'acceso.php';
	if ( isset($_GET['continuar']) && ! empty($_GET['continuar']) && is_string($_GET['continuar']) )
		echo sprintf('?continuar=%s', $_GET['continuar']);
			?>">
  <h2 class="form-signin-heading"><?php _e('Acceder') ?></h2>
  <input type="text" name="usuario" class="input-block-level" placeholder="<?php _e('Nombre de usuario') ?>" id="usuario" 
  required="required" <?php if( $post ) _f(@$_POST['usuario']) ?>>
  <input type="password" name="clave" class="input-block-level" placeholder="<?php _e('Ingresa tu clave') ?>" id="clave"
  required="required">
  <label class="checkbox"><input type="checkbox" name="recuerdame"><?php _e('Recuérdame') ?></label>
  <center><input type="submit" value="<?php _e('Enviar') ?>" class="btn btn-primary"></center>
</form>
	<?php
}
pies();