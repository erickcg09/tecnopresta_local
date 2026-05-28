
let codigoPresupuestario;

window.onload = function() {
  
  let boologin = login();

  if (boologin) {
     
  }  else {
   
    let contenedorError = document.getElementById("mensaje");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesión ...' +
                                    '</div>';

  }
      
  return false;

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
    
    spinner.style.visibility = 'hidden';
    btnIngresar.innerText="Exportar Gráficos a Microsoft Excel";

    let tituloMensaje = document.getElementById("tituloMensaje");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModal");
    contenedorError.innerText='';

    let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
    mensajeModalParrafo.innerText="";

    tituloMensaje.innerText = 'Ok!';
    contenedorError.innerText ='Se exportó la informarcaión correctamente!';      

    $('#modalMensaje').modal('show');

  
return true;

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
