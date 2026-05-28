let codigoPresupuestario;

 window.onload = function() {
  
  let boologin = login();

  if (boologin) {
    
    cargaDatosBd();
  
  }  else {
   
    let contenedorError = document.getElementById("contenedorError");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesión ...' +
                                    '</div>';

  }

  return false;

  }

 function cargaDatosBd() {
    
    fetch('sql/selectSolicitudGestor.php?'
      + new URLSearchParams({codigo: codigoPresupuestario}))
      .then(function(response) {

    if(response.ok) {

      response.json().then(function(data) {
        
       //console.log(data);
        if (Object.keys(data).length>0) {

          cargaDatosPantalla(data);

        } else {
          
          // let almacenllenoTitulo = document.getElementById("almacenllenoTitulo");
          // almacenllenoTitulo.innerText = "Todas las incidencias de préstamo han sido revisadas";  
          document.getElementById("almacenlleno").src = './img/nosolicitudes.png';
          
        }
         
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

function login() {

  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');
    
  if (userData && userData.length>0) {

    let jsonData = [];

    jsonData = JSON.parse(userData);

    codigoPresupuestario = jsonData["CentrosEducativosDondeTrabaja"];

    //console.log(jsonData);

  } else {
                                
    return false;    

  }

  return true;

}

function muestraFecha(fecha) {
  

  let fechaY = fecha.substr(0, 4);
  let fechaM = fecha.substr(5, 2);
  let fechaD = fecha.substr(8, 2);

  let fechaconformato = [fechaD, fechaM, fechaY].join('/');
      
  return fechaconformato;

}

function muestraHora(hora) {
  

  let horaH = hora.substr(0, 2);
  let horaM = hora.substr(3, 2);
  

  let horaconformato = [horaH, horaM].join(':');
      
  return horaconformato;

}

function cargaDatosPantalla(rs) {

  let dir = "formulario_prestamo.html"; //Página de Editar registro

  $('.col-md-6').remove();  
  
  rs.forEach(obj => {

      let colCard = document.createElement('div');
      colCard.className = "col-md-6";

      let card = document.createElement('div');
      card.className = "card";
      
      let rownogutters = document.createElement('div');
      rownogutters.className = "row";
      
      let col = document.createElement('div');
      col.className = "col";

      let cardheader = document.createElement('div');
      cardheader.className = "card-header";

      let createTextcardheader = document.createTextNode(obj.solicitud_nombre_funcionario);
      cardheader.appendChild(createTextcardheader);

      let cardbody = document.createElement('div');
      cardbody.className = "card-body";

      /* */
      let filaRetiro = document.createElement('div');
      filaRetiro.className = "row";

      let colTituloRetiro = document.createElement('div');
      colTituloRetiro.className = "col";

      let h6tituloRetiro = document.createElement('h6');
      h6tituloRetiro.className = "card-title";
      let createTextTituloRetiro = document.createTextNode("Fecha y hora de retiro");
      h6tituloRetiro.appendChild(createTextTituloRetiro);

      colTituloRetiro.appendChild(h6tituloRetiro);
      filaRetiro.appendChild(colTituloRetiro);

      let colFechaRetiro = document.createElement('div');
      colFechaRetiro.className = "col";

      let h6fechaRetiro = document.createElement('h6');
      h6fechaRetiro.className = "card-title";
      let createTextfechaRetiro = document.createTextNode(muestraFecha(obj.solicitud_fechaRetiro) + " " + muestraHora(obj.solicitud_horaRetiro));
      h6fechaRetiro.appendChild(createTextfechaRetiro);
           
      colFechaRetiro.appendChild(h6fechaRetiro);
      filaRetiro.appendChild(colFechaRetiro);

      let filaDevolucion = document.createElement('div');
      filaDevolucion.className = "row";

      let colTituloDevolucion = document.createElement('div');
      colTituloDevolucion.className = "col";

      let h6tituloDevolucion = document.createElement('h6');
      h6tituloDevolucion.className = "card-title";
      let createTextTituloDevolucion = document.createTextNode("Fecha y hora de devolucion");
      h6tituloDevolucion.appendChild(createTextTituloDevolucion);

      colTituloDevolucion.appendChild(h6tituloDevolucion);
      filaDevolucion.appendChild(colTituloDevolucion);

      let colFechaDevolucion = document.createElement('div');
      colFechaDevolucion.className = "col";

      let h6fechaDevolucion = document.createElement('h6');
      h6fechaDevolucion.className = "card-title";
      let createTextfechaDevolucion = document.createTextNode(muestraFecha(obj.solicitud_fechaDevolucion) + " " + muestraHora(obj.solicitud_horaDevolucion));
      h6fechaDevolucion.appendChild(createTextfechaDevolucion);
           
      colFechaDevolucion.appendChild(h6fechaDevolucion);
      filaDevolucion.appendChild(colFechaDevolucion);

     /* */
    
      let psolicitud_uso = document.createElement('p');
      psolicitud_uso.className = "card-text";
      let createTextsolicitud_uso = document.createTextNode(obj.solicitud_uso);
      psolicitud_uso.appendChild(createTextsolicitud_uso);
    
      let botonAceptar = document.createElement('a');
      botonAceptar.className = "btn btn-dark";
      let createATextbotonAceptar = document.createTextNode("Tramitar solicitud");
      botonAceptar.setAttribute('href', dir + "?solicitud_Id=" + obj.solicitud_Id);
      botonAceptar.appendChild(createATextbotonAceptar);
                 
      let rowDetalle = document.createElement('div');
      rowDetalle.className = "row";
                          
      cardbody.appendChild(filaRetiro);      
      cardbody.appendChild(filaDevolucion);
      cardbody.appendChild(psolicitud_uso);      
      cardbody.appendChild(botonAceptar);

      col.appendChild(cardheader);
      col.appendChild(cardbody);      
      rownogutters.appendChild(col);

      card.appendChild(rownogutters);
          
      fetch('sql/selectSolicitudDetalleGestor.php?'
      + new URLSearchParams({solicitud_Id: obj.solicitud_Id}))
        .then(function(response) {
                if(response.ok) {
                    response.json().then(function(data) {
                      //console.log(data);
                      let rsDetalle = [];
                      rsDetalle = data;

                      rsDetalle.forEach(objdata => {

                        let colDetalle = document.createElement('div');
                        colDetalle.className = "col-md-6";

                        let cardimg = document.createElement('img');
                        cardimg.className = "card-img";     
                        cardimg.src = './img/alias/' + objdata.alias_imagen;                         
                
                        let contador = document.createElement('span');
                        contador.className = "badge badge-pill badge-info";
                        let createTextcontador = document.createTextNode(objdata.solicitud_detalle_cantidad);
                        contador.appendChild(createTextcontador);
                
                        let alias = document.createElement('h5');
                        alias.className = "font-italic";
                        let createTextalias = document.createTextNode(objdata.alias);
                        alias.appendChild(createTextalias);
                
                        colDetalle.appendChild(cardimg);
                        colDetalle.appendChild(contador);
                        colDetalle.appendChild(alias);
                                               
                        rowDetalle.appendChild(colDetalle); 
                        
                      });                                                               
                    }).catch(function(error) {
                });                          
        }}).catch(function(error) {          
      }).then();

      fetch('sql/selectSolicitudDetalleActivosGestor.php?'
      + new URLSearchParams({solicitud_Id: obj.solicitud_Id}))
        .then(function(response) {
                if(response.ok) {
                    response.json().then(function(data) {
                      //console.log(data);
                      let rsDetalle = [];
                      rsDetalle = data;

                      rsDetalle.forEach(objdata => {

                        let colDetalle = document.createElement('div');
                        colDetalle.className = "col-md-6";

                        let cardimg = document.createElement('img');
                        cardimg.className = "card-img";     
                        cardimg.src = './img/' + objdata.imagen;                         
                                
                        let alias = document.createElement('h5');
                        alias.className = "font-italic";
                        let createTextalias = document.createTextNode(objdata.clase + " " + objdata.marca + " " + objdata.modelo + " " + objdata.placa + " " + objdata.numero_activo);
                        alias.appendChild(createTextalias);
                
                        colDetalle.appendChild(cardimg);
                        colDetalle.appendChild(alias);
                                               
                        rowDetalle.appendChild(colDetalle); 
                        
                      });                                                               
                    }).catch(function(error) {
                });                          
        }}).catch(function(error) {          
      }).then();

      card.appendChild(rowDetalle);
      colCard.appendChild(card);      
                      
      document.getElementById('fila').appendChild(colCard);

    });
    
    return false;
}