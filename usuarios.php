<?php
/**
*
* Archivo de usuarios
*
* Manipula los usuarios del sitio
*
* @package TrackYourPenguin
* @since 0.1
* @author Zerquix18
*
**/

require_once( dirname(__FILE__) . '/typ-load.php' );

comprobar( false );

$usuario = obt_usuario_actual();

if( ! es_super_admin() )
	$dnd = array("usuario" => $usuario->usuario );
else
	$dnd = null;
$u = obt_usuario_actual();
//now everyone can see the users ^-^!
$accion = isset($_GET['accion']) && is_string($_GET['accion']) ? $_GET['accion'] : '';
$post = 'POST' == getenv('REQUEST_METHOD');
switch( $accion ) {
	case "agregar":

	if( ! es_super_admin() )
		typ_die( __("Cheatin', uh?!") );

	construir( 'cabecera', __('Add user'), true ); 

	?>
	<h3><?php _e("Add user") ?></h3>
	<a href="<?php echo url() ?>usuarios.php" class="btn btn-link pull-right">
		<?php _e("Back to users") ?> &rarr;
	</a><hr>
	<?php
		if( $post ) {
			$nombre = trim( @$zerdb->real_escape( strtolower( $_POST['usuario']) ) );
			$clave = md5( @$zerdb->real_escape( $_POST['clave'] ) );
			$email = trim( @$zerdb->real_escape( strtolower($_POST['email']) ) );
			$rango1 = is_numeric(@$_POST['rango']) ? (int) @$_POST['rango'] : '';
			$rango2 = $u->rango;
			$args = ! comprobar_args( @$_POST['usuario'], @$_POST['clave'], @$_POST['email'], @$_POST['clave2'], @$_POST['rango']);
			$vacios = vacios( @$_POST['usuario'], @$_POST['clave'], @$_POST['email'], @$_POST['clave2'], @$_POST['rango'] );
			if( $args ) {
				typ_die( __("Cheatin', uh?!"));
			}elseif( $vacios ) {
				agregar_error( __("You can't leave empty fields."));
			}elseif( comprobar_rangos($rango1, $rango2 ) ) {
				agregar_error( __("Cheatin' to get a better role?") );
			}elseif( ! preg_match('/^[a-z0-9-_]{3,12}$/i', $_POST['usuario'] ) ) {
				agregar_error( __("I need a valid user. From 3 to 12 characters."));
			}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
				agregar_error( __("The email is not valid."));
			}elseif( existe_usuario( strtolower( $_POST['usuario'] ) ) ) {
				agregar_error( __("That user already exists."));
			}elseif( existe_email( strtolower($_POST['email'] ) ) ) {
				agregar_error( __("That email already exists"));
			}elseif( $rango1 == 1) {
				agregar_error( __("It can't be super admin!") );
			}else{
				$zerdb->insert( $zerdb->usuarios, array($nombre, $clave, $email, 1, $rango1, '') );
				agregar_info( __("The user has been created."), true, true );
				echo redireccion( url() . 'usuarios.php?id='.  $zerdb->id, 2 );
			}
		}
	?>
<form class="form-horizontal" action="<?php echo url() . 'usuarios.php?accion=agregar' ?>" method="POST">
	<div class="control-group">
		<label class="control-label">
		<?php _e("User") ?>
		</label>
		<div class="controls">
			<input type="text" name="usuario" <?php if($post) echo 'value="' . @$_POST['usuario'] . '"' ?> required="required"
			pattern="^[A-Za-z0-9-_]{3,12}$">
			<span class="help-block"><?php _e("Username") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Password") ?>
		</label>
		<div class="controls">
			<input type="password" name="clave" id="clave" required="required">
			<span class="help-block"><?php _e("User password") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Re-type password") ?>
		</label>
		<div class="controls">
			<input type="password" name="clave2" id="clave2" required="required">
			<span class="help-block"><?php _e("Re-type the user password") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Email") ?>
		</label>
		<div class="controls">
			<input type="email" name="email" id="email" required="required" <?php if($post) echo 'value="'. @$_POST['email'] . '"' ?>>
			<span class="help-block"><?php _e("User email") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e('Role') ?>
		</label>
		<div class="controls">
			<select name="rango">
				<option value="2"><?php _e("Administrator") ?></option>
				<option value="3"><?php _e("Updater") ?></option>
			</select>
			<span class="help-block"><?php _e("Which will be this user's role?") ?></span>
		</div>
	</div>
	<hr>
	<center><input type="submit" name="enviar" class="btn btn-primary text-center" value="<?php _e("Add") ?>" id="enviar"></center>
