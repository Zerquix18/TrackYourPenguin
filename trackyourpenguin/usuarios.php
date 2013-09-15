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
	$dnd = false;

$u = new extraer( $zerdb->usuarios, "*", $dnd );
$accion = isset($_GET['accion']) && is_string($_GET['accion']) ? $_GET['accion'] : '';
$post = ( 'POST' == $_SERVER['REQUEST_METHOD'] ); // <-- buscando enviar? aquí xdd...
switch( $accion ) {
	case "agregar":

	if( ! es_super_admin() )
		typ_die("Haciendo trampa, ¿eh?");

	construir( 'cabecera', 'Agregar usuario', 1 ); // 1 = true

	?>
	<h3>Agregar usuario</h3><a href="<?php echo url() ?>usuarios.php" class="btn btn-link pull-right">Volver al usuario &rarr;</a><hr>
	<?php
		if( $post ) {
			$nombre = @$zerdb->proteger( strtolower( $_POST['usuario']) );
			$clave = md5( @$zerdb->proteger( $_POST['clave'] ) );
			$email = @$zerdb->proteger( strtolower($_POST['email']) );
			$rango1 = is_numeric(@$_POST['rango']) ? (int) $_POST['rango'] : '';
			$rango2 = $u->rango;
			$args = ! comprobar_args( @$_POST['usuario'], @$_POST['clave'], @$_POST['email'], @$_POST['clave2'], @$_POST['rango']);
			$vacios = vacios( @$_POST['usuario'], @$_POST['clave'], @$_POST['email'], @$_POST['clave2'], @$_POST['rango'] );
			$estado = 1;
			$hash = '';
			if( $args ) {
				agregar_error("Haciendo trampa, ¿eh?");
			}elseif( $vacios ) {
				agregar_error("No puedes dejar datos vacíos");
			}elseif( comprobar_rangos($rango1, $rango2 ) ) {
				agregar_error("¿Haciendo trampa para obtener un mejor rango? " . $rango1 . ' - ' . $rango2);
			}elseif( ! preg_match('/^[A-Za-z0-9-_]{3,12}$/', $_POST['usuario'] ) ) {
				agregar_error("Pon un nombre de usuario válido, de 3 a 12 caracteres");
			}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
				agregar_error("El email ingresado no parece ser válido");
			}elseif( existe_usuario( strtolower( $_POST['usuario'] ) ) ) {
				agregar_error("El usuario ingresado ya existe");
			}elseif( existe_email( strtolower($_POST['email'] ) ) ) {
				agregar_error("El email ingresado ya existe");
			}elseif( $rango1 == 1) {
				agregar_error("No puedes ser super administrador");
			}else{
				$zerdb->insertar( $zerdb->usuarios, array($nombre, $clave, $email, $estado, $rango1, $hash) );
				agregar_info("El usuario ha sido actualizado", true, true );
				echo redireccion( url() . 'usuarios.php?id='.  $u->id, 2 );
			}
		}
	?>
<form class="form-horizontal" action="<?php echo url( true ) ?>" method="POST">
	<div class="control-group">
		<label class="control-label">
		Usuario
		</label>
		<div class="controls">
			<input type="text" name="usuario" <?php if($post) echo 'value="' . @$_POST['usuario'] . '"' ?> required="required"
			pattern="^[A-Za-z0-9-_]{3,12}$">
			<span class="help-block">El nombre de usuario</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			Clave
		</label>
		<div class="controls">
			<input type="password" name="clave" id="clave" required="required">
			<span class="help-block">La clave para el usuario</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		Confirmar clave
		</label>
		<div class="controls">
			<input type="password" name="clave2" id="clave2" required="required">
			<span class="help-block">Confirma la clave para el usuario</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		Email
		</label>
		<div class="controls">
			<input type="email" name="email" id="email" required="required" <?php if($post) echo 'value="'. @$_POST['email'] . '"' ?>>
			<span class="help-block">Email para el usuario</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		Rango
		</label>
		<div class="controls">
			<select name="rango">
			<?php if( es_super_admin() ) : ?>
				<option value="2">Administrador</option>
				<option value="3">Actualizador</option>
			<?php elseif( es_admin() ): ?>
				<option value="3">Actualizador</option>
			<?php endif ?>
			</select>
			<span class="help-block">¿Qué le vamos a permitir a este usuario?</span>
		</div>
	</div>
	<hr>
	<center><input type="submit" name="enviar" class="btn btn-primary text-center" value="Agregar" id="enviar"></center>
