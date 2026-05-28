let cantidad = 0;
let txtBuscar = document.getElementById("txtBuscar");
let codigoPresupuestario;

window.onload = function() {

  let boologin = login();

  if (boologin==false) {

    let contenedorError = document.getElementById("mensaje");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesión ...' +
                                    '</div>';
    return false;                                
  }

  let buscarNombre = "";
  buscarNombre = window.sessionStorage.getItem('buscarNombre');
  if (buscarNombre != "") {
    $('#txtBuscar').val(buscarNombre);
    window.sessionStorage.setItem('buscarNombre',"");//limpia la variable 
    //para que no quede con el valor de busqueda
    
  }
  
  muestraContador();  
  buscar();
      
  return false;

}

function getval(sel){             
    //alert(sel);
    cantidad = sel;           
}

txtBuscar.addEventListener("keypress", 
function(e) {
  if(e.keyCode == 13) {
    e.preventDefault();
    buscar();
    return false;
  }
});

txtBuscar.addEventListener("keydown", 
function(e) {
  
  let allowedCode = [8, 13, 32, 37, 39, 44, 45, 46, 95];
  let charCode = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode :
      ((e.which) ? e.which : 0));
   if (charCode > 31 && (charCode < 64 || charCode > 90) &&
    (charCode < 97 || charCode > 122) &&
    (charCode < 48 || charCode > 57) &&
    (allowedCode.indexOf(charCode) == -1)) {
      //console.log(e.which);
      e.preventDefault();
      return false;            
  }
    
});

txtBuscar.addEventListener('keyup', 
  function(e) {
  
    let allowedCode = [8, 13, 32, 37, 39, 44, 45, 46, 95];
    let charCode = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode :
        ((e.which) ? e.which : 0));
    if (charCode > 31 && (charCode < 64 || charCode > 90) &&
      (charCode < 97 || charCode > 122) &&
      (charCode < 48 || charCode > 57) &&
      (allowedCode.indexOf(charCode) == -1)) {
        //console.log(e.which);
        e.preventDefault();
        return false;            
    }

  if(charCode == 37 || charCode == 39) {
    e.preventDefault();
    return false;  // No se ejecuta si son las flechitas de adelante y atras, 
                  //pero si son permitidas en la variable allowedCode 
  }  

});

function formularioCanasta() {

  let tituloMensaje = document.getElementById("tituloMensaje");
  tituloMensaje.innerText='';
  
  let contenedorError = document.getElementById("mensajeModal");
  contenedorError.innerText='';

  let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
  mensajeModalParrafo.innerText='';

  let userData = [];
  let userDataActivo = [];

  userData = window.sessionStorage.getItem('sesionCanasta');
  userDataActivo = window.sessionStorage.getItem('sesionActivo');
  
  let jsonDataincludes = [];
  jsonDataincludes = JSON.parse(userData);

  let jsonDataincludesActivo = [];
  jsonDataincludesActivo = JSON.parse(userDataActivo);
  
  
  if ((jsonDataincludes && jsonDataincludes.length>0) || (jsonDataincludesActivo && jsonDataincludesActivo.length>0)) {

    window.location.href = "formulario_solicitud_canasta.html";
  
  } else {

    tituloMensaje.innerText = 'Hubo un inconveniente!' ;
    contenedorError.innerText ='No es posible mostrar el Carrito!'
    mensajeModalParrafo.innerText ='Parece que no has agregado ningún artículo aún.';                           
    
    $('#modalMensaje').modal('show');
  }

  return false;

}

function botonEnviaSolicitud(jsonArray, cantidad) {
    
  let jsonCanasta = [];
 
  jsonArray["cantidad"]=cantidad;
  jsonCanasta.push(jsonArray);

  let json = JSON.stringify(jsonCanasta);
  
  window.sessionStorage.setItem('postSolicitud',json);
  window.sessionStorage.setItem('botonEnviaSolicitud',true);
  window.location.assign('formulario_solicitud.html');
  return true;
  
}

