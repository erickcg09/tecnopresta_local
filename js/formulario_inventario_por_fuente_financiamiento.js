
let codigoPresupuestario;

window.onload = function() {
  
  let boologin = login();

  if (boologin) {
    
    cargaComboFondos();
     
  }  else {
   
    let contenedorError = document.getElementById("mensaje");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesión ...' +
                                    '</div>';

  }
      
  return false;

}

function obtieneEstadoCheck(nombreElem) {
  
  let rates = document.getElementsByName(nombreElem);
  let rate_value;
  for(let i = 0; i < rates.length; i++){
      if(rates[i].checked){
          rate_value = rates[i].value;
          return rate_value;
      }
  }

  return "0";
  
}

function guardar() {
  
  let boologin = login();

  let btnIngresar = document.getElementById("btnGuardar");
  btnIngresar.disabled = true;
  btnIngresar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

  if (boologin==false) {
    
    let spinner = document.getElementById("spinner");

    spinner.style.visibility = 'hidden';
    btnIngresar.innerText="Guardar";
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
    

  let cboFondos = $('#cboFondos option:selected').val();
  
  if (cboFondos < 1) {
    
    spinner.style.visibility = 'hidden';
    btnIngresar.innerText="Registrar estdado del Activo";
    btnIngresar.disabled = false;

    let tituloMensaje = document.getElementById("tituloMensaje");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModal");
    contenedorError.innerText='';

    let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
    mensajeModalParrafo.innerText='';

    tituloMensaje.innerText = 'Hubo un inconveniente!';
    contenedorError.innerText ='No ha seleccionado el Origen de los Fondos';
    mensajeModalParrafo.innerText ='Por favor seleccione un Origen Presupuestario';

    $('#modalMensaje').modal('show');

    return false;

  }

  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');

  let nombre = "";
  let codigo = "";
  let cedula = "";
  let jsonData = [];

  jsonData = JSON.parse(userData);

  nombre = jsonData["Nombre"] + " " + jsonData["Apellido1"] + " " + jsonData["Apellido2"];
  codigo = jsonData["CentrosEducativosDondeTrabaja"];
  cedula = jsonData["EMPCED"];
  para =  jsonData["Correo_Electronico_Oficial"];
  Dependencia = jsonData["Dependencia"];

  let arrayArticulos = [];
  let arrayEstado =[];
  let arrayEnUso =[];
  let arrayDonacion =[];

  let checkboxes = document.querySelectorAll('input[type=checkbox]:checked');
  let chkTodos = document.getElementById("chkTodos");

  for (let i = 0; i < checkboxes.length; i++) {  
  
    if (checkboxes[i] != chkTodos && 
        checkboxes[i].id !="usoSINO" && 
        checkboxes[i].id !="donar") {

            arrayArticulos.push(checkboxes[i].getAttribute('data-id'));
            arrayEstado.push(obtieneEstadoCheck("est-" + checkboxes[i].getAttribute('data-id')));
            arrayEnUso.push(obtieneEstadoCheck("usoSINO-" + checkboxes[i].getAttribute('data-id')));
            arrayDonacion.push(obtieneEstadoCheck("donar-" + checkboxes[i].getAttribute('data-id')));              
    }

  } 
  
  console.log(arrayArticulos);
  console.log(arrayEstado);
  console.log(arrayEnUso);
  console.log(arrayDonacion);
  
  if (arrayArticulos && arrayArticulos.length>0) {
   
    const formData = new FormData();    
    const jsonArticulos = JSON.stringify(arrayArticulos);
    const jsonEstado = JSON.stringify(arrayEstado);
    const jsonEnUso = JSON.stringify(arrayEnUso);
    const jsonDonacion = JSON.stringify(arrayDonacion);
    
    formData.append('arrayArticulos', jsonArticulos);    
    formData.append('arrayEstado', jsonEstado);
    formData.append('arrayEnUso', jsonEnUso);    
    formData.append('arrayDonacion', jsonDonacion);    
    
    fetch('sql/updateEstadoporPlacaGestor.php',{
      method: 'POST', 
      body: formData,     
    }).then(function(response) {
  
      if(response.ok) {
                
          response.text().then(function(data) {
            
            console.log(data);                       
            
            if (data == "ok") {

                spinner.style.visibility = 'hidden';
                btnIngresar.innerText="Guardar";

                let tituloMensaje = document.getElementById("tituloMensajeGuardar");
                tituloMensaje.innerText='';

                let contenedorError = document.getElementById("mensajeModalGuardar");
                contenedorError.innerText='';

                let mensajeModalParrafo = document.getElementById("mensajeModalParrafoGuardar");
                mensajeModalParrafo.innerText="";

                tituloMensaje.innerText = 'Ok!';
                contenedorError.innerText ='Se registró la informarcaión correctamente!';      
                
                $('#modalMensajeGuardar').modal('show');

            }        
                            
        }).catch(function(error) {
                  
          spinner.style.visibility = 'hidden';
          btnIngresar.innerText="Guardar";
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
              btnIngresar.innerText="Guardar";
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
            btnIngresar.innerText="Guardar";
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
    btnIngresar.innerText="Guardar";
    btnIngresar.disabled = false;

    let tituloMensaje = document.getElementById("tituloMensaje");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModal");
    contenedorError.innerText='';

    let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
    mensajeModalParrafo.innerText='';

    tituloMensaje.innerText = 'Hubo un inconveniente!';
    contenedorError.innerText ='Al parecer no hay artículos seleccionados!';
    mensajeModalParrafo.innerText = 'Marca los artículos que vas a actualizar ' + 
                                    'haciendo click en el cuadro pequeño junto al nombre del Artículo';
    
    $('#modalMensaje').modal('show');

    return false;   
  }

  
return true;

}

function cargaDatosBd(id_fondos) {

  document.getElementById("chkTodos").checked = false;
  //$("#chkTodos").attr("checked", false);  
  $('#plantilla').empty();

  fetch('sql/selectActivos_por_Fondos_Gestor.php?' 
          + new URLSearchParams({id_fondos: id_fondos, codigo: codigoPresupuestario}))
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
  
  rs.forEach(obj => {
      
      let plantilla = document.getElementById("plantilla");
      
      let row = document.createElement('div');
      row.className = "form-group row";
      row.id = "item";
             
      let colCheck = document.createElement('div');
      colCheck.className = "col-1";
      colCheck.innerHTML='<div class="form-check">'+
                            '<input class="form-check-input position-static" ' +
                            'type="checkbox" data-id="' + obj.id_placa  + '">' +
                          '</div>';
            
      let colNombre = document.createElement('div');
      colNombre.className = "col-3";
      colNombre.style="font-size: 1.3em;";
      let nombreArticulo = obj.clase + " " + obj.modelo + " " + obj.marca;
      let createATextNombre = document.createTextNode(nombreArticulo);
      colNombre.appendChild(createATextNombre);
                   
      //Placa
      let colPlaca = document.createElement('div');
      colPlaca.className = "col-2";
      colPlaca.style="font-size: 1.3em;";
      let createATextPlaca = document.createTextNode(obj.placa);
      colPlaca.appendChild(createATextPlaca);      

      //Estado
      let colEstado = document.createElement('div');
      colEstado.className = "col-2";
     
      switch (obj.id_estado) {
        case 1:                   
            colEstado.innerHTML='<div class="row">'+
                                  '<input value="1" checked="true" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Muy Buena</span></div>'+
                                  '<div class="row">'+
                                  '<input value="2" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Bueno</span></div>'+
                                  '<div class="row">'+
                                  '<input value="3" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Regular</span></div>'+
                                  '<div class="row">'+
                                  '<input value="4" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Malo</span></div>' +
                                  '<div class="row">'+
                                  '<input value="5" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Robado o Hurtado</span></div>';
  
              break;
        case 2:
            colEstado.innerHTML='<div class="row">'+
                                '<input value="1" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Muy Buena</span></div>'+
                                '<div class="row">'+
                                '<input value="2" checked="true" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Bueno</span></div>'+
                                '<div class="row">'+
                                '<input value="3" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Regular</span></div>'+
                                '<div class="row">'+
                                '<input value="4" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Malo</span></div>' +
                                '<div class="row">'+
                                '<input value="5" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Robado o Hurtado</span></div>';

            break;                        
        case 3:
            colEstado.innerHTML='<div class="row">'+
                                '<input value="1" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Muy Buena</span></div>'+
                                '<div class="row">'+
                                '<input value="2" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Bueno</span></div>'+
                                '<div class="row">'+
                                '<input value="3" checked="true" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Regular</span></div>'+
                                '<div class="row">'+
                                '<input value="4" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Malo</span></div>' +
                                '<div class="row">'+
                                '<input value="5" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Robado o Hurtado</span></div>';


            break;
        case 4:
            colEstado.innerHTML='<div class="row">'+
                                '<input value="1" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Muy Buena</span></div>'+
                                '<div class="row">'+
                                '<input value="2" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Bueno</span></div>'+
                                '<div class="row">'+
                                '<input value="3" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Regular</span></div>'+
                                '<div class="row">'+
                                '<input value="4" checked="true" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Malo</span></div>' +
                                '<div class="row">'+
                                '<input value="5" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Robado o Hurtado</span></div>';

            break;
         case 5:
            colEstado.innerHTML='<div class="row">'+
                                '<input value="1" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Muy Buena</span></div>'+
                                '<div class="row">'+
                                '<input value="2" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Bueno</span></div>'+
                                '<div class="row">'+
                                '<input value="3" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Regular</span></div>'+
                                '<div class="row">'+
                                '<input value="4" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Malo</span></div>' +
                                '<div class="row">'+
                                '<input value="5" checked="true" type="radio" class="p-1" name="est-' + obj.id_placa  + '"><span style="font-size: 1.3em;">Robado o Hurtado</span></div>';
              break;                                                      
                      
      } 
            
     //Se usa si no
      let colCheckusosino = document.createElement('div');
      colCheckusosino.className = "col-1 form-check text-center";  
      
      switch (obj.enuso) {
        case 1:
                colCheckusosino.innerHTML=  '<div class="form-check">'+
                                            '<input value="1" checked id="usoSINO" name="usoSINO-' + obj.id_placa  + '" class="form-check-input position-static" ' +
                                            'type="checkbox">' +
                                            '</div>';          
          break;
      
        default:
                colCheckusosino.innerHTML=  '<div class="form-check">'+
                                            '<input value="1" id="usoSINO" name="usoSINO-' + obj.id_placa  + '" class="form-check-input position-static" ' +
                                            'type="checkbox">' +
                                            '</div>';
          break;
      }     

      //Donacion
      let colCheckdonar = document.createElement('div');
      colCheckdonar.className = "col form-check text-center";
      switch (obj.donar) {
        case 1:
              colCheckdonar.innerHTML=  '<div class="form-check">'+
                                        '<input value="1" checked id="donar" name="donar-' + obj.id_placa  + '" class="form-check-input position-static" ' +
                                        'type="checkbox">' +
                                        '</div>';                    
          break;
      
        default:
              colCheckdonar.innerHTML=  '<div class="form-check">'+
                                        '<input value="1" id="donar" name="donar-' + obj.id_placa  + '" class="form-check-input position-static" ' +
                                        'type="checkbox">' +
                                        '</div>';
          break;
      }
      
     
      row.appendChild(colCheck);
      row.appendChild(colNombre);
      row.appendChild(colPlaca);
      row.appendChild(colEstado);
      row.appendChild(colCheckusosino);
      row.appendChild(colCheckdonar);            
      plantilla.appendChild(row);            
 
    });      

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

  } else {
                                
    return false;    

  }

  return true;

}

function cargaComboFondos() {
  
  fetch('sql/selectFondos_Gestor.php')
  .then(function(response) 
  {
          
    if(response.ok) 
    {

      response.json().then(function(data) 
      {
        
        //console.log(data);
              
        data.forEach(element => 
          {

            let cboSeccion = document.getElementById("cboFondos"); 
            let opt = document.createElement("option");
            opt.value = element.id_fondos;
            opt.innerHTML = element.fondos;        
            cboSeccion.append(opt);            

          });
          
          $("#cboFondos").selectpicker("refresh");
    
      });
  
    }

  }).then(function(data){});

}

function checkAll(source) {
      
  let checkboxes = document.querySelectorAll('input[type="checkbox"]');

  for (let i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i] != source && 
        checkboxes[i].id !="usoSINO" && 
        checkboxes[i].id !="donar") {
          checkboxes[i].checked = source.checked;     
    }
  }

}

function muestraEstadoDescripcion() {

  $('#modalMensajeEstado').modal('show');
  return true;
  
}