</form>
	<?php
	break;
	case "editar":
	if( ! isset($_GET['id']) || ! is_numeric( $_GET['id'] ) )
		typ_die("Necesito un ID correcto");

	$u = obt_id( $zerdb->proteger( $_GET['id'] ) );
	$usuario = obt_usuario_actual();

	if( ! $u || $u->nums == 0)
		typ_die("El usuario que especificas no existe");

	if( ! es_super_admin() && $_GET['id'] !== $_SESSION['id'] )
		typ_die("No tienes los premisos suficientes para acceder aquí");

	if( $u->rango <= $usuario->rango && $_GET['id'] !== $_SESSION['id'] && ! es_super_admin() )
		typ_die("No puedes editar a alguien que tenga tu mismo rango o mayor");

	construir( 'cabecera', sprintf("Editar el usuario %s", ucfirst($u->usuario) ), true ); ?>

<h3>Editar el usuario: <i><?php echo ucfirst( $u->usuario ) ?></i></h3>
<a href="<?php echo url() ?>usuarios.php?id=<?php echo $u->id ?>" class="btn btn-link pull-right">Volver al usuario&rarr;</a><hr>

	<?php
		if( $post ) {
			$nombre = @$zerdb->proteger( strtolower($_POST['usuario']) );
			$email = @$zerdb->proteger( strtolower($_POST['email']) );
			$rango2 = $u->rango;
			$rango = ( $u->id !== $_SESSION['id'] ) ? @$_POST['rango'] : $u->rango;
			$rango1 = is_numeric($rango) ? (int) $rango : '1';
			$args = ! comprobar_args( @$_POST['usuario'], @$_POST['email']);
			$vacios = vacios( @$_POST['usuario'], @$_POST['email'], $rango1 );
			$estado = 1;
			$hash = '';
			$usuario_ = sprintf(
					"SELECT * FROM usuarios WHERE usuario = '%s' AND email != '%s'",
					$nombre,
					$u->email
				);
			$email_ =  sprintf(
					"SELECT * FROM usuarios WHERE email = '%s' AND usuario != '%s'",
					$email,
					$u->usuario
				);
			$usuario_ = @mysql_num_rows( @$zerdb->query( $usuario_ ) );
			$email_ = @mysql_num_rows( @$zerdb->query( $email_ ) );
			if( $args ) {
				agregar_error("Haciendo trampa, ¿eh?", true, true);
			}elseif( $vacios ) {
				agregar_error("No puedes dejar datos vacíos", true, true);
			}elseif( isset($_POST['rango']) && is_string($_POST['rango'] && $_POST['rango'] > $u->rango) ) {
				agregar_error("¿Haciendo trampa para obtener un mejor rango?", true, true);
			}elseif( ! preg_match('/^[A-Za-z0-9-_]{3,12}$/', $_POST['usuario'] ) ) {
				agregar_error("Pon un nombre de usuario válido, de 3 a 12 caracteres", true, true);
			}elseif( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
				agregar_error("El email ingresado no parece ser válido", true, true);
			}elseif(  $usuario_ > 0 ) {
				agregar_error("El usuario ingresado ya existe", true, true);
			}elseif( $email_ > 0) {
				agregar_error("El email ingresado ya existe", true, true);
			}elseif( $rango == 1 && ! es_super_admin() ) {
				agregar_error("No puedes ser super administrador", true, true);
			}else{
				$zerdb->actualizar( $zerdb->usuarios,
						array("usuario" => $nombre, "email" => $email, "rango" => $rango),
						array("id" => $u->id)
					) or die($zerdb->ult_err);

				agregar_info("El usuario ha sido actualizado", true, true );
			}
		}
	?>
