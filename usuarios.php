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
		typ_die( __("Haciendo trampa, ¿eh?") );

	construir( 'cabecera', __('Agregar usuario'), true ); 

	?>
	<h3><?php _e("Agregar usuario") ?></h3>
	<a href="<?php echo url() ?>usuarios.php" class="btn btn-link pull-right">
		<?php _e("Volver al usuario") ?> &rarr;
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
				typ_die( __("Haciendo trampa, ¿eh?"));
			}elseif( $vacios ) {
				agregar_error( __("No puedes dejar datos vacíos"));
			}elseif( comprobar_rangos($rango1, $rango2 ) ) {
				agregar_error( __("¿Haciendo trampa para obtener un mejor rango?") );
			}elseif( ! preg_match('/^[a-z0-9-_]{3,12}$/i', $_POST['usuario'] ) ) {
				agregar_error( __("Necesito un usuario válido. De 3 a 12 caracteres."));
			}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
				agregar_error( __("El email ingresado no parece ser válido"));
			}elseif( existe_usuario( strtolower( $_POST['usuario'] ) ) ) {
				agregar_error( __("El usuario ingresado ya existe"));
			}elseif( existe_email( strtolower($_POST['email'] ) ) ) {
				agregar_error( __("El email ingresado ya existe"));
			}elseif( $rango1 == 1) {
				agregar_error( __("No puede ser super administrador") );
			}else{
				$zerdb->insert( $zerdb->usuarios, array($nombre, $clave, $email, 1, $rango1, '') );
				agregar_info( __("El usuario ha sido ingresado"), true, true );
				echo redireccion( url() . 'usuarios.php?id='.  $zerdb->id, 2 );
			}
		}
	?>
<form class="form-horizontal" action="<?php echo url() . 'usuarios.php?accion=agregar' ?>" method="POST">
	<div class="control-group">
		<label class="control-label">
		<?php _e("Usuario") ?>
		</label>
		<div class="controls">
			<input type="text" name="usuario" <?php if($post) echo 'value="' . @$_POST['usuario'] . '"' ?> required="required"
			pattern="^[A-Za-z0-9-_]{3,12}$">
			<span class="help-block"><?php _e("El nombre de usuario") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Clave") ?>
		</label>
		<div class="controls">
			<input type="password" name="clave" id="clave" required="required">
			<span class="help-block"><?php _e("La clave para el usuario") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Confirmar clave") ?>
		</label>
		<div class="controls">
			<input type="password" name="clave2" id="clave2" required="required">
			<span class="help-block"><?php _e("Confirma la clave para el usuario") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Email") ?>
		</label>
		<div class="controls">
			<input type="email" name="email" id="email" required="required" <?php if($post) echo 'value="'. @$_POST['email'] . '"' ?>>
			<span class="help-block"><?php _e("Email para el usuario") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		Rango
		</label>
		<div class="controls">
			<select name="rango">
				<option value="2"><?php _e("Administrador") ?></option>
				<option value="3"><?php _e("Actualizador") ?></option>
			</select>
			<span class="help-block"><?php _e("¿Qué le vamos a permitir a este usuario?") ?></span>
		</div>
	</div>
	<hr>
	<center><input type="submit" name="enviar" class="btn btn-primary text-center" value="<?php _e("Agregar") ?>" id="enviar"></center>
</form>
	<?php
	break;
	case "editar":
	if( ! isset($_GET['id']) || ! is_numeric( $_GET['id'] ) )
		typ_die( __("Necesito un ID correcto") );

	$u = obt_id( $_GET['id'] );
	$usuario = obt_usuario_actual();
	if( false == $u )
		typ_die( __("El usuario que especificas no existe") );

	if( ! es_super_admin() && $_GET['id'] !== $_SESSION['id'] )
		typ_die( __("No tienes los premisos suficientes para acceder aquí") );

	if( $u->rango <= $usuario->rango && $_GET['id'] !== $_SESSION['id'] && ! es_super_admin() )
		typ_die( __("No puedes editar a alguien que tenga tu mismo rango o mayor") );

	construir( 'cabecera', sprintf( __("Editar el usuario: %s"), ucfirst($u->usuario) ), true ); ?>

