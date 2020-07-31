
 function cargaDatosBd() {
  
  fetch('sql/selectSolicitudGestor.php').then(function(response) {

    if(response.ok) {

      response.json().then(function(data) {  
          
          //console.log(data);
          cargaDatosPantalla(data);

        }).catch(function(error) {

                  let contenedorError = document.getElementById("contenedorError");
                  contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          'No hay respuesta del servidor MEP. Verifique su conexión de internet ' + error.message +
                                          '</div>';
            });              


    } else {
            
            let contenedorError = document.getElementById("contenedorError");           
            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No se pudo conectar con el servidor. Intente de nuevo.' +
                                    '</div>';
    }

  }).catch(function(error) {
    
          let contenedorError = document.getElementById("contenedorError");         
          contenedorError.innerHTML='<div class="alert alert-danger">' +
                                  '<strong>Error! </strong>' +
                                      'Hubo un problema al conectar con el servidor: ' + error.message +
                                  '</div>';        
  }).then();

  return false;

}

function cargaDatosPantalla(rs) {

  let dir = "formulario_prestamo.html"; //Página de Editar registro

  $('.card').remove();

  rs.forEach(obj => {

      let card = document.createElement('div');
      card.className = "card";
      
      let rownogutters = document.createElement('div');
      rownogutters.className = "row no-gutters";

      // Imagen
      let colmd4 = document.createElement('div');
      colmd4.className = "col-3";

      let cardimg = document.createElement('img');
      cardimg.className = "card-img";
      cardimg.alt = obj.activo_nombre;
      cardimg.src = './img/' + obj.alias_imagen;
      
      colmd4.appendChild(cardimg);
      rownogutters.appendChild(colmd4);//Imagen

      // Nombre
      let colmd8 = document.createElement('div');
      colmd8.className = "col-md-8";

      let cardbody = document.createElement('div');
      cardbody.className = "card-body";

      let h4 = document.createElement('h4');
      h4.className = "card-title";
      let createATextNombreh4 = document.createTextNode(obj.alias);
      h4.appendChild(createATextNombreh4);

      let h5 = document.createElement('h5');
      h5.className = "card-title";
      let createATextNombre = document.createTextNode("Cantidad:" + obj.solicitud_cantidad);
      h5.appendChild(createATextNombre);

      //Retiro
      let p = document.createElement('p');
      p.className = "card-title";
      let createATextDescripcion = document.createTextNode("Retiro:  " + obj.solicitud_fechaRetiro + " Devolución " + obj.solicitud_fechaDevolucion);
      p.appendChild(createATextDescripcion);

      //Programa
      let pP = document.createElement('p');
      pP.className = "text-muted";
      let createATextPrograma = document.createTextNode("Nombre del solicitante, destino y uso del equipo");
      pP.appendChild(createATextPrograma);
      
      //Boton Aceptar
      let botonEditar = document.createElement('a');
      botonEditar.className = "btn btn-dark";
      let createATextBoton = document.createTextNode("Aceptar");
      botonEditar.setAttribute('href', dir + "?alias_id=" + obj.alias_id);
      botonEditar.appendChild(createATextBoton);

      //Boton Rechazar
      let botonAceptar = document.createElement('a');
      botonAceptar.className = "btn btn-danger";
      let createATextBotonAceptar = document.createTextNode("Rechazar");
      botonAceptar.setAttribute('href', dir + "?id_activo=" + obj.alias_id);
      botonAceptar.appendChild(createATextBotonAceptar);
      
      cardbody.appendChild(h4);//Nombre
      cardbody.appendChild(h5);//Nombre
      cardbody.appendChild(p); //Retiro
      cardbody.appendChild(pP); //Programa
      cardbody.appendChild(botonEditar); //Boton Aceptar
      cardbody.appendChild(botonAceptar); //Boton Rechazar

      colmd8.appendChild(cardbody);
      rownogutters.appendChild(colmd8);      
      card.appendChild(rownogutters);      
      document.getElementById('colCards').appendChild(card);

    });
    
    return false;
}

window.onload = function() {
  
  cargaDatosBd();  
  return false;

};