function botonEnviaSolicitudActivo(jsonArray) {
  
  let jsonActivo = [];
  jsonActivo.push(jsonArray);

  let json = JSON.stringify(jsonActivo);
  
  window.sessionStorage.setItem('postSolicitudActivo',json);
  window.sessionStorage.setItem('botonEnviaSolicitud',true);
  window.location.assign('formulario_solicitud.html');

  return true;
  
}

function agregarActivo(jsonArray) {
  
  let tituloMensaje = document.getElementById("tituloMensaje");
  tituloMensaje.innerHTML='';

  let contenedorError = document.getElementById("mensajeModal");
  contenedorError.innerHTML='';

  let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
  mensajeModalParrafo.innerText='';
  
  let jsonActivo = [];
  let userData = [];
  
  userData = window.sessionStorage.getItem('sesionActivo');

  

  if (userData && userData.length>0) {

    let jsonDataincludes = [];
    jsonDataincludes = JSON.parse(userData);
    
    let magenicVendor = jsonDataincludes.find( jsonDataincludes => jsonDataincludes['id_activo'] === jsonArray.id_activo );
    
    if (typeof magenicVendor !== 'undefined') {
      
      tituloMensaje.innerText = 'Hubo un inconveniente!' ;
      contenedorError.innerText = 'Parece que ya agregaste ' + magenicVendor.clase + " " + magenicVendor.marca + " " + magenicVendor.modelo;
      
      $('#modalMensaje').modal('show');

      return false;
    }
  
    jsonActivo = jsonDataincludes; //si ya existe datos en sessionStorage

  }

  jsonActivo.push(jsonArray);

  let json = JSON.stringify(jsonActivo);
  window.sessionStorage.setItem('sesionActivo',json);

  muestraContador();

  tituloMensaje.innerText = 'Ok!';
  contenedorError.innerHTML='Has agregado ' + jsonArray.clase + ' al Carrito.';
        
  $('#modalMensaje').modal('show');
  
  return true;
  

}

function agregar(jsonArray, cantidad) {
  
  let tituloMensaje = document.getElementById("tituloMensaje");
  tituloMensaje.innerHTML='';

  let contenedorError = document.getElementById("mensajeModal");
  contenedorError.innerHTML='';

  let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
  mensajeModalParrafo.innerText='';
  
  let jsonCanasta = [];
  let userData = [];
  
  userData = window.sessionStorage.getItem('sesionCanasta');

  if (userData && userData.length>0) {

    let jsonDataincludes = [];
    jsonDataincludes = JSON.parse(userData);
        
    let magenicVendor = jsonDataincludes.find( jsonDataincludes => jsonDataincludes['alias_id'] === jsonArray.alias_id );
    
    if (typeof magenicVendor !== 'undefined') {
      
      tituloMensaje.innerText = 'Hubo un inconveniente!' ;
      contenedorError.innerText = 'Parece que ya agregaste ' + magenicVendor.alias;
      
      $('#modalMensaje').modal('show');

      return false;
    }
  
    jsonCanasta = jsonDataincludes; //si ya existe datos en sessionStorage

  }

  jsonArray["cantidad"]=cantidad; //Agrega la cantidad de cboCantidad
  jsonCanasta.push(jsonArray);

  let json = JSON.stringify(jsonCanasta);
  window.sessionStorage.setItem('sesionCanasta',json);
  
  
  muestraContador();

  tituloMensaje.innerText = 'Ok!';
  contenedorError.innerHTML='Has agregado ' + cantidad +                           
                                ' ' + jsonArray.alias + ' al Carrito.';
        
  $('#modalMensaje').modal('show');
  
  return true;
  

}

function muestraContador() {

  let contador = document.getElementById("contador");
  let userDataCanasta = [];
  let userDataActivo = [];
  let intcontadorAlias = 0;
  let intcontadorActivo = 0;
  let total = 0;

  userDataCanasta = window.sessionStorage.getItem('sesionCanasta');
  userDataActivo = window.sessionStorage.getItem('sesionActivo');
  
  if (userDataCanasta && userDataCanasta.length>0) {
    let jsonData = [];
    jsonData = JSON.parse(userDataCanasta);  
    intcontadorAlias = jsonData.length;
  }
  
  if (userDataActivo && userDataActivo.length>0) {
    let jsonData = [];
    jsonData = JSON.parse(userDataActivo);  
    intcontadorActivo = jsonData.length;
  } 
  
  total = intcontadorAlias + intcontadorActivo;
 
  contador.innerText = total;

}

