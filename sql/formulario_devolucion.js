
let prestamo_email_solicitante = "";
let codigoPresupuestario = "";
let prestamo_Id = 0;
let solicitud_email_funcionario;

window.onload = function() {
  
  let boologin = login();

  if (boologin) {
    
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    prestamo_Id = urlParams.get('prestamo_Id');
        
    cargaDatosBdPrestamoEncabezado();
    cargaDatosBd();
  
  }  else {
   
    let contenedorError = document.getElementById("mensaje");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesión ...' +
                                    '</div>';

  }
      
  return false;

}

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

function guardar() {
  
  let boologin = login();

  if (boologin==false) {
    
    spinner.style.visibility = 'hidden';
    btnIngresar.innerText="Registrar devolución de equipo";
    btnIngresar.disabled = false;
    
    let tituloMensaje = document.getElementById("tituloMensaje");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModal");
    contenedorError.innerText='';

    let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
    mensajeModalParrafo.innerText='';

    tituloMensaje.innerText = 'Hubo un inconveniente!';
    contenedorError.innerText ='No has iniciado sesión';
    mensajeModalParrafo.innerText ='O probablemente ha expirado la sesión. Ingresa de nuevo a TecnoPresta';   
    
    $('#modalMensaje').modal('show');

    return false;
  }

  let btnIngresar = document.getElementById("btnGuardar");
  btnIngresar.disabled = true;
  btnIngresar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
  let spinner = document.getElementById("spinner");

  let arrayArticulos = [];
  let arrayArticulosNombre = [];
  let checkboxes = document.querySelectorAll('input[type=checkbox]:checked')

  for (let i = 0; i < checkboxes.length; i++) {    
    arrayArticulos.push(checkboxes[i].getAttribute('data-id'));
    arrayArticulosNombre.push(checkboxes[i].getAttribute('data-nombre'));
  }

  ///console.log(arrayArticulos);

  if (arrayArticulos && arrayArticulos.length>0) {

    const formData = new FormData();    
    const json = JSON.stringify(arrayArticulos);
    const jsonNombre = JSON.stringify(arrayArticulosNombre);

    formData.append('prestamo_Id', prestamo_Id);
    formData.append('arrayArticulos', json);

    formData.append('prestamo_email_solicitante', prestamo_email_solicitante);
    formData.append('arrayArticulosNombre', jsonNombre);
    formData.append('prestamo_nombre_solicitante', prestamo_nombre_solicitante);    

    fetch('sql/insertDevolucionGestor.php', {
      method: 'POST', 
      body: formData,     
    }).then(function(response) {
  
      if(response.ok) {
                  
          response.text().then(function(data) {  
          console.log(data);                 
          
        }).catch(function(error) {
                  
          spinner.style.visibility = 'hidden';
          btnIngresar.innerText="Registrar devolución de equipo";
          btnIngresar.disabled = false;

          let tituloMensaje = document.getElementById("tituloMensaje");
          tituloMensaje.innerText='';
      
          let contenedorError = document.getElementById("mensajeModal");
          contenedorError.innerText='';

          let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
          mensajeModalParrafo.innerText='';

          tituloMensaje.innerText = 'Hubo un inconveniente!';
          contenedorError.innerText ='Intente de nuevo!';
          mensajeModalParrafo.innerText ='No hubo respuesta del servidor MEP.';  
        
          $('#modalMensaje').modal('show');

        })
  
      } else {
              
              spinner.style.visibility = 'hidden';
              btnIngresar.innerText="Registrar devolución de equipo";
              btnIngresar.disabled = false;

              let tituloMensaje = document.getElementById("tituloMensaje");
              tituloMensaje.innerText='';
          
              let contenedorError = document.getElementById("mensajeModal");
              contenedorError.innerText='';

              let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
              mensajeModalParrafo.innerText='';

              tituloMensaje.innerText = 'Hubo un inconveniente!';
              contenedorError.innerText ='No hay respuesta del servidor MEP!';
              mensajeModalParrafo.innerText ='Verifique su conexión de internet.';  
            
              $('#modalMensaje').modal('show');

      }
  
    })
    .catch(function(error) {
      
            spinner.style.visibility = 'hidden';
            btnIngresar.innerText="Registrar devolución de equipo";
            btnIngresar.disabled = false;

            let tituloMensaje = document.getElementById("tituloMensaje");
            tituloMensaje.innerText='';
        
            let contenedorError = document.getElementById("mensajeModal");
            contenedorError.innerText='';

            let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
            mensajeModalParrafo.innerText='';

            tituloMensaje.innerText = 'Hubo un inconveniente!';
            contenedorError.innerText ='Error al guardar la información!';
            mensajeModalParrafo.innerText = error.message;
            
            $('#modalMensaje').modal('show');

    })
    .then();
  
  } else {

    spinner.style.visibility = 'hidden';
    btnIngresar.innerText="Registrar devolución de equipo";
    btnIngresar.disabled = false;

    let tituloMensaje = document.getElementById("tituloMensaje");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModal");
    contenedorError.innerText='';

    let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
    mensajeModalParrafo.innerText='';

    tituloMensaje.innerText = 'Hubo un inconveniente!';
    contenedorError.innerText ='Al parecer no hay artículos seleccionados!';
    mensajeModalParrafo.innerText = 'Marca los artículos que vas a devolver, ' + 
                                    'haciendo click en el cuadro pequeño junto al nombre del Artículo';
    
    $('#modalMensaje').modal('show');

    return false;   
  }

  spinner.style.visibility = 'hidden';
  btnIngresar.innerText="Registrar devolución de equipo";  

  let tituloMensaje = document.getElementById("tituloMensajeGuardar");
  tituloMensaje.innerText='';

  let contenedorError = document.getElementById("mensajeModalGuardar");
  contenedorError.innerText='';

  let mensajeModalParrafo = document.getElementById("mensajeModalParrafoGuardar");
  mensajeModalParrafo.innerText='TecnoPresta enviará una notificación al correo institucional del solicitante';

  tituloMensaje.innerText = 'Ok!';
  contenedorError.innerText ='Se registró la devolución!';      
   
  $('#modalMensajeGuardar').modal('show');

return true;

}

