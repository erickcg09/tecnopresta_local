
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
    let booGuardar = guardar();
    
    if (booGuardar) {
      let contenedorError = document.getElementById("mensaje");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                            '<strong>La información se registró satisfacoriamente</strong>' +
                            '</div>';
    }               
      return false;
  });

});

window.onload = function() {
  
  let id_activo;
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  id_activo = urlParams.get('alias_id');
  
  cargaDatosBd(id_activo);
      
  return false;

}

function validaFechas(prestamo_fechaRetiro,prestamo_fechaDevolucion) {

  let fechaInicio = new Date(prestamo_fechaRetiro);
  let fechaFin = new Date(prestamo_fechaDevolucion);

  if (fechaInicio > fechaFin) {
    return true;
  }

  return false;
}

function isValidDate(value) {
  var dateWrapper = new Date(value);
  return !isNaN(dateWrapper.getDate());
}

function guardar() {
  
  let prestamo_fechaRetiro = $('#fechaRetiro').val();
  let prestamo_fechaDevolucion = $('#fechaDevolución').val();

  if (isValidDate(prestamo_fechaRetiro)!==true) {

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                            '<strong>Error! Ingrese una fecha de retiro válida </strong>' +                          
                            '</div>';
    return false;
  }

  if (isValidDate(prestamo_fechaDevolucion)!==true) {

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                            '<strong>Error! Ingrese una fecha de devolución válida </strong>' +                          
                            '</div>';
    return false;
  }

  if (validaFechas(prestamo_fechaRetiro, prestamo_fechaDevolucion)) {

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                            '<strong>Error! Rango de fechas inválido. </strong>' +
                            'La fecha de retiro no puede ser mayor a la fecha de devolución'
                            '</div>';
    return false;
  }

  let arrayArticulos = []
  let checkboxes = document.querySelectorAll('input[type=checkbox]:checked')

  for (let i = 0; i < checkboxes.length; i++) {    
    arrayArticulos.push(checkboxes[i].getAttribute('data-id'))
  }

  ///console.log(arrayArticulos);

  if (arrayArticulos && arrayArticulos.length>0) {

    const formData = new FormData();    
    const json = JSON.stringify(arrayArticulos);
    formData.append('prestamo_fechaRetiro', prestamo_fechaRetiro);
    formData.append('prestamo_fechaDevolucion', prestamo_fechaDevolucion);
    formData.append('arrayArticulos', json);
    
    fetch('sql/insertPrestamoGestor.php', {
      method: 'POST', 
      body: formData,     
    }).then(function(response) {
  
      if(response.ok) {
                  
          response.text().then(function(data) {  
                 console.log(data);                 
          
        }).catch(function(error) {
                  
                  let contenedorError = document.getElementById("mensaje");
                  contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          'Intente de nuevo... no hubo respuesta del servidor MEP' +
                                          '</div>'; 
                })
  
      } else {
              
              let contenedorError = document.getElementById("mensaje");
              contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                      'No hay respuesta del servidor MEP. Verifique su conexión de internet ' +
                                      '</div>';
      }
  
    })
    .catch(function(error) {
      
            let contenedorError = document.getElementById("mensaje");
            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                    'Hubo un problema al guardar la información: ' + error.message +
                                    '</div>';        
    })
    .then();
  
  } else {

    let contenedorError = document.getElementById("mensaje");    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'Seleccione los artículos ...' +
                                    '</div>';    
  }


return true;
}

function cargaDatosBd(id) {

  fetch('sql/selectPrestamoGestor.php?' 
          + new URLSearchParams({alias_id: id,}))
    .then(function(response) {

    if(response.ok) {

      response.json().then(function(data) {  
          
        //console.log(data);
          cargaDatosPantalla(data);

        }).catch(function(error) {

                  let contenedorError = document.getElementById("mensaje");
                  contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          'No hay respuesta del servidor MEP. Verifique su conexión de internet ' + error.message +
                                          '</div>';
            });              


    } else {
            
            let contenedorError = document.getElementById("mensaje");           
            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No se pudo conectar con el servidor. Intente de nuevo.' +
                                    '</div>';
    }

  }).catch(function(error) {
    
          let contenedorError = document.getElementById("mensaje");         
          contenedorError.innerHTML='<div class="alert alert-danger">' +
                                  '<strong>Error! </strong>' +
                                      'Hubo un problema al conectar con el servidor: ' + error.message +
                                  '</div>';        
  }).then();

  return false;

}

function cargaDatosPantalla(rs) {

  $('#item').remove();    

  rs.forEach(obj => {

      //console.log(linkData);
      let plantilla = document.getElementById("plantilla");
      
      let row = document.createElement('div');
      row.className = "form-group row";
      row.id = "item";
      
      let colCheck = document.createElement('div');
      colCheck.className = "col-1";
      colCheck.innerHTML='<div class="form-check">'+
                            '<input class="form-check-input position-static" ' +
                            'type="checkbox" data-id="' + obj.id_activo  + '">' +
                          '</div>';

      let colNombre = document.createElement('div');
      colNombre.className = "col-4";
      let createATextNombre = document.createTextNode(obj.nombre);
      colNombre.appendChild(createATextNombre);
             
      // marca
      let colMarca = document.createElement('div');
      colMarca.className = "col";
      let createATextMarca = document.createTextNode(obj.marca);
      colMarca.appendChild(createATextMarca);      
          
      // numero_activo
      let colNumero = document.createElement('div');
      colNumero.className = "col";
      let createATextNumero = document.createTextNode(obj.numero_activo);
      colNumero.appendChild(createATextNumero);      
      
      row.appendChild(colCheck);
      row.appendChild(colNombre);
      row.appendChild(colMarca);
      row.appendChild(colNumero);
      plantilla.appendChild(row);            
 
    });      

    return false;
}