function buscar() {

  let contenedorError = document.getElementById("mensaje");
  contenedorError.innerHTML='';
  
  let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
  mensajeModalParrafo.innerText='';

  let btnBuscar = document.getElementById("btnBuscar");
   
  btnBuscar.disabled = true;
  
  btnBuscar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
  let spinner = document.getElementById("spinner"); 
  let activo_nombre = $('#txtBuscar').val();

  cargaDatosBd(activo_nombre);

  spinner.style.visibility = 'hidden';
  btnBuscar.innerHTML='<img src="img/buscar.png" width="25" height="25" alt="" loading="lazy">';
  btnBuscar.disabled = false;

}

function cargaDatosPantalla(rs) {
                   
  rs.forEach(obj => {
    
    let colCard = document.createElement('div');
    //"col-sm-6 col-md-4 col-lg-3 col-xl-3"
    colCard.className = "col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3";    

    let card = document.createElement('div');
    card.className = "card border-light mb-1"; 

    let colImagen = document.createElement('div');
    colImagen.className = "col";

    let cardimg = document.createElement('img');
    cardimg.className = "card-img";
    cardimg.src = './img/alias/' + obj.alias_imagen;
    //console.log(obj.alias_imagen);

    colImagen.appendChild(cardimg);

    let cardbody = document.createElement('div');
    cardbody.className = "card-body";

    let contenedorTituloImagen = document.createElement('div');
    contenedorTituloImagen.className = "form-group row row justify-content-center";

    let h4 = document.createElement('h4');
    h4.className = "card-title";
    let createATextNombre = document.createTextNode(obj.alias);
    h4.appendChild(createATextNombre);

    contenedorTituloImagen.appendChild(h4);

    //Fila Col Cantidad equipos
    /*
    let filaCantidadequipos = document.createElement('div');
    filaCantidadequipos.className = "form-group row";

    let disponible=0;
    let colCantidadequipos = document.createElement('div');

    let selectCantidadsolicitud = document.createElement('select');
    selectCantidadsolicitud.className="col cssCombo";
    selectCantidadsolicitud.id="cboCantidad";
    
    fetch('sql/selectPlacaAliasdisponibleGestor.php?'
    + new URLSearchParams({alias_id: obj.alias_id, codigo: codigoPresupuestario}))
    .then(function(response) {
            
      if(response.ok) {

        response.json().then(function(data) {
          
          disponible = data[0].disponible;
          
          colCantidadequipos.className = "col";
          colCantidadequipos.innerHTML = '<strong>Cantidad de equipos para prestar: </strong>' + 
                                            '<span class="badge badge-pill badge-info">' + disponible + '</span>';
          
          let valor = 0;
          for (let i = 0; i < disponible; i++) {
            valor = i+1;
            let opt = document.createElement("option");
            opt.value = valor;
            opt.innerHTML = valor; 
            selectCantidadsolicitud.append(opt);
        }
      
        });

      }

      })
      .then(function(data) {
          //console.log('data = ', data);
      })
      .catch(function(err) {
          console.error(err);
      });

   
    //Combo cantidad solicitud
    let filaCantidadsolicitud = document.createElement('div');
    filaCantidadsolicitud.className = "form-group row justify-content-center";

    let labelCantidadsolicitud = document.createElement('label');
    labelCantidadsolicitud.className="col col-form-label"; 
    labelCantidadsolicitud.for="cboCantidad";
    let createATextlabelCantidadsolicitud = document.createTextNode("Cantidad a solicitar:");
    labelCantidadsolicitud.appendChild(createATextlabelCantidadsolicitud);
    
    filaCantidadsolicitud.appendChild(selectCantidadsolicitud);

    */

    //Boton agregar canasta
    let filaAgregarcanasta = document.createElement('div');
    filaAgregarcanasta.className = "form-group row justify-content-center";

    let botonAgregar = document.createElement('a');
    botonAgregar.id = "btnAgregar";
    botonAgregar.className = "btn text-white bg-secondary";
    botonAgregar.href = "javascript:void(0)";
    botonAgregar.onclick =  function () {
                                          //agregar(obj, selectCantidadsolicitud.value);
                                          agregar(obj, 1);
                                        };
    let createATextBotonAgregar = document.createTextNode("Agregar al Carrito");
    botonAgregar.appendChild(createATextBotonAgregar);

    filaAgregarcanasta.appendChild(botonAgregar);

     //Boton solicitud
     let filaBotonsolicitud = document.createElement('div');
     filaBotonsolicitud.className = "form-group row justify-content-center";
 
     let botonSolicitud = document.createElement('a');
     botonSolicitud.id = "btnAgregar";
     botonSolicitud.className = "btn text-white bg-success";
     botonSolicitud.href = "javascript:void(0)";
     botonSolicitud.onclick =  function () {
                                          //botonEnviaSolicitud(obj, selectCantidadsolicitud.value);
                                          botonEnviaSolicitud(obj, 1);
                                         };    
     
     let createATextBotonSolicitud = document.createTextNode("Solicitar ahora");
     botonSolicitud.appendChild(createATextBotonSolicitud);
 
     filaBotonsolicitud.appendChild(botonSolicitud);
 
    //Agrega al DOM        
    cardbody.appendChild(contenedorTituloImagen);
    /*
    cardbody.appendChild(colCantidadequipos);
    cardbody.appendChild(labelCantidadsolicitud);
    cardbody.appendChild(filaCantidadsolicitud);
    */
    cardbody.appendChild(filaAgregarcanasta);
    cardbody.appendChild(filaBotonsolicitud);
    card.appendChild(colImagen);
    card.appendChild(cardbody);
    colCard.appendChild(card);
    
    document.getElementById('fila').appendChild(colCard);
       
  });
  
  return false;

}

