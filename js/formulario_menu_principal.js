window.userData = []; //Variable global para almacenar el JSON del ws

function login() {

  userData = window.sessionStorage.getItem('sesion');
    
  if (userData && userData.length>0) {

    let jsonData = [];
    let h5institucion = document.getElementById("institucion");
    let h4Usuario = document.getElementById("usuario");

    jsonData = JSON.parse(userData);

    h4Usuario.innerText = jsonData["Nombre"] + " " + jsonData["Apellido1"] + " " + jsonData["Apellido2"];
    h5institucion.innerText = jsonData["CentrosEducativosDondeTrabaja"] + " " + jsonData["Dependencia"];                                      

  } else {

    let contenedorError = document.getElementById("mensaje");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesión ...' +
                                    '</div>';    
  }

  return false;

};

window.onload = function() {
  
  login();
      
  return false;

};