</form>
	<?php
	break;
	case "editar":
	if( ! isset($_GET['id']) || ! is_numeric( $_GET['id'] ) )
		typ_die( __("I need a valid ID.") );

	$u = obt_id( $_GET['id'] );
	$usuario = obt_usuario_actual();
	if( false == $u )
		typ_die( __("The user you specify doesn't exist.") );

	if( ! es_super_admin() && $_GET['id'] !== $_SESSION['id'] )
		typ_die( __("You are not allowed to be here.") );

	if( $u->rango <= $usuario->rango && $_GET['id'] !== $_SESSION['id'] && ! es_super_admin() )
		typ_die( __("You can't edit someone's user with a role bigger or the same that you.") );

	construir( 'cabecera', sprintf( __("Edit user: %s"), ucfirst($u->usuario) ), true ); ?>

<h3><?php _e("Edit user") ?>: <i><?php echo ucfirst( $u->usuario ) ?></i></h3>
<a href="<?php echo url() ?>usuarios.php?id=<?php echo $u->id ?>" class="btn btn-link pull-right">
	<?php _e("Back to user") ?> &rarr;
</a><hr>
	<?php
		if( $post ) {
			$nombre = @$zerdb->real_escape( strtolower($_POST['usuario']) );
			$email = @$zerdb->real_escape( strtolower($_POST['email']) );
			$rango2 = $u->rango;
			$rango = ( $u->id !== $_SESSION['id'] ) ? @$_POST['rango'] : $u->rango;
			$rango1 = is_numeric($rango) ? (int) $rango : '3';
			$args = ! comprobar_args( @$_POST['usuario'], @$_POST['email']);
			$vacios = vacios( @$_POST['usuario'], @$_POST['email'] );
			$estado = 1;
			$hash = '';
			$usuario_ = $zerdb->query("SELECT * FROM {$zerdb->usuarios} WHERE usuario = ? AND email != ?", $nombre, $u->email);
			$email_ =  $zerdb->query("SELECT * FROM {$zerdb->usuarios} WHERE email = ? AND usuario != ?", $email, $u->usuario);
			if( $args ) {
				typ_die( __("Cheatin', uh?!") );
			}elseif( $vacios ) {
				agregar_error( __("You can't leave empty fields."), true, true);
			}elseif( isset($_POST['rango']) && is_string($_POST['rango'] && $_POST['rango'] > $u->rango) ) {
				agregar_error( __("Cheatin' to get a better role?"), true, true);
			}elseif( ! preg_match('/^[A-Za-z0-9-_]{3,12}$/', $_POST['usuario'] ) ) {
				agregar_error( __("Type a valid username. From 3 to 12 characters."), true, true);
			}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
				agregar_error( __("The email is not valid."), true, true);
			}elseif(  $usuario_->nums > 0 ) {
				agregar_error( __("That user already exist."), true, true);
			}elseif( $email_->nums > 0) {
				agregar_error( __("That email already exist."), true, true);
			}elseif( $rango == 1 && ! es_super_admin() ) {
				agregar_error( __("You can't be super admin."), true, true);
			}else{
				$zerdb->update( $zerdb->usuarios,array("usuario" => $nombre, "email" => $email, "rango" => $rango) ) -> where('id', $u->id ) -> execute();
				agregar_info( __("The user has been updated."), true, true );
				
			}
		}
	?>
<form class="form-horizontal" action="<?php echo url( true ) ?>" method="POST">
	<div class="control-group">
		<label class="control-label">
		<?php _e("User") ?>
		</label>
		<div class="controls">
			<input type="text" name="usuario" value="<?php if($post) echo @$_POST['usuario']; else echo ucwords($u->usuario) ?>" required="required"
			pattern="^[A-Za-z0-9-_]{3,12}$">
			<span class="help-block"><?php _e("Username") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Email") ?>
		</label>
		<div class="controls">
			<input type="email" name="email" id="email" required="required" value="<?php if($post) echo @$_POST['email']; else echo strtolower($u->email) ?>">
			<span class="help-block"><?php _e("User email") ?></span>
		</div>
	</div>