function cargaDatosBd() {

  fetch('sql/selectPrestamoDetalleActivoGestor.php?' 
          + new URLSearchParams({prestamo_Id: prestamo_Id, codigo: codigoPresupuestario}))
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

function cargaDatosBdPrestamoEncabezado() {

  fetch('sql/selectPrestamoEncabezadoGestor.php?' 
          + new URLSearchParams({prestamo_Id: prestamo_Id, codigo: codigoPresupuestario}))
    .then(function(response) {

    if(response.ok) {

      response.json().then(function(data) {  
       
          //console.log(data);
          cargaDatosPantallaEncabezado(data);

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

      //console.log(obj);
      let plantilla = document.getElementById("plantilla");
      
      let row = document.createElement('div');
      row.className = "form-group row";
      row.id = "item";
      
      let nombreArticulo = obj.modelo + " " + obj.marca;
      let colCheck = document.createElement('div');
      colCheck.className = "col-1";
      colCheck.innerHTML='<div class="form-check">'+
                            '<input class="form-check-input position-static" ' +
                            'type="checkbox" data-id="' + obj.id_placa  + '" data-nombre="' + nombreArticulo + '" >' +
                          '</div>';

      let colNombre = document.createElement('div');
      colNombre.className = "col-4";
      let createATextNombre = document.createTextNode(obj.modelo);
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

function cargaDatosPantallaEncabezado(rs) {  

  console.log(rs[0].prestamo_horaRetiro);
  
    let rsfechaRetiro = rs[0].prestamo_fechaRetiro;
    let prestamo_fechaRetiroY = rsfechaRetiro.substr(0, 4);
    let prestamo_fechaRetiroM = rsfechaRetiro.substr(5, 2);
    let prestamo_fechaRetiroD = rsfechaRetiro.substr(8, 2);

    let prestamo_fechaRetiro = prestamo_fechaRetiroD + "/" + prestamo_fechaRetiroM + "/"  + prestamo_fechaRetiroY;

    let rshoraRetiro = rs[0].prestamo_horaRetiro;
    let prestamo_horaRetiroH = rshoraRetiro.substr(0, 2);
    let prestamo_horaRetiroM = rshoraRetiro.substr(3, 2);

    let prestamo_horaRetiro = prestamo_horaRetiroH + ":" + prestamo_horaRetiroM;

    let fechaRetiro = document.getElementById("fechaRetiro");
    fechaRetiro.value = prestamo_fechaRetiro + " " + prestamo_horaRetiro;
    
    let rsfechaDevolucion = rs[0].prestamo_fechaDevolucion;
    let prestamo_fechaDevolucionY = rsfechaDevolucion.substr(0, 4);
    let prestamo_fechaDevolucionM = rsfechaDevolucion.substr(5, 2);
    let prestamo_fechaDevolucionD = rsfechaDevolucion.substr(8, 2);

    let prestamo_fechaDevolucion = prestamo_fechaDevolucionD + "/" + prestamo_fechaDevolucionM + "/"  + prestamo_fechaDevolucionY;

    let rshoraDevolucion = rs[0].prestamo_horaDevolucion;
    let prestamo_horaDevolucionH = rshoraDevolucion.substr(0, 2);
    let prestamo_horaDevolucionM = rshoraDevolucion.substr(3, 2);

    let prestamo_horaDevolucion = prestamo_horaDevolucionH + ":" + prestamo_horaDevolucionM;

    let fechaDevolucion = document.getElementById("fechaDevolución");
    fechaDevolucion.value = prestamo_fechaDevolucion + " " + prestamo_horaDevolucion;

    let rsprestamo_uso = rs[0].prestamo_uso;
    let prestamo_uso = document.getElementById("txtUso");
    prestamo_uso.value = rsprestamo_uso;

    prestamo_nombre_solicitante = rs[0].prestamo_nombre_solicitante;

    prestamo_email_solicitante = rs[0].prestamo_email_solicitante;

    return false;
}

function login() {

  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');
    
  if (userData && userData.length>0) {

    let nombre = document.getElementById("nombre");
    let codigo = document.getElementById("codigo");
    let jsonData = [];

    jsonData = JSON.parse(userData);

    nombre.innerText = jsonData["Nombre"] + " " + jsonData["Apellido1"] + " " + jsonData["Apellido2"];
    codigo.innerText = jsonData["Dependencia"];
    codigoPresupuestario = jsonData["CentrosEducativosDondeTrabaja"];
    
    //console.log(jsonData);

  } else {
        
    return false;    

  }

  return true;

}

function salir() {

  window.location.assign('formulario_menu_prestamo.html');

}