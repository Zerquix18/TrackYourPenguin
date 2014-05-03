<?php
/**
*
* Archivo de parámetros
*
* Edita los parámetros de un tracker
*
* @author Zerquix18
*
**/

require_once( dirname( __FILE__ ) . '/typ-load.php' );

comprobar( false );

if( ! es_admin() )
	typ_die( __("Haciendo trampa, ¿eh?") );

if( ! isset($_GET['id'] ) || empty($_GET['id'] ) || ! is_numeric( $_GET['id']) )
	typ_die( __("Necesito un ID correcto para especificar") );


$t = obt_tracker( $zerdb->proteger( $_GET['id'] ) );

if( ! $t or ! $t->nums > 0 )
	typ_die( __("El tracker no existe") );

$p = array(
	"estado" => obt_parametros(1, $t->id),
	"servidor" => obt_parametros(2, $t->id),
	"sala" => obt_parametros(3, $t->id)
	);

$estado = $p['estado'];
$servidor = $p['servidor'];
$sala = $p['sala'];

function desactivar( $posicion ) {
	$comprobar = isset($_GET['posicion'] );
	if( ! $comprobar )
		return;
	$get = $_GET['posicion'];
	$devolver = 'class="disabled"';
	switch($posicion) {
		case "1":
		if( $get == "estado")
			echo $devolver;
		break;
		case "2":
		if( $get == "servidor")
			echo $devolver;
		break;
		case "3":
		if( $get == "sala")
			echo $devolver;
		break;
		default:
		return '';
	}
}

function navegacion() {
	if( ! isset($_GET['posicion'] ) ) :
		agregar_info( __("Debes elegir una posición válida para editar sus parámetros") );
		echo '<hr>';
	endif;
		?>
<ul class="nav nav-pills">
  		<li <?php desactivar( 1 ) ?>>
   			 <a href="<?php echo url() . 'parametros.php?id=' . $_GET['id'] . '&posicion=estado' ?>">Estado</a>
  		</li>
 	 	<li <?php desactivar( 2 ) ?>>
 	 		<a href="<?php echo url() . 'parametros.php?id=' . $_GET['id'] . '&posicion=servidor' ?>">Servidor</a>
 	 	</li>
  		<li <?php desactivar( 3 ) ?>>
  			<a href="<?php echo url() . 'parametros.php?id=' . $_GET['id'] . '&posicion=sala' ?>">Sala</a>
  		</li>
</ul>
		<?php
		$posiciones = array("estado", "servidor", "sala"); // validar, en orden
		if( ! isset($_GET['posicion'] ) or ! in_array($_GET['posicion'], $posiciones ) ) {
			construir( 'pies' );
			exit();
		}
}

construir( 'cabecera', sprintf( __('Editar parámetros del tracker: %s'), ucwords($t->personaje) ) );

?>
<h3><?php _e("Editar los parámetros del tracker ") ?><i><?php echo ucwords( $t->personaje ) ?></i></h3><hr>

<?php

navegacion();

if( $_GET['posicion'] == 'estado' ) :

	if( "POST" == $_SERVER['REQUEST_METHOD'] ) {
		$data = array(
				"x" => @$zerdb->proteger( $_POST['x'] ),
				"y" => @$zerdb->proteger( $_POST['y'] ),
				"angulo" => @$zerdb->proteger( $_POST['angulo'] ),
				"size" => @$zerdb->proteger( $_POST['size'] )
			);
		$actualizar = new actualizar_parametros( $t->id, 1, $data );
		if( $actualizar->comp_error ) :
			agregar_error( $actualizar->error );
		else:
			agregar_info( __("Parámetros actualizados. :)") );
			echo redireccion( url( true ),  3 );
		endif;
	}

?>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Tamaño del texto") ?>
		</label>
		<div class="controls">
			<input type="text" name="size" value="<?php echo $estado['size'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Tamaño en el que se escribirá el texto en la imagen") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Ángulo") ?>
		</label>
		<div class="controls">
			<input type="text" name="angulo" value="<?php echo $estado['angulo'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Ángulo en el que se escribirá el texto") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Posición X") ?>
		</label>
		<div class="controls">
			<input type="text" name="x" value="<?php echo $estado['x'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Pósición X de la imagen en donde se escribirá el texto") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Posición Y") ?>
		</label>
		<div class="controls">
			<input type="text" name="y" value="<?php echo $estado['y'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Posición Y de la imagen en donde se escribirá el texto") ?></small></span>
		</div>
	</div>
	<center><input type="submit" id="enviar_parametros" name="enviar_parametros" required="required" class="btn btn-primary"
		value="<?php _e("Actualizar parámetros") ?>">
</form>
<?php elseif( $_GET['posicion'] == 'servidor' ) :

	if( "POST" == $_SERVER['REQUEST_METHOD'] ) {
		$data = array(
				"x" => @$zerdb->proteger( $_POST['x'] ),
				"y" => @$zerdb->proteger( $_POST['y'] ),
				"angulo" => @$zerdb->proteger( $_POST['angulo'] ),
				"size" => @$zerdb->proteger( $_POST['size'] )
			);

		$actualizar = @new actualizar_parametros( $t->id, 2, $data ); // 2 =  'servidor'
		if( $actualizar->comp_error ) :
			agregar_error( $actualizar->error );
		else:
			agregar_info( __("Parámetros actualizados. :)") );
			echo redireccion( url( true ),  3 );
		endif;
	}

