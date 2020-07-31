
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

window.onload = function() {
  
  let id_activo;
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  id_activo = urlParams.get('alias_id');
  
  cargaDatosBd(id_activo);
      
  return false;

};

$("#btnGuardar").on("click", function(event) {
  event.preventDefault();            
    return false;
});
