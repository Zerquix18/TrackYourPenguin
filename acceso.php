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
			<li><a href="<?php echo url() . 'acceso.php?accion=co' ?>"><?php _e('Forgot password?') ?>&rarr; </li>
        <?php elseif($acc == 'co') : ?>
			<li><a href="<?php echo url() . 'acceso.php?accion=acceder' ?>">&larr; <?php _e('Back to login') ?> </li>
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
		$mensaje = __('Hash or password are WRONG.');
		$error = true;
	elseif( ! $post ) :
		$mensaje = __('Type your email to recover your password.');
		$error = false;
	else:
		$mensaje = $error = false;
	endif;

	cabecera( __('Forgot password'), $mensaje, $error);

	if( $post ) {
		$email = $zerdb->real_escape( trim($_POST['email']) );
		$usuario = $zerdb->select($zerdb->usuarios, "*", array("email" => $email) )->_();

		if( ! comprobar_args( @$_POST['email'] ) ) {
			agregar_error( __("Cheatin', uh?!") );
		}elseif( vacio($_POST['email']) ) {
			agregar_error( __("The email input can't be empty."), true, true);
		}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
			agregar_error( __("That email isn't valid."), true, true);
		}elseif(! $usuario || 0 == (int) $usuario->nums ) {
			agregar_error( __("The email you typed is not registered."), true, true);
		}else{
			if( empty($usuario->hash) ) {
				$hash = md5( uniqid() );
				$x = $zerdb->update($zerdb->usuarios, "hash", $hash )->where("email", $email )->_();
			}else{
				$hash = $usuario->hash;
			}
			$asunto = sprintf( __('Forgot password [%s]'), titulo() );
			$texto = sprintf( __("<html><body>
					Hi, <strong>%s</strong>! \n\n
					Someone sent a request to this email for a lost password, if it was you
					<a href=\"%s\"><strong>click here</strong></a>\n\n
					If it wasn't you, don't do nothing, take it easy and nothing will happen. :)</body></html>"), 
			$usuario->usuario, url() . 'acceso.php?accion=rc&hash=' . $hash . '&usuario=' . $usuario->usuario);
			$mail = enviar_email($email, $asunto, $texto);
			if( $mail === true )
				agregar_info( __("The mail has been sent. Check it.") );
			else
				agregar_error( sprintf( __("Unafortunely, the email couldn't send. Error returned: %s"), $mail ) );
		}
	}
	?>
<form method="POST" class="form-signin" action="<?php echo url() . 'acceso.php?accion=co' ?>">
  <h2 class="form-signin-heading"><?php _e('Forgot password') ?></h2>
  <input type="email" name="email" class="input-block-level" placeholder="<?php _e('Type your email') ?>" id="email" 
  required="required" <?php if($post) _f(@$_POST['email'], 1) ?> maxlength="60">
  <center><input type="submit" value="<?php _e('Send') ?>" class="btn btn-primary"></center>
</form>
	<?php
break;
	case "rc": // Recuperar clave
	$hash_ = isset($_GET['hash']) && ! empty($_GET['hash']) && is_string($_GET['hash']) ? $_GET['hash'] : '';
	$usuario_ = isset($_GET['usuario']) && !empty($_GET['usuario']) && is_string($_GET['usuario']) ? $_GET['usuario'] : '';
	if( empty($usuario_) || empty($hash_) ) 
		exit( header("Location: acceso.php?accion=co&error=1") );

	$usuario = $zerdb->real_escape( $_GET['usuario'] );
	$hash = $zerdb->real_escape( $_GET['hash'] );
	$u = $zerdb->select($zerdb->usuarios, "*", array("hash" => $hash, "usuario" => $usuario) )->_();
	if( ! $u  || !$u->nums > 0)
		exit( header("Location: acceso.php?accion=co&error=1&" ) );
	cabecera( __('Change password'), (!$post) ? __('Now you can type your password to update it.') : false, false );
	if( $post ) {
		if( ! comprobar_args(@$_POST['clave'], @$_POST['clave2']) ) {
			agregar_error( __("Cheatin', uh?!"));
		}elseif( vacios( $_POST['clave'], $_POST['clave2'] ) ) {
			agregar_error( __("You can't leave empty fields.") );
		}elseif( ! ($_POST['clave'] == $_POST['clave2']) ) {
			agregar_error( __("The password don't match."));
		}else{
			$clave = md5( $zerdb->real_escape($_POST['clave']) );
			$zerdb->update($zerdb->usuarios, array("clave" => $clave, "hash" => "") )->where("id", $u->id )->_(); // bug fixed ;)
			agregar_info( __("Your password has been updated."));
		}
	}
