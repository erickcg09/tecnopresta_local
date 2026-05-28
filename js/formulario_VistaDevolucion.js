let codigoPresupuestario;

$( "#chkAlerta" ).click(function() {
  $( "#contenedorObservacion" ).toggle( "fast", function() {
    // Animation complete.
    
  });
});
 
window.onload = function() {

  let boologin = login();

  if (boologin) {
    
    cargaDatosBd();

  }  else {
   
    let contenedorError = document.getElementById("mensaje");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesi\u00f3n ...' +
                                    '</div>';

  }
          
  return false;

};

function login() {

  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');
    
  if (userData && userData.length>0) {

    let nombre = document.getElementById("nombre");
    let codigo = document.getElementById("codigo");
    let jsonData = [];

    jsonData = JSON.parse(userData);

    // nombre.innerText = jsonData["Nombre"] + " " + jsonData["Apellido1"] + " " + jsonData["Apellido2"];
    // codigo.innerText = jsonData["Dependencia"];
    codigoPresupuestario = jsonData["CentrosEducativosDondeTrabaja"];   
    
    //console.log(jsonData);

  } else {
                                
    return false;    

  }

  return true;

}

function cargaDatosBd() {

  fetch('sql/selectVistaDevolucionGestor.php?' 
          + new URLSearchParams({codigo: codigoPresupuestario}))
    .then(function(response) {
  
    if(response.ok) {

      response.json().then(function(data) {
          
        //console.log(data);
        if (Object.keys(data).length>0) {

        cargaDatosPantalla(data);

      } else {
            
        document.getElementById("almacenlleno").src = './img/Almacenlleno-01.png';
        
      }
         
        }).catch(function(error) {

                  let contenedorError = document.getElementById("mensaje");
                  contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          'No hay respuesta del servidor MEP. Verifique su conexi\u00f3n de internet ' + error.message +
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

  let dir = "formulario_devolucion.html"; //Página de Editar regist

  $('.col-md-6').remove();
  
  rs.forEach(obj => {
 
     //console.log(obj);
      //let colCard = document.createElement('div');
      //colCard.className = "col-md-6";

      let card = document.createElement('div');
      card.className = "card col-md-6";
      
      let rownogutters = document.createElement('div');
      rownogutters.className = "row";
      
      let col = document.createElement('div');
      col.className = "col";

      let cardheader = document.createElement('div');
      cardheader.className = "card-header";

      let createTextcardheader = document.createTextNode(obj.prestamo_nombre_solicitante);
      cardheader.appendChild(createTextcardheader);

      let cardbody = document.createElement('div');
      cardbody.className = "card-body";

      let h6fechaRetiro = document.createElement('h6');
      h6fechaRetiro.className = "card-title";

      let h6tituloRetiro = document.createElement('h6');
      h6tituloRetiro.className = "card-title";

      let rsfechaRetiro = obj.prestamo_fechaRetiro;
      let prestamo_fechaRetiroY = rsfechaRetiro.substr(0, 4);
      let prestamo_fechaRetiroM = rsfechaRetiro.substr(5, 2);
      let prestamo_fechaRetiroD = rsfechaRetiro.substr(8, 2);
  
      let prestamo_fechaRetiro = prestamo_fechaRetiroD + "/" + prestamo_fechaRetiroM + "/"  + prestamo_fechaRetiroY;
  
      let rshoraRetiro = obj.prestamo_horaRetiro;
      let prestamo_horaRetiroH = rshoraRetiro.substr(0, 2);
      let prestamo_horaRetiroM = rshoraRetiro.substr(3, 2);
  
      let prestamo_horaRetiro = prestamo_horaRetiroH + ":" + prestamo_horaRetiroM;
  
      let filaRetiro = document.createElement('div');
      filaRetiro.className = "row";

      let createTextTituloRetiro = document.createTextNode("Fecha y hora de retiro");
      h6tituloRetiro.appendChild(createTextTituloRetiro);

      let colTituloRetiro = document.createElement('div');
      colTituloRetiro.className = "col";

      colTituloRetiro.appendChild(h6tituloRetiro);
      filaRetiro.appendChild(colTituloRetiro);

      let colFechaRetiro = document.createElement('div');
      colFechaRetiro.className = "col";

      let createTextfechaRetiro = document.createTextNode(prestamo_fechaRetiro + " " + prestamo_horaRetiro);
      h6fechaRetiro.appendChild(createTextfechaRetiro);

      colFechaRetiro.appendChild(h6fechaRetiro);
      filaRetiro.appendChild(colFechaRetiro);
           
      let filaDevolucion = document.createElement('div');
      filaDevolucion.className = "row";

      let coltituloDevolucion = document.createElement('div');
      coltituloDevolucion.className = "col";
      
      let h6solicitud_fechaDevolucion = document.createElement('h6');
      h6solicitud_fechaDevolucion.className = "card-title";

      let h6titulofechaDevolucion = document.createElement('h6');
      h6titulofechaDevolucion.className = "card-title";

      let createTextTituloDevolucion = document.createTextNode("Fecha y hora de devolución");
      h6titulofechaDevolucion.appendChild(createTextTituloDevolucion);

      coltituloDevolucion.appendChild(h6titulofechaDevolucion);

      filaDevolucion.appendChild(coltituloDevolucion);

      let rsfechaDevolucion = obj.prestamo_fechaDevolucion;
      let prestamo_fechaDevolucionY = rsfechaDevolucion.substr(0, 4);
      let prestamo_fechaDevolucionM = rsfechaDevolucion.substr(5, 2);
      let prestamo_fechaDevolucionD = rsfechaDevolucion.substr(8, 2);
  
      let prestamo_fechaDevolucion = prestamo_fechaDevolucionD + "/" + prestamo_fechaDevolucionM + "/"  + prestamo_fechaDevolucionY;
  
      let rshoraDevolucion = obj.prestamo_horaDevolucion;
      let prestamo_horaDevolucionH = rshoraDevolucion.substr(0, 2);
      let prestamo_horaDevolucionM = rshoraDevolucion.substr(3, 2);
  
      let prestamo_horaDevolucion = prestamo_horaDevolucionH + ":" + prestamo_horaDevolucionM;

      let colfechaDevolucion = document.createElement('div');
      colfechaDevolucion.className = "col";

      let createTexth6solicitud_fechaDevolucion = document.createTextNode(prestamo_fechaDevolucion + " " + prestamo_horaDevolucion);
      h6solicitud_fechaDevolucion.appendChild(createTexth6solicitud_fechaDevolucion);
      
      colfechaDevolucion.appendChild(h6solicitud_fechaDevolucion);
      filaDevolucion.appendChild(colfechaDevolucion);
      
      let botonAceptar = document.createElement('a');
      botonAceptar.className = "btn btn-dark";
      let createATextbotonAceptar = document.createTextNode("Recibir equipo");
      botonAceptar.setAttribute('href', dir + "?prestamo_Id=" + obj.prestamo_Id);
      botonAceptar.appendChild(createATextbotonAceptar);
            
      let rowDetalle = document.createElement('div');
      rowDetalle.className = "row";
                          
      cardbody.appendChild(filaRetiro);      
      cardbody.appendChild(filaDevolucion);
      cardbody.appendChild(botonAceptar);

      col.appendChild(cardheader);
      col.appendChild(cardbody);      
      rownogutters.appendChild(col);

      card.appendChild(rownogutters);
          
/*    fetch('sql/selectDevolucionDetalleGestor.php?'
      + new URLSearchParams({prestamo_Id: obj.prestamo_Id, codigo: codigoPresupuestario}))
        .then(function(response) {
                if(response.ok) {
                    response.json().then(function(data) {
                      //console.log(data);
                      let rsDetalle = [];
                      rsDetalle = data;

                      rsDetalle.forEach(objdata => {

                        let colDetalle = document.createElement('form');
                        colDetalle.className = "row form-inline";

                        let colImagen = document.createElement('div');
                        colImagen.className = "col";

                        let cardimg = document.createElement('img');
                        cardimg.className = "card-img";     
                        cardimg.src = './img/alias/' + objdata.alias_imagen;
                        
                        colImagen.appendChild(cardimg);
                        
                        let colAlias = document.createElement('div');
                        colAlias.className = "col";

                        let alias = document.createElement('span');
                        alias.className = "font-italic";
                        let createTextalias = document.createTextNode(objdata.alias);
                        alias.appendChild(createTextalias);

                        colAlias.appendChild(alias);

                        let colModelo = document.createElement('div');
                        colModelo.className = "col";

                        let modelo = document.createElement('span');
                        modelo.className = "font-italic";
                        let createTextmodelo = document.createTextNode(objdata.modelo);
                        modelo.appendChild(createTextmodelo);

                        colModelo.appendChild(modelo);

                        let colMarca = document.createElement('div');
                        colMarca.className = "col";

                        let marca = document.createElement('span');
                        marca.className = "font-italic";
                        let createTextmarca = document.createTextNode(objdata.marca);
                        marca.appendChild(createTextmarca);

                        colMarca.appendChild(marca);
                
                        colDetalle.appendChild(colImagen);
                        colDetalle.appendChild(colAlias);
                        colDetalle.appendChild(colModelo);
                        colDetalle.appendChild(colMarca);

                        rowDetalle.appendChild(colDetalle); 
                        
                      });                                                               
                    }).catch(function(error) {
                      console.log(error);
                });                          
        }}).catch(function(error) {
          console.log(error);          
      }).then();
 */

      fetch('sql/selectDevolucionDetalleActivosGestor.php?'
      + new URLSearchParams({prestamo_Id: obj.prestamo_Id, codigo: codigoPresupuestario}))
        .then(function(response) {
                if(response.ok) {
                    response.json().then(function(data) {
                      //console.log(data);
                      let rsDetalle = [];
                      rsDetalle = data;

                      rsDetalle.forEach(objdata => {

                        let colDetalle = document.createElement('form');
                        colDetalle.className = "row form-inline";

                        let colImagen = document.createElement('div');
                        colImagen.className = "col";

                        let cardimg = document.createElement('img');
                        cardimg.className = "card-img";     
                        cardimg.src = './img/' + objdata.imagen;
                        
                        colImagen.appendChild(cardimg);
                        
                        let colAlias = document.createElement('div');
                        colAlias.className = "col";

                        let alias = document.createElement('span');
                        alias.className = "font-italic";
                        let createTextalias = document.createTextNode(objdata.clase);
                        alias.appendChild(createTextalias);

                        colAlias.appendChild(alias);

                        let colModelo = document.createElement('div');
                        colModelo.className = "col";

                        let modelo = document.createElement('span');
                        modelo.className = "font-italic";
                        let createTextmodelo = document.createTextNode(objdata.modelo);
                        modelo.appendChild(createTextmodelo);

                        colModelo.appendChild(modelo);

                        let colMarca = document.createElement('div');
                        colMarca.className = "col";

                        let marca = document.createElement('span');
                        marca.className = "font-italic";
                        let createTextmarca = document.createTextNode(objdata.marca);
                        marca.appendChild(createTextmarca);

                        colMarca.appendChild(marca);

                        let colPlaca = document.createElement('div');
                        colPlaca.className = "col";

                        let placa = document.createElement('span');
                        placa.className = "font-italic";
                        let createTextplaca = document.createTextNode(objdata.placa);
                        placa.appendChild(createTextplaca);

                        colPlaca.appendChild(placa);

                        let colNumeroActivo = document.createElement('div');
                        colNumeroActivo.className = "col";

                        let numero_activo = document.createElement('span');
                        numero_activo.className = "font-italic";
                        let createTextNumeroActivo = document.createTextNode(objdata.numero_activo);
                        numero_activo.appendChild(createTextNumeroActivo);

                        colNumeroActivo.appendChild(numero_activo);
                
                        colDetalle.appendChild(colImagen);
                        colDetalle.appendChild(colAlias);
                        colDetalle.appendChild(colModelo);
                        colDetalle.appendChild(colMarca);
                        colDetalle.appendChild(colPlaca);
                        colDetalle.appendChild(colNumeroActivo);

                        rowDetalle.appendChild(colDetalle); 
                        
                      });                                                               
                    }).catch(function(error) {
                      console.log(error);
                });                          
        }}).catch(function(error) {
          console.log(error);          
      }).then();

      card.appendChild(rowDetalle);
      //colCard.appendChild(card);      
                      
      document.getElementById('fila').appendChild(card);

    });

  return false;

}
