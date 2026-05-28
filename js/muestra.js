window.userData = []; //Variable global para almacenar el JSON del ws

function login() {

  userData = window.sessionStorage.getItem('sesion');
  //console.log(userData);

  if (userData && userData.length>0) {

    const formData = new FormData();
    const json = JSON.stringify(userData);
    formData.append('data', json);
    
    fetch('sql/sesionCargaSW.php', {
      method: 'POST', 
      body: formData,     
    }).then(function(response) {
  
      if(response.ok) {
                  
          response.json().then(function(data) {  
                                  
            if (Object.keys(data).length>0) {
              
              // sesion de php iniciada...
              
               //console.log(data);
             
            }
  
            else  { //Array vacío []
  
                  let contenedorError = document.getElementById("mensaje");
                  contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          'No se pudo iniciar sesión. Intente de nuevo.' +                                        
                                          '</div>';        
            }
        }).catch(function(error) {
                  
                  let contenedorError = document.getElementById("mensaje");
                  contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          'Intente de nuevo... no hubo respuesta del servidor MEP' +
                                          '</div>'; 
                })
  
      } else {
              
              let contenedorError = document.getElementById("mensaje");
              contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                      'No hay respuesta del servidor MEP. Verifique su conexión de internet ' +
                                      '</div>';
      }
  
    })
    .catch(function(error) {
      
            let contenedorError = document.getElementById("mensaje");
            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                    'Hubo un problema con la petición Fetch de login: ' + error.message +
                                    '</div>';        
    })
    .then();
  
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