<form class="form-horizontal" action="<?php echo url( true ) ?>" method="POST">
	<div class="control-group">
		<label class="control-label">
		Usuario
		</label>
		<div class="controls">
			<input type="text" name="usuario" value="<?php if($post) echo @$_POST['usuario']; else echo ucwords($u->usuario) ?>" required="required"
			pattern="^[A-Za-z0-9-_]{3,12}$">
			<span class="help-block">El nombre de usuario</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		Email
		</label>
		<div class="controls">
			<input type="email" name="email" id="email" required="required" value="<?php if($post) echo @$_POST['email']; else echo strtolower($u->email) ?>">
			<span class="help-block">Email para el usuario</span>
		</div>
	</div>
<?php if( es_admin() ) : ?>
	<div class="control-group">
		<label class="control-label">
		Rango
		</label>
		<div class="controls">
		<?php if( $u->id !== $_SESSION['id'] ) : ?>
			<select name="rango">
			<?php if( $u->rango == '2' && es_super_admin() ) : ?>
				<option value="2">Administrador</option>
				<option value="3">Actualizador</option>
			<?php elseif( $u->rango == '3' && es_super_admin() ) : ?>
				<option value="3">Actualizador</option>
				<option value="2">Administrador</option>
			<?php elseif( $u->rango == '3' && es_admin() ) : ?>
				<option value="3">Actualizador</option>
			<?php endif ?>
			</select>
			<span class="help-block">¿Qué le vamos a permitir a este usuario?</span>
		<?php elseif( $u->id == $_SESSION['id'] ) : ?>
			<span class="help-inline">No puedes cambiar tu propio rango</span>
		<?php endif ?>
		</div>
	</div>
<?php endif ?>
	<hr>
	<center><input type="submit" name="enviar" class="btn btn-primary text-center" value="Agregar" id="enviar"></center>
</form>
<?php
break;
case "eliminar":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die("Debes especificar un ID");

	if( ! es_super_admin() )
		agregar_error("No tienes rango suficiente");

	$u = obt_id( $zerdb->proteger( $_GET['id'] ) );

	if( ! $u || $u->nums == "0")
		typ_die("El usuario seleccionado no existe");

	if( $u->id == $_SESSION['id'])
		typ_die("No puedes eliminar tu propio usuario");

	$usuario = obt_usuario_actual();

	construir('cabecera');

	eliminar_usuario( $_GET['id'] );

	agregar_info("El usuario seleccionado ha sido eliminado");

echo sprintf("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>", url() . 'usuarios.php');

	construir('pies');
break;
case "suspender":
	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die("Debes especificar un ID");

	if( ! es_super_admin() )
		agregar_error("No tienes rango suficiente");

	$u = obt_id( $zerdb->proteger( $_GET['id'] ) );

	if( ! $u || $u->nums == "0")
		typ_die("El usuario seleccionado no existe");

	if( $u->id == $_SESSION['id'])
		typ_die("No puedes suspender tu propio usuario");

	if( esta_suspendido($zerdb->proteger($u->id) ) )
		typ_die("Este usuario ya está suspendido");

	construir('cabecera', sprintf("Suspender al usuario %s", ucfirst($u->usuario) ), true );

	suspender_usuario( $u->id );

	agregar_info( sprintf("El usuario <b>%s</b> ha sido suspendido", ucfirst($u->usuario) ) );

	echo sprintf("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>", url() . 'usuarios.php');

	construir('pies');

	break;

	case "quitar_suspension":

	if( ! isset($_GET['id'] ) || ! is_numeric($_GET['id'] ) )
		typ_die("Debes especificar un ID");

	if( ! es_super_admin() )
		agregar_error("No tienes rango suficiente");

	$u = obt_id( $zerdb->proteger( $_GET['id'] ) );

	if( ! $u || $u->nums == "0")
		typ_die("El usuario seleccionado no existe");

	if( $u->id == $_SESSION['id'])
		typ_die("No puedes suspender tu propio usuario");

	if( ! esta_suspendido($u->id) )
		typ_die("Este usuario no se encuentra suspendido");

	construir('cabecera', sprintf("Quitar suspensión al usuario %s", ucfirst($u->usuario) ), true );

	quitar_suspension( $u->id );

	agregar_info( sprintf("Al usuario <b>%s</b> se le ha removido la suspensión", ucfirst($u->usuario) ) );

	echo sprintf("<ul class=\"pager\"><li class=\"previous\"><a href=\"%s\">&larr; Volver</a></li></ul>", url() . 'usuarios.php');

	construir('pies');

	break;
	
