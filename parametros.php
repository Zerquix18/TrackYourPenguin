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
	typ_die( __("Cheatin', uh?!") );

if( ! isset($_GET['id'] ) || empty($_GET['id'] ) || ! is_numeric( $_GET['id']) )
	typ_die( __("I need a correct ID") );

$t = obt_tracker($_GET['id']);

if( false == $t )
	typ_die( __("The tracker doesn't exist.") );

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
		agregar_info( __("You must select a valid position to edit its parameters.") );
		echo '<hr>';
	endif;
?>
<ul class="nav nav-pills">
  		<li <?php desactivar( 1 ) ?>>
   			 <a href="<?php echo url() . 'parametros.php?id=' . $_GET['id'] . '&posicion=estado' ?>"><?php _e('Status') ?></a>
  		</li>
 	 	<li <?php desactivar( 2 ) ?>>
 	 		<a href="<?php echo url() . 'parametros.php?id=' . $_GET['id'] . '&posicion=servidor' ?>"><?php _e('Server') ?></a>
 	 	</li>
  		<li <?php desactivar( 3 ) ?>>
  			<a href="<?php echo url() . 'parametros.php?id=' . $_GET['id'] . '&posicion=sala' ?>"><?php _e('Room') ?></a>
  		</li>
</ul>
		<?php
		$posiciones = array("estado", "servidor", "sala"); // validar, en orden
		if( ! isset($_GET['posicion'] ) or ! in_array($_GET['posicion'], $posiciones ) ) {
			construir( 'pies' );
			exit();
		}
}
construir( 'cabecera', sprintf( __("Edit %s's tracker parameters"), ucwords($t->personaje) ) );
?>
<h3><?php _e( sprintf("Edit %s's tracker parameters"), ucwords( $t->personaje ) ) ?></h3><hr>
<?php
navegacion();
if( 'estado' == $_GET['posicion'] ) :
	if( "POST" == $_SERVER['REQUEST_METHOD'] ) {
		$data = array(
				"x" => @$zerdb->real_escape( $_POST['x'] ),
				"y" => @$zerdb->real_escape( $_POST['y'] ),
				"angulo" => @$zerdb->real_escape( $_POST['angulo'] ),
				"size" => @$zerdb->real_escape( $_POST['size'] )
			);
		$actualizar = new actualizar_parametros( $t->id, 1, $data );
		if( $actualizar->comp_error ) :
			agregar_error( $actualizar->error );
		else:
			agregar_info( __("Parameters updated. :)") );
			echo redireccion( url( true ),  3 );
		endif;
	}

?>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Text size") ?>
		</label>
		<div class="controls">
			<input type="text" name="size" value="<?php echo $estado['size'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Size of the text which will be written in the image") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Angle") ?>
		</label>
		<div class="controls">
			<input type="text" name="angulo" value="<?php echo $estado['angulo'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Angle in which the text will be written") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("X position") ?>
		</label>
		<div class="controls">
			<input type="text" name="x" value="<?php echo $estado['x'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("X position where the text will be written.") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Y position") ?>
		</label>
		<div class="controls">
			<input type="text" name="y" value="<?php echo $estado['y'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Y position where the text will be written") ?></small></span>
		</div>
	</div>
	<center><input type="submit" id="enviar_parametros" name="enviar_parametros" required="required" class="btn btn-primary"
		value="<?php _e("Update parameters") ?>">
</form>
<?php elseif( 'servidor' == $_GET['posicion'] ) :
	if( "POST" == $_SERVER['REQUEST_METHOD'] ) {
		$data = array(
				"x" => @$zerdb->real_escape( $_POST['x'] ),
				"y" => @$zerdb->real_escape( $_POST['y'] ),
				"angulo" => @$zerdb->real_escape( $_POST['angulo'] ),
				"size" => @$zerdb->real_escape( $_POST['size'] )
			);

		$actualizar = @new actualizar_parametros( $t->id, 2, $data ); // 2 =  'servidor'
		if( $actualizar->comp_error ) :
			agregar_error( $actualizar->error );
		else:
			agregar_info( __("Parameters updated :)") );
			echo redireccion( url( true ),  3 );
		endif;
	}
?>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Text size") ?>
		</label>
		<div class="controls">
			<input type="text" name="size" value="<?php echo $servidor['size'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Size of the text which will be written in the image") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Angle") ?>
		</label>
		<div class="controls">
			<input type="text" name="angulo" value="<?php echo $servidor['angulo'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Angle in which the text will be written") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("X position") ?>
		</label>
		<div class="controls">
			<input type="text" name="x" value="<?php echo $servidor['x'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("X position where the text will be written.") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Y position") ?>
		</label>
		<div class="controls">
			<input type="text" name="y" value="<?php echo $servidor['y'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Y position where the text will be written") ?></small></span>
		</div>
	</div>
	<center><input type="submit" id="enviar_parametros" required="required" class="btn btn-primary"
		value="<?php _e("Update parameters.") ?>">
</form>

<?php elseif( 'sala' == $_GET['posicion'] ) :

	if( "POST" == $_SERVER['REQUEST_METHOD'] ) {
		$data = array(
				"x" => @$zerdb->real_escape( $_POST['x'] ),
				"y" => @$zerdb->real_escape( $_POST['y'] ),
				"angulo" => @$zerdb->real_escape( $_POST['angulo'] ),
				"size" => @$zerdb->real_escape( $_POST['size'] )
			);

		$actualizar = @new actualizar_parametros( $t->id, 3, $data ); // 3 = sala
		if( $actualizar->comp_error ) :
			agregar_error( $actualizar->error );
		else:
			agregar_info( __("Parameters updated. :)") );
			echo redireccion( url( true ),  3 );
		endif;
	}

?>
<form action="<?php echo url( true ) ?>" method="POST" class="form-horizontal">
	<div class="control-group">
		<label class="control-label">
			<?php _e("Text size") ?>
		</label>
		<div class="controls">
			<input type="text" name="size" value="<?php echo $sala['size'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Size of the text which will be written in the image") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
		<?php _e("Angle") ?>
		</label>
		<div class="controls">
			<input type="text" name="angulo" value="<?php echo $sala['angulo'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Angle in which the text will be written") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("X position") ?>
		</label>
		<div class="controls">
			<input type="text" name="x" value="<?php echo $sala['x'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("X position where the text will be written.") ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">
			<?php _e("Y position") ?>
		</label>
		<div class="controls">
			<input type="text" name="y" value="<?php echo $sala['y'] ?>" required="required" pattern="^[\d]+$" class="input-mini">
			<span class="help-inline"><small><?php _e("Y position where the text will be written") ?></small></span>
		</div>
	</div>
	<center><input type="submit" id="enviar_parametros" required="required" class="btn btn-primary"
		value="<?php _e("Update parameters.") ?>">
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
  $("#y_").html("<?php _e('<strong>X position</strong>') ?>: " + PosX);
  $("#x_").html("<?php _e('<strong>Y position</strong>') ?>: " + PosY);
  $("input[name='x']").val(PosX);
  $("input[name='y']").val(PosY);
}
//-->
</script>
<div id="obtparams" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php _e("Get Y & X parameters") ?></h3>
  </div>
  <div class="modal-body">
    <p><?php _e("Get the X & Y paramters by clicking in a part of the image.") ?></p>
    <p><?php _e('Once you click, you can close this and click "Update parameters" to update them.') ?></p>
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
    <button class="btn btn-inverse" data-dismiss="modal" aria-hidden="true"><?php _e("Close") ?></button>
    <img src="">
  </div>
</div>
<?php
construir( "pies" ); // this won't stop!