?>
<form method="POST" class="form-signin" action="<?php echo url() . sprintf('acceso.php?accion=rc&usuario=%s&hash=%s', $_GET['usuario'], $_GET['hash']) ?>">
  <h2 class="form-signin-heading"><?php _e('Update password') ?></h2>
  <input type="password" name="clave" class="input-block-level" placeholder="<?php _e('Type your new password') ?>" id="clave" 
  required="required">
  <input type="password" name="clave2" class="input-block-level" placeholder="<?php _e('Re-type your new password.') ?>" id="clave2"
  required="required">
  <center><input type="submit" value="<?php _e('Update password') ?>" class="btn btn-primary"></center>
</form>
	<?php
	break;
	case "acceder":
	default:
	if( ! isset($_GET['salir']) ) comprobar( true );

	if( isset($_GET['continuar']) && is_string($_GET['continuar']) && ! empty( $_GET['continuar']) && ! $post ) {
		$mensaje = __("Please log in to continue.");
		$error = true;
	}else{
		$mensaje = false;
		$error = false;
	}

	cabecera( __('Log In' ), $mensaje, $error );

	if( $post ) {
		$usuario = @$zerdb->real_escape( strtolower( trim($_POST['usuario']) ) );
		$clave = md5( @$zerdb->real_escape( $_POST['clave']) );
		$query = $zerdb->select($zerdb->usuarios, "*", array("usuario" => $usuario, "clave" => $clave) )->_();
		$recordar = isset($_POST['recuerdame']) ? true : false;
		
		if( ! comprobar_args( @$_POST['usuario'], @$_POST['clave'] ) ) {
			agregar_error( __("Cheatin', uh?!") );
		}elseif( vacios($_POST['usuario'], $_POST['clave']) ) {
			agregar_error( __("You can't leave empty fields.") );
		}elseif( ! $query || ! $query->nums > 0) {
			agregar_error( __("The user and password don't match."), true, true);
		}elseif( 1 != $query->estado ) {
			agregar_error( __("This user was banned.") );
		}else{
		$sesion->crear( $usuario, $recordar );
        $uri = urlencode(url());
        $decoded = (isset($_GET['continuar'])) ? urldecode( $_GET['continuar']) : '';
        if( isset($_GET['continuar']) && preg_match("#" . url() . "#", $decoded ) )
          header("Location: " . urldecode($_GET['continuar']) );
        else
          header("Location: " . url() );
		}
	}elseif( isset($_GET['salir']) && 1 == $_GET['salir']) {
		$sesion->destruir();
		agregar_info( __("You have logged out.") );
	}elseif( sesion_iniciada() ) {
		header("Location: " . url() );
	}
?>
<form method="POST" class="form-signin" action="<?php
	echo url() . 'acceso.php';
	if ( isset($_GET['continuar']) && ! empty($_GET['continuar']) && is_string($_GET['continuar']) )
		echo sprintf('?continuar=%s', $_GET['continuar']);
			?>">
  <h2 class="form-signin-heading"><?php _e('Log In') ?></h2>
  <input type="text" name="usuario" class="input-block-level" placeholder="<?php _e('Username') ?>" id="usuario" 
  required="required" <?php if( $post ) _f(@$_POST['usuario']) ?>>
  <input type="password" name="clave" class="input-block-level" placeholder="<?php _e('Password') ?>" id="clave"
  required="required">
  <label class="checkbox"><input <?php if( $post && $recordar ) echo 'checked="checked"' ?> type="checkbox" name="recuerdame"><?php _e('Remember me') ?></label>
  <center><input type="submit" value="<?php _e('Send') ?>" class="btn btn-primary"></center>
</form>
	<?php
}
pies();