<?php if( es_super_admin() ) : ?>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Role") ?>
		</label>
		<div class="controls">
		<?php if( $u->id !== $_SESSION['id'] ) : ?>
			<select name="rango">
			<?php if( $u->rango == '2' && es_super_admin() ) : ?>
				<option value="2"><?php _e("Administrator") ?></option>
				<option value="3"><?php _e("Updater") ?></option>
			<?php elseif( $u->rango == '3' && es_super_admin() ) : ?>
				<option value="3"><?php _e("Updater") ?></option>
				<option value="2"><?php _e("Administrator") ?></option>
			<?php elseif( $u->rango == '3' && es_admin() ) : ?>
				<option value="3"><?php _e("Updater") ?></option>
			<?php endif ?>
			</select>
			<span class="help-block"><?php _e("Which will be this user's role?") ?></span>
		<?php elseif( $u->id == $_SESSION['id'] ) : ?>
			<span class="help-inline"><?php _e("You can't change your own role.") ?></span>
		<?php endif ?>
		</div>
	</div>
<?php endif ?>
	<hr>
	<center><input type="submit" name="enviar" class="btn btn-primary text-center" value="<?php _e('Update') ?>" id="enviar"></center>
</form>
<?php
break;
case "eliminar":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die( __("You must specify a valid ID.") );

	if( ! es_super_admin() )
		typ_die( __("Cheatin', uh?!") );
	$u = obt_id( $_GET['id'] );
	if( $u == false )
		typ_die( __("The selected user doesn't exist.") );

	if( $u->id == $_SESSION['id'])
		typ_die( __("You can't delete your own user. >.<") );

	construir('cabecera', sprintf( __('Delete the user: %s'), $u->usuario ) );

	$zerdb->delete($zerdb->usuarios, array("id" => $_GET['id'] ) )->_();
	$sesiones->destruir_id( $_GET['id'] ); // bye sessions...
	agregar_info( sprintf( __('The user %s has been deleted'), ucfirst($u->usuario) ) );
	echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Back</a></li></ul>"), url() . 'usuarios.php');
break;
case "suspender":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die( __("You must specify an ID.") );
	if( ! es_super_admin() )
		typ_die("You are not allowed to be here.");
	$u = obt_id( $_GET['id'] );

	if( false == $u )
		typ_die( __("The selected user doesn't exist.") );

	if( $u->id == $_SESSION['id'])
		typ_die( __("You can't ban your own user.") );

	if( $u->estado !== "1" )
		typ_die( __("This user is already banned.") );

	construir('cabecera', sprintf( __("Ban: %s"), ucfirst($u->usuario) ), true );
	$x = $zerdb->update($zerdb->usuarios, array("estado" => "0") ) -> where("id", $_GET['id'])->execute();
	$sesiones->destruir_id( $u->id );
	if( $x )
		agregar_info( sprintf( __("The user <strong>%s</strong> has been banned."), ucfirst($u->usuario) ) );
	else
		echo "Error: " . $zerdb->error;
	echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Back</a></li></ul>"), url() . 'usuarios.php');
	break;
	case "quitar_suspension":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die( __("You must specify an ID.") );

	if( ! es_super_admin() )
		typ_die( __("You are not allowed to be here.") );

	$u = obt_id( $_GET['id'] );

	if( false == $u)
		typ_die( __("The selected user doesn't exist.") );

	if( $u->id == $_SESSION['id'])
		typ_die( esc_html("What the fuck are you doing? ><!") ); // xddd
	if( $u->estado == "1" )
		typ_die( __("This user is not banned.") );

	construir('cabecera', sprintf( __("Unbanning: %s"), ucfirst($u->usuario) ), true );
	$x = $zerdb->update($zerdb->usuarios, array("estado" => "1") ) -> where("id", $_GET['id'])->execute();
	if( $x )
		agregar_info( sprintf( __("The user %s has been unbanned."), ucfirst($u->usuario) ) );
	else
		echo "Error: " . $zerdb->error;
	echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Back</a></li></ul>"), url() . 'usuarios.php');
	break;
default:

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? true : false;