<h3><?php _e("Editar el usuario") ?>: <i><?php echo ucfirst( $u->usuario ) ?></i></h3>
<a href="<?php echo url() ?>usuarios.php?id=<?php echo $u->id ?>" class="btn btn-link pull-right">
	<?php _e("Volver al usuario") ?> &rarr;
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
				typ_die( __("Haciendo trampa, ¿eh?") );
			}elseif( $vacios ) {
				agregar_error( __("No puedes dejar datos vacíos"), true, true);
			}elseif( isset($_POST['rango']) && is_string($_POST['rango'] && $_POST['rango'] > $u->rango) ) {
				agregar_error( __("¿Haciendo trampa para obtener un mejor rango?"), true, true);
			}elseif( ! preg_match('/^[A-Za-z0-9-_]{3,12}$/', $_POST['usuario'] ) ) {
				agregar_error( __("Pon un nombre de usuario válido, de 3 a 12 caracteres"), true, true);
			}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
				agregar_error( __("El email ingresado no parece ser válido"), true, true);
			}elseif(  $usuario_->nums > 0 ) {
				agregar_error( __("El usuario ingresado ya existe"), true, true);
			}elseif( $email_->nums > 0) {
				agregar_error( __("El email ingresado ya existe"), true, true);
			}elseif( $rango == 1 && ! es_super_admin() ) {
				agregar_error( __("No puedes ser super administrador"), true, true);
			}else{
				$zerdb->update( $zerdb->usuarios,array("usuario" => $nombre, "email" => $email, "rango" => $rango) ) -> where('id', $u->id ) -> execute();
				agregar_info( __("El usuario ha sido actualizado"), true, true );
				
			}
		}
	?>
<form class="form-horizontal" action="<?php echo url( true ) ?>" method="POST">
	<div class="control-group">
		<label class="control-label">
		<?php _e("Usuario") ?>
		</label>
		<div class="controls">
			<input type="text" name="usuario" value="<?php if($post) echo @$_POST['usuario']; else echo ucwords($u->usuario) ?>" required="required"
			pattern="^[A-Za-z0-9-_]{3,12}$">
			<span class="help-block"><?php _e("El nombre de usuario") ?></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Email") ?>
		</label>
		<div class="controls">
			<input type="email" name="email" id="email" required="required" value="<?php if($post) echo @$_POST['email']; else echo strtolower($u->email) ?>">
			<span class="help-block"><?php _e("Email para el usuario") ?></span>
		</div>
	</div>
<?php if( es_super_admin() ) : ?>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Rango") ?>
		</label>
		<div class="controls">
		<?php if( $u->id !== $_SESSION['id'] ) : ?>
			<select name="rango">
			<?php if( $u->rango == '2' && es_super_admin() ) : ?>
				<option value="2"><?php _e("Administrador") ?></option>
				<option value="3"><?php _e("Actualizador") ?></option>
			<?php elseif( $u->rango == '3' && es_super_admin() ) : ?>
				<option value="3"><?php _e("Actualizador") ?></option>
				<option value="2"><?php _e("Administrador") ?></option>
			<?php elseif( $u->rango == '3' && es_admin() ) : ?>
				<option value="3"><?php _e("Actualizador") ?></option>
			<?php endif ?>
			</select>
			<span class="help-block"><?php _e("¿Qué le vamos a permitir a este usuario?") ?></span>
		<?php elseif( $u->id == $_SESSION['id'] ) : ?>
			<span class="help-inline"><?php _e("No puedes cambiar tu propio rango") ?></span>
		<?php endif ?>
		</div>
	</div>
<?php endif ?>
	<hr>
	<center><input type="submit" name="enviar" class="btn btn-primary text-center" value="<?php _e('Actualizar') ?>" id="enviar"></center>
</form>
<?php
break;
case "eliminar":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die( __("Debes especificar un ID correcto") );

	if( ! es_super_admin() )
		typ_die( __('Haciendo trampa, ¿eh?') );
	$u = obt_id( $_GET['id'] );
	if( $u == false )
		typ_die( __("El usuario seleccionado no existe") );

	if( $u->id == $_SESSION['id'])
		typ_die( __("No puedes eliminar tu propio usuario") );

	construir('cabecera', sprintf( __('Eliminar al usuario: %s'), $u->usuario ) );

	$zerdb->delete($zerdb->usuarios, array("id" => $_GET['id'] ) )->_();
	$sesiones->destruir_id( $_GET['id'] ); // bye sessions...
	agregar_info( sprintf( __('El usuario %s ha sido eliminado'), ucfirst($u->usuario) ) );
	echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>"), url() . 'usuarios.php');
break;
case "suspender":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die( __("Debes especificar un ID") );
	if( ! es_super_admin() )
		typ_die("No tienes rango suficiente");
	$u = obt_id( $_GET['id'] );

	if( false == $u )
		typ_die( __("El usuario seleccionado no existe") );

	if( $u->id == $_SESSION['id'])
		typ_die( __("No puedes suspender tu propio usuario") );

	if( $u->estado !== "1" )
		typ_die( __("Este usuario ya está suspendido") );

	construir('cabecera', sprintf( __("Suspender al usuario: %s"), ucfirst($u->usuario) ), true );
	$x = $zerdb->update($zerdb->usuarios, array("estado" => "0") ) -> where("id", $_GET['id'])->execute();
	$sesiones->destruir_id( $u->id );
	if( $x )
		agregar_info( sprintf( __("El usuario <strong>%s</strong> ha sido suspendido"), ucfirst($u->usuario) ) );
	else
		echo "Error: " . $zerdb->error;
	echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>"), url() . 'usuarios.php');
	break;
	case "quitar_suspension":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die( __("Debes especificar un ID") );

	if( ! es_super_admin() )
		typ_die( __("No tienes rango suficiente") );

	$u = obt_id( $_GET['id'] );

	if( false == $u)
		typ_die( __("El usuario seleccionado no existe") );

	if( $u->id == $_SESSION['id'])
		typ_die( esc_html("¿Qué mierda estás haciendo? ><!") ); // xddd
	if( $u->estado == "1" )
		typ_die( __("Este usuario no se encuentra suspendido") );

	construir('cabecera', sprintf( __("Quitar suspensión al usuario: %s"), ucfirst($u->usuario) ), true );
	$x = $zerdb->update($zerdb->usuarios, array("estado" => "1") ) -> where("id", $_GET['id'])->execute();
	if( $x )
		agregar_info( sprintf( __("Al usuario <strong>%s</strong> se le ha removido la suspensión"), ucfirst($u->usuario) ) );
	else
		echo "Error: " . $zerdb->error;
	echo sprintf( __("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>"), url() . 'usuarios.php');
	break;
