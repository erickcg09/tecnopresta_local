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
}

function login() {
  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');
  if (userData && userData.length>0) {
    let jsonData = [];
    jsonData = JSON.parse(userData);
    codigoPresupuestario = jsonData["CentrosEducativosDondeTrabaja"];
  } else {
    return false;    
  }
  return true;
}

function buscarInstitucion() {
  window.sessionStorage.setItem('pagina-origen-buscar', "formulario_boleta_de_servicio.html");
  window.location.replace("formulario_institucion_buscar.html");
  return true;    
}
