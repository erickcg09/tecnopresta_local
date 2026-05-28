
window.onload = function() {
  
  obtienePermisos();
    
  return false;

};

function obtienePermisos() {
  
  let sesion = [];
  sesion = window.sessionStorage.getItem('sesion');

  if (sesion && sesion.length>0) {

    let jsonData = [];

    jsonData = JSON.parse(sesion);

    let codigo = jsonData["CentrosEducativosDondeTrabaja"];
    let cedula = jsonData["EMPCED"];

    //console.log(codigo, cedula);

    let rolData; 
    rolData = obtieneRolMostrarCantidades(codigo, cedula);

    return 1;    

  } else {
                                
    return 0;    

  }  


}

function obtieneRolEnvio_a_Formulario(formulario) {

  let sesion = [];
  sesion = window.sessionStorage.getItem('sesion');

  let jsonData = [];
  jsonData = JSON.parse(sesion);

  let codigo = jsonData["CentrosEducativosDondeTrabaja"];
  let cedula = jsonData["EMPCED"];

  fetch('sql/selectPermisosMenuPrestamoGestor.php?'
      + new URLSearchParams({codigo: codigo,
                             cedula: cedula
                            }))
        .then(function(response) {

          if(response.ok) {
      
            response.json().then(function(data) {
                    
              //console.log(data);          
              let rol = 0;

              if (Object.keys(data).length>0) {
                
                rol = data[0].id_rol;

              }
              
              if (rol == 1 || rol == 2 || rol == 3) {

                window.location.assign(formulario);    

              } else {


                let tituloMensaje = document.getElementById("tituloMensaje");
                tituloMensaje.innerText='';
            
                let contenedorError = document.getElementById("mensajeModal");
                contenedorError.innerText='';
      
                let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
                mensajeModalParrafo.innerText='';
      
                tituloMensaje.innerText = 'Hubo un inconveniente!';
                contenedorError.innerText = 'No tiene permisos';
                mensajeModalParrafo.innerText ='Comuniquese con el Administrador de TecnoPresta';  
              
                $('#modalMensaje').modal('show');


              }                
                            
            });              
      
          } 
  
    }).then();
    
    return 0;
}


function obtieneRolMostrarCantidades(codigo, cedula) {

  fetch('sql/selectPermisosMenuPrestamoGestor.php?'
      + new URLSearchParams({codigo: codigo,
                             cedula: cedula
                            }))
        .then(function(response) {

      if(response.ok) {
  
        response.json().then(function(data) {
                
          //console.log(data);          
          let rol = 0;
           
          if (Object.keys(data).length>0) {
                
            rol = data[0].id_rol;

          }

          
          if (rol == 1 || rol == 2 || rol == 3) {
               
            muestraCantidadSolicitudes();
            muestraCantidadPrestamos();

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
    
    return 0;
}

function login() {

  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');
  let codigoPresupuestario;

  if (userData && userData.length>0) {

    let jsonData = [];

    jsonData = JSON.parse(userData);

    codigoPresupuestario = jsonData["CentrosEducativosDondeTrabaja"];
    
    //console.log(jsonData);

  } else {
                                
    return 0;    

  }

  return codigoPresupuestario;

}

function muestraCantidadSolicitudes() {
  
  let codigoPresupuestario = login();

    fetch('sql/selectSolicitudCantidadGestor.php?'
      + new URLSearchParams({codigo: codigoPresupuestario}))
        .then(function(response) {

      if(response.ok) {
  
        response.json().then(function(data) {
                
         let solicitudes = document.getElementById("solicitudes");
         solicitudes.innerText= data[0].cantidad;         
           
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

  function muestraCantidadPrestamos() {
  
    let codigoPresupuestario = login();
  
      fetch('sql/selectPrestamoCantidadGestor.php?'
        + new URLSearchParams({codigo: codigoPresupuestario}))
          .then(function(response) {
  
        if(response.ok) {
    
          response.json().then(function(data) {
                  
           let prestamos = document.getElementById("prestamos");
           prestamos.innerText= data[0].cantidad;         
             
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

    function creaSesionPHP_y_Envia_a_Formulario(formulario) {

      let userData = window.sessionStorage.getItem('sesion');

      if (userData && userData.length>0) {
    
        const formData = new FormData();
        const json = JSON.stringify(userData);
        formData.append('data', json);
        
        fetch('../sql/sesionCargaSW.php', {
          method: 'POST', 
          body: formData,     
        }).then(function(response) {
      
          if(response.ok) {
                      
              response.json().then(function(data) {  
                                      
                if (Object.keys(data).length>0) {
                  
                  // sesion de php iniciada...
                  //console.log(data);
                  window.location.assign(formulario);
      
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
        }).then();
      
      } else {
    
        let contenedorError = document.getElementById("mensaje");    
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Error! </strong>' +
                                            'No ha iniciado sesión ...' +
                                        '</div>';    
      }
    
    
      return true;      
      
    }
  