default:

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? true : false;

switch( $id ) {

	case true:

	$u = obt_id($_GET['id']);
	$usuario = obt_id( $_SESSION['id'] );

	if( false == $u || $u->id !== $_SESSION['id'] && ! es_admin() )
		typ_die( __("Este usuario no existe o tienes permiso de ver sus datos") );
	construir( 'cabecera', ucfirst($u->usuario), true );
	?>
	<h3><?php _e("Usuario") ?>: <?php echo sprintf( '<em>%s</em>', ucfirst( $u->usuario ) ) ?></h3>
	<a href="<?php echo url() ?>usuarios.php" class="btn btn-link pull-right">
		<?php _e("Volver al Inicio") ?> &rarr;
	</a><hr>
	<p><strong><?php _e("Usuario") ?>:</strong>&nbsp;<i><?php echo ucfirst($u->usuario) ?></i></p>
	<p><strong><?php _e("Email") ?>:</strong>&nbsp;<i><?php echo $u->email ?></i></p>
	<p><strong><?php _e("Estado") ?>:</strong>&nbsp;<i><?php echo estado($u->estado) ?></i></p>
	<p><strong><?php _e("Rango") ?>:</strong>&nbsp;<i><?php echo rango( $u->id ) ?></i></p>
	<p><strong><?php _e('Última actualización hecha') ?>:</strong>&nbsp;<i><?php
	$q = $zerdb->select($zerdb->log)->like('accion', $u->usuario )->add("ORDER BY id DESC")->limit(1);
	if( $q->nums > 0 ) {
		$l = json_decode($q->accion);
		$t = obt_tracker($l->tracker);
		$t = isset($t) ? ucfirst($t->personaje) : __('Tracker desconocido');
		echo sprintf("<b>%s</b> en el tracker de <b>%s</b>", mostrar_fecha($q->fecha), $t );
	}else{
		echo __('No se encontraron actualizaciones recientes');
	}
	?></i></p><?php
	break;
	case false:
	construir( 'cabecera', __('Usuarios'), true );
	?>
	<h3> <?php _e("Usuarios") ?> </h3>
	<?php if( es_super_admin() ) : ?>
	<a href="<?php echo url() . 'usuarios.php?accion=agregar' ?>" class="btn btn-link pull-right">
		<i class="icon-plus"></i>&nbsp; <?php _e("Agregar nuevo") ?></a>
	<?php endif ?><hr>
	<table class="table table-bordered table-hover">
		<tr>
			<th><?php _e("Usuario") ?></th>
			<th><?php _e("Email") ?></th>
			<th><?php _e("Rango") ?></th>
			<th><?php _e("Estado") ?></th>
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
							title="<?php _e("Editar") ?>">
							<i class="icon-pencil"></i></a>
						<?php if( es_super_admin() && $u['rango'] !== '1' && $u['id'] !== $_SESSION['id'] ) : ?>
   							<a class="btn btn-danger btn-small" href="<?php echo url() . 'usuarios.php?accion=eliminar&id=' . $u['id'] ?>"
   								title="<?php _e("Eliminar") ?>"><i class="icon-trash"></i></a>
   						<?php endif ?>
   						<?php if( es_super_admin() &&  $u['estado'] == '1' && $u['rango'] !== '1' ) : ?>
						    <a class="btn btn-warning btn-small" title="<?php _e('Suspender') ?>" href="<?php echo url() . 'usuarios.php?accion=suspender&id=' . $u['id'] ?>"><i class="icon-ban-circle"></i></a>
						<?php elseif( es_super_admin() && $u['estado'] !== '1' && $u['rango'] !== '1' ) : ?>
							<a class="btn btn-inverse btn-small" title="<?php _e('Quitar suspensión') ?>" href="<?php echo url() . 'usuarios.php?accion=quitar_suspension&id=' . $u['id'] ?>"><i class="icon-ban-circle"></i></a>
						<?php endif ?>
					</a>
			<?php
		}
		?></center></td></table><?php
	}
}
construir( 'pies' );