function cargaDatosPantallaActivo(rs) {
                 
  rs.forEach(obj => {
    
    let colCard = document.createElement('div');
  
    colCard.className = "col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 pb-2";

    let card = document.createElement('div');
    card.className = "card border-light mb-1"; 

    let colImagen = document.createElement('div');
    colImagen.className = "col";

    let cardimg = document.createElement('img');
    cardimg.className = "card-img-top";
    cardimg.src = './img/' + obj.imagen;

    colImagen.appendChild(cardimg);

    let cardbody = document.createElement('div');
    cardbody.className = "card-body";

    let contenedorTituloImagen = document.createElement('div');
    contenedorTituloImagen.className = "form-group row row justify-content-center";

    let h4 = document.createElement('p');
    h4.className = "card-text font-weight-bold text-center";
    let createATextNombre = document.createTextNode(obj.clase);
    h4.appendChild(createATextNombre);

    contenedorTituloImagen.appendChild(h4);

    let contenedorTituloImagenMarca = document.createElement('div');
    contenedorTituloImagenMarca.className = "form-group row row justify-content-center";

    let hMarca = document.createElement('p');
    hMarca.className = "card-text font-weight-bold";
    let createATextNombreMarca = document.createTextNode(obj.marca);
    hMarca.appendChild(createATextNombreMarca);

    contenedorTituloImagenMarca.appendChild(hMarca);

    let contenedorTituloImagenPlaca = document.createElement('div');
    contenedorTituloImagenPlaca.className = "form-group row row justify-content-center";

    let hPlaca = document.createElement('p');
    hPlaca.className = "card-text";
    let createATextNombrePlaca = document.createTextNode("Placa: " + obj.placa);
    hPlaca.appendChild(createATextNombrePlaca);

    contenedorTituloImagenPlaca.appendChild(hPlaca);

    let etiqueta = "";
    if( obj.numero_activo === null ){
      etiqueta="Etiqueta: Por asignar";
    } else {
      etiqueta="Etiqueta:" + obj.numero_activo ;
    }
    
    let contenedorTituloImagenNumero_activo = document.createElement('div');
    contenedorTituloImagenNumero_activo.className = "form-group row row justify-content-center";

    let hnumero_Activo = document.createElement('p');
    hnumero_Activo.className = "card-text";
    let createATextNombrenumero_Activo = document.createTextNode(etiqueta);
    hnumero_Activo.appendChild(createATextNombrenumero_Activo);

    contenedorTituloImagenNumero_activo.appendChild(hnumero_Activo);

    //Boton agregar canasta
    let filaAgregarcanasta = document.createElement('div');
    filaAgregarcanasta.className = "form-group row justify-content-center";

    let botonAgregar = document.createElement('a');
    botonAgregar.id = "btnAgregar";
    botonAgregar.className = "btn text-white bg-secondary";
    botonAgregar.href = "javascript:void(0)";
   botonAgregar.onclick =  function () {
                                          agregarActivo(obj);
                                        };
    let createATextBotonAgregar = document.createTextNode("Agregar al Carrito");
    botonAgregar.appendChild(createATextBotonAgregar);

    filaAgregarcanasta.appendChild(botonAgregar);

     //Boton solicitud
     let filaBotonsolicitud = document.createElement('div');
     filaBotonsolicitud.className = "form-group row justify-content-center";
 
     let botonSolicitud = document.createElement('a');
     botonSolicitud.id = "btnAgregar";
     botonSolicitud.className = "btn text-white bg-success";
     botonSolicitud.href = "javascript:void(0)";
      botonSolicitud.onclick =  function () {
                                  botonEnviaSolicitudActivo(obj);
                                };  
     
     let createATextBotonSolicitud = document.createTextNode("Solicitar ahora");
     botonSolicitud.appendChild(createATextBotonSolicitud);
 
     filaBotonsolicitud.appendChild(botonSolicitud);
 
    //Agrega al DOM        
    cardbody.appendChild(contenedorTituloImagen);
    cardbody.appendChild(contenedorTituloImagenMarca);
    cardbody.appendChild(contenedorTituloImagenPlaca);
    cardbody.appendChild(contenedorTituloImagenNumero_activo);    
    cardbody.appendChild(filaAgregarcanasta);
    cardbody.appendChild(filaBotonsolicitud);
    card.appendChild(colImagen);
    card.appendChild(cardbody);
    colCard.appendChild(card);
    
    document.getElementById('fila').appendChild(colCard);
       
  });
  
  return false;

}

