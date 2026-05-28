let txtBuscar = document.getElementById("txtBuscar");
let alias_id = 0;
let codigoPresupuestario;
let id_placa = 0;

txtBuscar.addEventListener("keydown", 
function(e) {
  
  var allowedCode = [8, 13, 32, 37, 39, 44, 45, 46, 95];
  var charCode = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode :
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
  
    var allowedCode = [8, 13, 32, 37, 39, 44, 45, 46, 95];
    var charCode = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode :
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

txtBuscar.addEventListener("keypress", 
function(e) {
  if(e.keyCode == 13) {
    e.preventDefault();
    buscar();
    return false;
  }
});

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

function solicitud() {       
  
  window.sessionStorage.setItem('botonEnviaSolicitud',false);
  window.location.href = 'formulario_solicitud.html';

  return false;
}

function modificar(jsonArray, cantidad) {
   
  let jsonCanasta = [];
  let userData = [];
  
  userData = window.sessionStorage.getItem('sesionCanasta');

  let jsonDataincludes = [];
  jsonDataincludes = JSON.parse(userData);
      
  let indice = jsonDataincludes.indexOf(jsonDataincludes.find( jsonDataincludes => jsonDataincludes['alias_id'] === jsonArray.alias_id ));
  jsonDataincludes.splice(indice,1);
  
  jsonCanasta = jsonDataincludes; 

  jsonArray["cantidad"]=cantidad; //Agrega la cantidad de cboCantidad
  jsonCanasta.push(jsonArray);

  let json = JSON.stringify(jsonCanasta);
  window.sessionStorage.setItem('sesionCanasta',json);
    
  return true;
  
}

function buscar() {
     
  let activo_nombre = $('#txtBuscar').val();
  
   window.sessionStorage.setItem('buscarNombre',activo_nombre);
   window.location.href = 'formulario_buscar_alias.html';

   return false;
}

window.onload = function() {
  
  let boologin = login();

  if (boologin==false) {
      
    spinner.style.visibility = 'hidden';
    btnIngresar.innerText="Registrar solicitud de equipo";
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
  
  muestraContador();
  $('.card').remove();
  cargaDatosPantalla();
  cargaDatosPantallaActivo();  
        
  return false;

}

function muestraContador() {

  /* let contador = document.getElementById("contador");

  let userDataCanasta = [];
  userDataCanasta = window.sessionStorage.getItem('sesionCanasta');
  
  if (userDataCanasta && userDataCanasta.length>0) {
    let jsonData = [];
    jsonData = JSON.parse(userDataCanasta);  
    contador.innerText = jsonData.length;

    
  } else {

    contador.innerText = "0";
    
  } */

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

  
  return false;

}

function quitarElementoArray() {

  let jsonData = JSON.parse(window.sessionStorage.getItem('sesionCanasta'));
    
  let newjsonData = jsonData.filter(item=>item.alias_id!=alias_id);
  
  let jsonCanasta = [];
  jsonCanasta = newjsonData;

  let json = JSON.stringify(jsonCanasta);
  window.sessionStorage.setItem('sesionCanasta',json);

  $("#modalMensajeSiNo").modal('hide');

  muestraContador();
  $('.card').remove();
  cargaDatosPantalla();
  cargaDatosPantallaActivo(); 
    
  return false;

}

function quitarElementoArrayArticulo() {

  let jsonData = JSON.parse(window.sessionStorage.getItem('sesionActivo'));
    
  let newjsonData = jsonData.filter(item=>item.id_placa!=id_placa);
  
  let jsonCanasta = [];
  jsonCanasta = newjsonData;

  let json = JSON.stringify(jsonCanasta);
  window.sessionStorage.setItem('sesionActivo',json);

  $("#modalMensajeSiNoArticulo").modal('hide');

  muestraContador();
  $('.card').remove();
  cargaDatosPantalla();
  cargaDatosPantallaActivo(); 
    
  return false;

}

  function botonQuitar(arrayArticulo) {

    let tituloMensaje = document.getElementById("tituloMensajeSiNo");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModalSiNo");
    contenedorError.innerText='';
    
    tituloMensaje.innerText = 'Aviso importante!' ;
    contenedorError.innerText = 'Realmente desea quitar ' + arrayArticulo["alias"] + ' del Carrito ?';
    alias_id=arrayArticulo["alias_id"]; 

    $("#modalMensajeSiNo").modal('show');

    return false;

  }  
  
  function botonQuitarActivo(arrayArticulo) {

    let tituloMensaje = document.getElementById("tituloMensajeSiNoArticulo");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModalSiNoArticulo");
    contenedorError.innerText='';
    
    tituloMensaje.innerText = 'Aviso importante!' ;
    contenedorError.innerText = 'Realmente desea quitar ' + arrayArticulo["clase"] + ' del Carrito ?';

    id_placa=arrayArticulo["id_placa"];   

    $("#modalMensajeSiNoArticulo").modal('show');

    return false;

  }  

  function cargaDatosPantalla() {
    
    //$('.card').remove();

    let userDataCanasta = [];
    userDataCanasta = window.sessionStorage.getItem('sesionCanasta');
    
    if (userDataCanasta && userDataCanasta.length>0) {

      let jsonData = [];
      jsonData = JSON.parse(userDataCanasta);


      jsonData.forEach(obj => {
                       
            let card = document.createElement('div');
            card.className = "card border-light";
            card.id = obj.alias;

            let form = document.createElement('form');
            form.className = "row form-inline";

            //Column Imagen
            let colImg = document.createElement('div');
            colImg.className = "col";
      
            let cardimg = document.createElement('img');
            cardimg.className = "card-img";
            cardimg.src = './img/alias/' + obj.alias_imagen;

            colImg.appendChild(cardimg);
            
            //Columna Info
            let colInfo = document.createElement('div');
            colInfo.className = "col";

            let nombre = document.createElement('div');
            nombre.innerText = obj.alias;

            colInfo.appendChild(nombre);

            /*
            let contenedorCantidadQuitar = document.createElement('div');
            contenedorCantidadQuitar.className = "input-group";

            let disponible=0;
            let selectCantidadsolicitud = document.createElement('select');
            selectCantidadsolicitud.className="form-control cssCombo";            
        
            fetch('sql/selectPlacaAliasdisponibleGestor.php?'
            + new URLSearchParams({alias_id: obj.alias_id, codigo: codigoPresupuestario}))
            .then(function(response) {
                    
              if(response.ok) {

                response.json().then(function(data) {
                  
                  disponible = data[0].disponible;
                          
                    //Llena el combo con las cantidades
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
                
            
            selectCantidadsolicitud.value = obj.cantidad;            
            selectCantidadsolicitud.onclick =  function () {
                                          modificar(obj, selectCantidadsolicitud.value);
                                        };

            contenedorCantidadQuitar.appendChild(selectCantidadsolicitud);
            
        */

            let contenedorBotonQuitar = document.createElement('div');
            contenedorBotonQuitar.className = "input-group-prepend";
            
            let spanBotonQuitar = document.createElement('span');
            spanBotonQuitar.className = "input-group-text";

            let botonQuitar = document.createElement('button');
            botonQuitar.type = "button";
            botonQuitar.className ="close";

            botonQuitar.onclick = new Function("botonQuitar(" + JSON.stringify(obj) + ");");
            botonQuitar.innerHTML = '<span class="text-danger" aria-hidden="true">&times;</span> ';
            
            spanBotonQuitar.appendChild(botonQuitar);

            contenedorBotonQuitar.appendChild(spanBotonQuitar);            

            //contenedorCantidadQuitar.appendChild(contenedorBotonQuitar);

            //colInfo.appendChild(contenedorCantidadQuitar);
            colInfo.appendChild(contenedorBotonQuitar);

            form.appendChild(colImg);
            form.appendChild(colInfo);
            card.appendChild(form);

            document.getElementById('colCards').appendChild(card);

        });

    }    
    
    return false;

  }
  

  function cargaDatosPantallaActivo() {
    
    //$('.card').remove();

    let userDataActivo = [];
    userDataActivo = window.sessionStorage.getItem('sesionActivo');
    
    if (userDataActivo && userDataActivo.length>0) {

      let jsonData = [];
      jsonData = JSON.parse(userDataActivo);


      jsonData.forEach(obj => {
                     
            let card = document.createElement('div');
            card.className = "card border-light";
            card.id=obj.id_placa;
            card.setAttribute('data-id' , obj.id_placa);
            card.setAttribute('data-tipo' , "activo");

            let form = document.createElement('form');
            form.className = "row form-inline";

            //Column Imagen
            let colImg = document.createElement('div');
            colImg.className = "col";
      
            let cardimg = document.createElement('img');
            cardimg.className = "card-img";
            cardimg.src = './img/' + obj.imagen;

            colImg.appendChild(cardimg);
            
            //Columna Info
            let colInfo = document.createElement('div');
            colInfo.className = "col";

            let nombre = document.createElement('div');
            nombre.innerText = obj.clase + " " + obj.marca + " " + obj.modelo + " " + obj.placa + " " + obj.numero_activo;

            colInfo.appendChild(nombre);

            let contenedorCantidadQuitar = document.createElement('div');
            contenedorCantidadQuitar.className = "input-group";

            let contenedorBotonQuitar = document.createElement('div');
            contenedorBotonQuitar.className = "input-group-prepend";
            
            let spanBotonQuitar = document.createElement('span');
            spanBotonQuitar.className = "input-group-text";

            let botonQuitar = document.createElement('button');
            botonQuitar.type = "button";
            botonQuitar.className ="close";

            botonQuitar.onclick = new Function("botonQuitarActivo(" + JSON.stringify(obj) + ");");
            botonQuitar.innerHTML = '<span class="text-dark" aria-hidden="true">&times;</span> ';
            
            spanBotonQuitar.appendChild(botonQuitar);

            contenedorBotonQuitar.appendChild(spanBotonQuitar);            

            contenedorCantidadQuitar.appendChild(contenedorBotonQuitar);

            colInfo.appendChild(contenedorCantidadQuitar);

            form.appendChild(colImg);
            form.appendChild(colInfo);
            card.appendChild(form);

            document.getElementById('colCards').appendChild(card);

        });

    }    
    
    return false;

  }

  function guardar() {

    let btnIngresar = document.getElementById("btnGuardar");
    btnIngresar.disabled = true;
    btnIngresar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    let spinner = document.getElementById("spinner");   

    let arrayArticulos = []
    let checkboxes = document.querySelectorAll('input[type=checkbox]:checked')

    for (let i = 0; i < checkboxes.length; i++) {    
    arrayArticulos.push(checkboxes[i].getAttribute('data-id'))
    }

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
      //console.log(data);                 

      }).catch(function(error) {

      spinner.style.visibility = 'hidden';
      btnIngresar.innerText="Guardar";
      btnIngresar.disabled = false;
      let contenedorError = document.getElementById("mensaje");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                              '<strong>Error! </strong>' +
                              'Intente de nuevo... no hubo respuesta del servidor MEP' +
                              '</div>'; 
      })

      } else {

      spinner.style.visibility = 'hidden';
      btnIngresar.innerText="Guardar";
      btnIngresar.disabled = false;
      let contenedorError = document.getElementById("mensaje");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                          '<strong>Error! </strong>' +
                          'No hay respuesta del servidor MEP. Verifique su conexión de internet ' +
                          '</div>';
      }

      })
      .catch(function(error) {

      spinner.style.visibility = 'hidden';
      btnIngresar.innerText="Guardar";
      btnIngresar.disabled = false;
      let contenedorError = document.getElementById("mensaje");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                        '<strong>Error! </strong>' +
                        'Hubo un problema al guardar la información: ' + error.message +
                        '</div>';        
      })
      .then();

      } else {

      spinner.style.visibility = 'hidden';
      btnIngresar.innerText="Guardar";
      btnIngresar.disabled = false;
      let contenedorError = document.getElementById("mensaje");    
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                        '<strong>Error! </strong>' +
                            'Seleccione los artículos ...' +
                        '</div>';
      return false;   
      }

      spinner.style.visibility = 'hidden';
      btnIngresar.innerText="Guardar";

      return true;
  }
