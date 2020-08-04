var id_activo;
$(document).ready(function() {   

    $('#fechaRetiro').datepicker({
      locale: 'es-es',
      format: 'dd mm yyyy',
      uiLibrary: 'bootstrap5'
    });
  
    $('#fechaDevolución').datepicker({
      locale: 'es-es',
      format: 'dd mm yyyy',
      uiLibrary: 'bootstrap5'
    });
  
    $("#btnGuardar").on("click", function(event) {
      
      event.preventDefault();
      /* let booGuardar = guardar();
      
      if (booGuardar) {
        let contenedorError = document.getElementById("mensaje");
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                              '<strong>La información se registró satisfactoriamente</strong>' +
                              '</div>';
      } */               
        return false;
    });
  
  });

  window.onload = function() {
  
    //const queryString = window.location.search;
    //const urlParams = new URLSearchParams(queryString);
    //id_activo = urlParams.get('alias_id');
    //document.getElementById("txtnombre").focus();
    //cargaActivo(id_activo);
        
    return false;
  
  };
  
function validaFechas(prestamo_fechaRetiroY, 
    prestamo_fechaRetiroM, 
    prestamo_fechaRetiroD, 
    prestamo_fechaDevolucionY,
    prestamo_fechaDevolucionM,
    prestamo_fechaDevolucionD) {

        let fechaInicio = new Date(prestamo_fechaRetiroY, prestamo_fechaRetiroM, prestamo_fechaRetiroD);
        let fechaFin = new Date(prestamo_fechaDevolucionY, prestamo_fechaDevolucionM, prestamo_fechaDevolucionD);

        if (fechaInicio > fechaFin) {
        return true;
        }

        return false;
}

function isValidDate(valueY, valueM, valueD) {
        var dateWrapper = new Date(valueY, valueM, valueD);
        return !isNaN(dateWrapper.getDate());
}

function cargaActivo(id_activo) {

  $('.card').remove();
  
  $.getJSON("sql/selectActivoAliasIdGestor.php", { alias_id: id_activo }).done(
    function(data){             

    $.each(data, function(i, linkData) { 

      //console.log(linkData);
      var card = document.createElement('div');
      card.className = "card";
      
      var rownogutters = document.createElement('div');
      rownogutters.className = "row no-gutters";

      // Imagen
      var colmd4 = document.createElement('div');
      colmd4.className = "col-md-4";

      var cardimg = document.createElement('img');
      cardimg.className = "card-img";
      cardimg.style = "width: 8rem;";
      cardimg.src = './img/' + linkData.alias_imagen;
      
      colmd4.appendChild(cardimg);
      rownogutters.appendChild(colmd4);//Imagen

      // Nombre
      var colmd8 = document.createElement('div');
      colmd8.className = "col-md-8";

      var cardbody = document.createElement('div');
      cardbody.className = "card-body";

      var h5 = document.createElement('h5');
      h5.className = "card-title";
      var createATextNombre = document.createTextNode(linkData.alias);
      h5.appendChild(createATextNombre);
      h5.innerHTML = h5.innerHTML + '<span class="badge badge-pill badge-primary">' + linkData.disponible + '</span>';
      
      cardbody.appendChild(h5);//Nombre

      colmd8.appendChild(cardbody);
      rownogutters.appendChild(colmd8);      
      card.appendChild(rownogutters);      
      document.getElementById('colCards').appendChild(card);
 
    });
      
    }).fail(function(jqXHR, textStatus, error) {			
    console.log("Error de la aplicación: " + error);            
    });

    return false;

}

