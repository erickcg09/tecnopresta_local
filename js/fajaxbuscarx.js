function buscar_datos(consulta){
	$.ajax({
	    url: 'buscarx.php',
	    type: 'POST',
	    dataType: 'html',
	    data: {consulta: consulta},
	    beforeSend: function() {
	        $("#datos").html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div>');
	    }
	})
	.done(function(respuesta) {
	    $("#datos").html(respuesta);
	})
	.fail(function() {
	    $("#datos").html("Error al cargar los datos.");
	    console.log("error");
	});
}

$(document).on('keyup', '#caja_busqueda', function(){
	var valor = $(this).val();
	if(valor != ""){
		buscar_datos(valor);
	} else {
		buscar_datos();
	}
});