default:

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? true : false;

switch( $id ) {

	case true:

	$u = obt_id( $zerdb->proteger( $_GET['id'] ) );
	$usuario = obt_id( $_SESSION['id'] );

	if( ! $u || ! $u->nums > 0 || $u->id !== $_SESSION['id'] && ! es_admin() )
		typ_die("Este usuario no existe o tienes permiso de ver sus datos");

	construir( 'cabecera', ucfirst($u->usuario), true);
	?>
	<h3>Usuario: <?php echo sprintf( '<i>%s</i>', ucfirst( $u->usuario ) ) ?></h3>
	<a href="<?php echo url() ?>usuarios.php" class="btn btn-link pull-right">
		Volver al Inicio &rarr;
	</a><hr>
	<p><b>Usuario:</b>&nbsp;<i><?php echo ucfirst($u->usuario) ?></i></p>
	<p><b>Email:</b>&nbsp;<i><?php echo $u->email ?></i></p>
	<p><b>Estado:</b>&nbsp;<i><?php echo estado($u->estado) ?></i></p>
	<p><b>Rango:</b>&nbsp;<i><?php echo rango( $u->id ) ?></i></p>
	<?php
	break;
	case false:
	construir( 'cabecera', 'Usuarios', true );
	$q = mysql_query( $u->query );
	?>
	<h3> Usuarios </h3>
	<a href="<?php echo url() . 'usuarios.php?accion=agregar' ?>" class="btn btn-link pull-right">
		<i class="icon-plus"></i>&nbsp;Agregar nuevo</a><hr>
	<table class="table table-bordered table-hover">
		<tr>
			<th>Usuario</th>
			<th>Email</th>
			<th>Rango</th>
			<th>Estado</th>
			<th><center>#</center></th>
		</tr>
	<?php
		while($u = mysql_fetch_array($q) ) {
			?>
			<tr>
				<td><?php echo ucfirst($u['usuario']) ?></td>
				<td><?php echo $u['email'] ?></td>
				<td><?php echo rango( $u['id'] ) ?></td>
				<td><?php echo estado( $u['estado'] ) ?></td>
				<td>
					<div class="btn-group">
 						<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#">Acciones <span class="caret"></span></a>
						<ul class="dropdown-menu">
						<?php if( es_super_admin() || $u['id'] == $_SESSION['id'] ) : ?>
    						<li><a href="<?php echo url() . 'usuarios.php?accion=editar&id=' . $u['id'] ?>"
							title="Editar el usuario: <?php echo ucfirst($u['usuario']) ?>">
							<i class="icon-pencil icon-whit"></i>&nbsp; Editar</a></li>
						<?php endif ?>
						<?php if( es_super_admin() && $u['rango'] !== '1') : ?>
   							<li><a href="<?php echo url() . 'usuarios.php?accion=eliminar&id=' . $u['id'] ?>"
   								title="Eliminar el usuario: <?php echo ucfirst($u['usuario']) ?>"><i class="icon-trash"></i> Eliminar</a></li>
   						<?php endif ?>
   						<?php if( es_super_admin() && ! esta_suspendido( $u['id'] ) && $u['rango'] !== '1' ) : ?>
						    <li><a href="<?php echo url() . 'usuarios.php?accion=suspender&id=' . $u['id'] ?>"><i class="icon-ban-circle"></i> Suspender</a></li>
						<?php elseif( es_super_admin() && esta_suspendido($u['id']) && $u['rango'] !== '1' ) : ?>
							<li><a href="<?php echo url() . 'usuarios.php?accion=quitar_suspension&id=' . $u['id'] ?>"><i class="icon-ban-circle"></i> Quitar suspensión</a></li>
						<?php endif ?>
						  </ul>
						</div>
					</a>
			<?php
		}
		?></table><?php
	}
}
construir( 'pies' );