?>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Tamaño del texto") ?>
		</label>
		<div class="controls">
			<input type="text" name="size" value="<?php echo $servidor['size'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Tamaño en el que se escribirá el texto en la imagen") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Ángulo") ?>
		</label>
		<div class="controls">
			<input type="text" name="angulo" value="<?php echo $servidor['angulo'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Ángulo en el que se escribirá el texto") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Posición X") ?>
		</label>
		<div class="controls">
			<input type="text" name="x" value="<?php echo $servidor['x'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Pósición X de la imagen en donde se escribirá el texto") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Posición Y") ?>
		</label>
		<div class="controls">
			<input type="text" name="y" value="<?php echo $servidor['y'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Posición Y de la imagen en donde se escribirá el texto") ?></small></span>
		</div>
	</div>
	<center><input type="submit" id="enviar_parametros" required="required" class="btn btn-primary"
		value="<?php _e("Actualizar parámetros") ?>">
</form>

<?php elseif( $_GET['posicion'] == 'sala' ) :

	if( "POST" == $_SERVER['REQUEST_METHOD'] ) {
		$data = array(
				"x" => @$zerdb->proteger( $_POST['x'] ),
				"y" => @$zerdb->proteger( $_POST['y'] ),
				"angulo" => @$zerdb->proteger( $_POST['angulo'] ),
				"size" => @$zerdb->proteger( $_POST['size'] )
			);

		$actualizar = @new actualizar_parametros( $t->id, 3, $data ); // 3 = sala
		if( $actualizar->comp_error ) :
			agregar_error( $actualizar->error );
		else:
			agregar_info( __("Parámetros actualizados. :)") );
			echo redireccion( url( true ),  3 );
		endif;
	}

?>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Tamaño del texto") ?>
		</label>
		<div class="controls">
			<input type="text" name="size" value="<?php echo $sala['size'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Tamaño en el que se escribirá el texto en la imagen") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Ángulo") ?>
		</label>
		<div class="controls">
			<input type="text" name="angulo" value="<?php echo $sala['angulo'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Ángulo en el que se escribirá el texto") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Posición X") ?>
		</label>
		<div class="controls">
			<input type="text" name="x" value="<?php echo $sala['x'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Pósición X de la imagen en donde se escribirá el texto") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Posición Y") ?>
		</label>
		<div class="controls">
			<input type="text" name="y" value="<?php echo $sala['y'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Posición Y de la imagen en donde se escribirá el texto") ?></small></span>
		</div>
	</div>
	<center><input type="submit" id="enviar_parametros" required="required" class="btn btn-primary"
		value="<?php _e("Actualizar parámetros") ?>">
</form>
<?php 
endif;

?>
<script type="text/javascript">
<!--
function FindPosition(oElement)
{
  if(typeof( oElement.offsetParent ) != "undefined")
  {
    for(var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent)
    {
      posX += oElement.offsetLeft;
      posY += oElement.offsetTop;
    }
      return [ posX, posY ];
    }
    else
    {
      return [ oElement.x, oElement.y ];
    }
}
function GetCoordinates(e)
{
  var PosX = 0;
  var PosY = 0;
  var ImgPos;
  ImgPos = FindPosition(myImg);
  if (!e) var e = window.event;
  if (e.pageX || e.pageY)
  {
    PosX = e.pageX;
    PosY = e.pageY;
  }
  else if (e.clientX || e.clientY)
    {
      PosX = e.clientX + document.body.scrollLeft
        + document.documentElement.scrollLeft;
      PosY = e.clientY + document.body.scrollTop
        + document.documentElement.scrollTop;
    }
  PosX = PosX - ImgPos[0];
  PosY = PosY - ImgPos[1];
  document.getElementById("x_").innerHTML = "<?php _e('<b>Posición X</b>') ?>: " + PosX;
  document.getElementById("y_").innerHTML = "<?php _e('<b>Posición Y</b>') ?>: " + PosY;
}
//-->
</script>

<div id="obtparams" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php _e("Obtener parámetros X y Y") ?></h3>
  </div>
  <div class="modal-body">
    <p><?php _e("Obtén los parámetros X y Y de la imagen haciendo clic en donde quieres obtenerlos.") ?></p>
    <p><?php _e("Por ejemplo, en donde va el estado, haz clic y obtendrás el parámetro X y Y para añadir en el tracker.") ?></p>
	<p id="x_"></p>
	<p id="y_"></p>
	<img id="myImgId" alt="" src="<?php echo url() . IMG . $t->imgbg ?>"/>
<script type="text/javascript">
<!--
var myImg = document.getElementById("myImgId");
myImg.onmousedown = GetCoordinates;
//-->
</script>
  </div>
  <div class="modal-footer">
    <button class="btn btn-inverse" data-dismiss="modal" aria-hidden="true"><?php _e("Cerrar") ?></button>
    <img src="">
  </div>
</div>

<?php
construir( "pies" ); // this won't stop!