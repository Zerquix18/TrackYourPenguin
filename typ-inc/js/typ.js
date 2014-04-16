function cambiar_modal( titulo, cuerpo, pies, opciones ) {
	titulo = (titulo || "TrackYourPenguin");
	cuerpo = (cuerpo || "One fine body...");
	pies = (pies || '<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>');
	opciones = (opciones || {'abrir' : true, "bcerrar" : false } );
	$("#typ > .modal-header").html(titulo);
	$("#typ > .modal-body").html(cuerpo);
	$("#typ > .modal-footer").html(pies);
	if( false == opciones['bcerrar'] )
		$("#typ > .modal-header button").hide();
	if( true == opciones['abrir'] )
		return $("#typ").modal('show');
	
	return true;
}

function cambiar_tema( tema ) {
	tema = (tema || "bootstrap");
	url_ = uri + "typ-inc/css/";
	$("#estilo").attr("href", url_ + tema + '.css');
	$("#estilo_r").attr("href", url_ + tema + '.min.css');
	var s = "i[class^='icon-']";
	if( tema == "cyborg" || tema == "slate") {
		$(s).addClass("icon-white");
	}else if( $(s).hasClass("icon-white") ) {
		$(s).removeClass("icon-white");
	}
	return true;
}

$(document).ready( function() {
	/** cambio de iconos a blanco dependiendo el tema **/
	var s = "i[class^='icon-']";
	if( _tema == "cyborg" || _tema == "slate") {
		$(s).addClass("icon-white");
	}else if( $(s).hasClass("icon-white") ) {
		$(s).removeClass("icon-white");
	}
	/*** cierra sesiones ***/
	setInterval( function() {
		$.ajax({
				url: uri + "typ-inc/ajax.php",
				data: {'accion' : 'sesion', 'href' : window.location.href },
				type: "POST",
				success: function( respuesta ) {
					respuesta = JSON.parse(respuesta);
					if( parseInt(respuesta.estado) !== 1 )
						return cambiar_modal('<h2>Error</h2>', '<strong>' + respuesta['mensaje'] + '</strong>', null, {'abrir' : true, 'bcerrar': false } );
				}
			})
	}, 3000);
	/*** tooltips **/
	$("a[title], img[title]").attr('rel', 'tooltip');
	tt = "a[rel='tooltip'], img[rel='tooltip']";
	if( $(tt).length ) {
		$(tt).tooltip({
			"title" : function() {
				return $(this).title()
			},
			"trigger" : "hover"
		});
	}
	/****/
});