switch( $id ) {

	case true:

	$u = obt_id($_GET['id']);
	$usuario = obt_id( $_SESSION['id'] );

	if( false == $u || $u->id !== $_SESSION['id'] && ! es_admin() )
		typ_die( __("This user doesn't exist or you're not allowed to read its data.") );
	construir( 'cabecera', ucfirst($u->usuario), true );
	?>
	<h3><?php _e("User") ?>: <?php echo sprintf( '<em>%s</em>', ucfirst( $u->usuario ) ) ?></h3>
	<a href="<?php echo url() ?>usuarios.php" class="btn btn-link pull-right">
		<?php _e("Back to users") ?> &rarr;
	</a><hr>
	<p><strong><?php _e("User") ?>:</strong>&nbsp;<i><?php echo ucfirst($u->usuario) ?></i></p>
	<p><strong><?php _e("Email") ?>:</strong>&nbsp;<i><?php echo $u->email ?></i></p>
	<p><strong><?php _e("Status") ?>:</strong>&nbsp;<i><?php echo estado($u->estado) ?></i></p>
	<p><strong><?php _e("Role") ?>:</strong>&nbsp;<i><?php echo rango( $u->id ) ?></i></p>
	<p><strong><?php _e('Last update done') ?>:</strong>&nbsp;<i><?php
	$q = $zerdb->select($zerdb->log)->like('accion', $u->usuario )->add("ORDER BY id DESC")->limit(1);
	if( $q->nums > 0 ) {
		$l = json_decode($q->accion);
		$t = obt_tracker($l->tracker);
		$t = isset($t) ? ucfirst($t->personaje) : __('Unknown tracker');
		echo sprintf("<b>%s</b> in <b>%s</b>", mostrar_fecha($q->fecha), $t );
	}else{
		echo __('No recent updates have been found.');
	}
	?></i></p><?php
	break;
	case false:
	construir( 'cabecera', __('Users'), true );
	?>
	<h3> <?php _e("Users") ?> </h3>
	<?php if( es_super_admin() ) : ?>
	<a href="<?php echo url() . 'usuarios.php?accion=agregar' ?>" class="btn btn-link pull-right">
		<i class="icon-plus"></i>&nbsp; <?php _e("Add new") ?></a>
	<?php endif ?><hr>
	<table class="table table-bordered table-hover">
		<tr>
			<th><?php _e("User") ?></th>
			<th><?php _e("Email") ?></th>
			<th><?php _e("Role") ?></th>
			<th><?php _e("Status") ?></th>
			<th><center>#</center></th>
		</tr>
	<?php
	$users = $zerdb->select( $zerdb->usuarios);
	if( $u->rango !== "1" )
		 $users->where( array('usuario' => $_SESSION['usuario']) );
	$r = $users->execute();
		while($u = $r->r->fetch_array() ) {
			?>
			<tr>
				<td><?php echo ucfirst($u['usuario']) ?></td>
				<td><?php echo $u['email'] ?></td>
				<td><?php echo rango( $u['id'] ) ?></td>
				<td><?php echo estado( $u['estado'] ) ?></td>
				<td><center>
    						<a class="btn btn-success btn-small" href="<?php echo url() . 'usuarios.php?accion=editar&id=' . $u['id'] ?>"
							title="<?php _e("Edit") ?>">
							<i class="icon-pencil"></i></a>
						<?php if( es_super_admin() && $u['rango'] !== '1' && $u['id'] !== $_SESSION['id'] ) : ?>
   							<a class="btn btn-danger btn-small" href="<?php echo url() . 'usuarios.php?accion=eliminar&id=' . $u['id'] ?>"
   								title="<?php _e("Delete") ?>"><i class="icon-trash"></i></a>
   						<?php endif ?>
   						<?php if( es_super_admin() &&  $u['estado'] == '1' && $u['rango'] !== '1' ) : ?>
						    <a class="btn btn-warning btn-small" title="<?php _e('Ban') ?>" href="<?php echo url() . 'usuarios.php?accion=suspender&id=' . $u['id'] ?>"><i class="icon-ban-circle"></i></a>
						<?php elseif( es_super_admin() && $u['estado'] !== '1' && $u['rango'] !== '1' ) : ?>
							<a class="btn btn-inverse btn-small" title="<?php _e('Unban') ?>" href="<?php echo url() . 'usuarios.php?accion=quitar_suspension&id=' . $u['id'] ?>"><i class="icon-ban-circle"></i></a>
						<?php endif ?>
					</a>
			<?php
		}
		?></center></td></table><?php
	}
}
construir( 'pies' );