function cargaDatosBd(activo_nombre) {            
    
    $('.col-sm-6,.col-md-4,.col-lg-3,.col-xl-3').remove();    
    
    fetch('sql/selectActivoAliasGestor.php?'
    + new URLSearchParams({aliasActivo: activo_nombre, codigo: codigoPresupuestario}))
    .then(function(response) {

      if(response.ok) {
  
        response.json().then(function(data) {  
            
         //console.log(data);
         if (Object.keys(data).length>0) {

          cargaDatosPantalla(data);

         } else {
          
          /* document.getElementById('txtBuscar').value = '';
          $('.card').remove();

          let contenedorError = document.getElementById("mensaje");
          contenedorError.innerHTML='<div class="alert alert-danger">' +
                                  '<strong>Error! </strong>' +
                                      'No se encontró el artículo' +
                                  '</div>'; */    
         }
          
          

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
                           
    fetch('sql/selectActivoGestor.php?'
    + new URLSearchParams({aliasActivo: activo_nombre, codigo: codigoPresupuestario}))
    .then(function(response) {

      if(response.ok) {
  
        response.json().then(function(data) {  
            
         //console.log(data);
         if (Object.keys(data).length>0) {

          cargaDatosPantallaActivo(data);

         } else {
          
          /* document.getElementById('txtBuscar').value = '';
          $('.card').remove();
          let contenedorError = document.getElementById("mensaje");
          contenedorError.innerHTML='<div class="alert alert-danger">' +
                                  '<strong>Error! </strong>' +
                                      'No se encontró el artículo' +
                                  '</div>';  */   
         }
          
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

function login() {

  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');
  
  if (userData && userData.length>0) {

    let jsonData = [];

    jsonData = JSON.parse(userData);

    codigoPresupuestario = jsonData["CentrosEducativosDondeTrabaja"];
    
    //console.log(jsonData);

  } else {
    
    //codigoPresupuestario = "5300";
    return false;    

  }

  return true;

}
