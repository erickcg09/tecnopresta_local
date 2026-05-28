window.onload = function() {
    carga_visita_sitio_bd();    
}

function ir_a_hoja_de_trabajo() {

    let id_visita = parseInt(window.sessionStorage.getItem('id_visita'));
    if (id_visita<=0 || id_visita==null || id_visita == "undefined"  || id_visita == NaN) {

        let contenedorError = document.getElementById("mensaje");    
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                          'No ha iniciado sesión ...' +
                                      '</div>';
        let spinner = document.getElementById("spinner");           
        spinner.className="d-none";                                      
        return false;  

    }

    window.sessionStorage.setItem('id_visita', id_visita);
    window.location.replace("formulario_hoja_trabajo_visitas_sitio.html");

    return true;

  }

function carga_visita_sitio_bd() {

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='';
    let spinner = document.getElementById("spinner");
    spinner.className="d-block";

    let id_visita = parseInt(window.sessionStorage.getItem('id_visita'));
    if (id_visita<=0 || id_visita==null || id_visita == "undefined"  || id_visita ==NaN) {

      let contenedorError = document.getElementById("mensaje");    
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                          'No ha iniciado sesión ...' +
                                      '</div>';
      return false;  

    }    
    
    try {
        fetch('sql/select_visita_id_Gestor.php?'
            + new URLSearchParams({id_visita: id_visita}))
        .then(function(response){                    
            if(response.ok){        
                response.json().then(function(data){                                                                                                      
                    if (Object.keys(data).length>0) {                                
                        carga_pantalla(data);                          
                    } else {                         
                    let contenedorError = document.getElementById("mensaje");
                    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                                '<strong> Regrese a la página anterior </strong>' +
                                                'Finalizó la sesión' +
                                                '</div>';               
                    }                                         
                });                    
                let spinner = document.getElementById("spinner");           
                spinner.className="d-none";
            }        
        }).then();
                
    } catch (error) {
        let contenedorError = document.getElementById("mensaje");
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          error + '</div>';
        let spinner = document.getElementById("spinner");           
        spinner.className="d-none";                  
    }   
}


function carga_pantalla(data) {
  try {
    
    let id_visita = 0;

    data.forEach(obj => {                           
        id_visita = obj.id_visita;
        document.getElementById("contacto").value=obj["persona_contacto"];
        document.getElementById("telefono").value=obj["telefono"];
        document.getElementById("direccion").value=obj["direccion"];
        document.getElementById("correo").value=obj["correo_institucional"];        
      });
    
  } catch (error) {
      let contenedorError = document.getElementById("mensaje");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Error! </strong>' +
                                        error + '</div>';  
  }
  return true;
}

function guardar() {

    document.getElementById("mensaje").innerHTML='';
    let spinner = document.getElementById("spinner");
    spinner.className="d-block"

    let visitas_sitio={};
    visitas_sitio = verifica_antes_guardar();    
    
    if (Object.keys(visitas_sitio).length>0){        
                
        const formData = new FormData();
        const json = JSON.stringify(visitas_sitio);
        formData.append('jsonDatos', json);             
        fetch('sql/updateVisitasSitioGestor.php', {
            method: 'POST',
            body: formData,})
            .then(function(response) {
            if(response.ok) {                                                  
                response.text().then(function(data){                     
                     if (data = "ok") {                                                                                             
                        let mensajeModalDescripcion = document.getElementById("mensajeModalDescripcion");
                        mensajeModalDescripcion.innerHTML = '<div class="alert alert-danger">' +
                                                '<strong>Se registró la información!</strong></div>';                          
                        $('#modalGuardar').modal('show');                        
                    } else {
                        let mensajeModal = document.getElementById("mensaje");                        
                        mensajeModal.innerHTML='<div class="alert alert-danger">' +
                                            '<strong>Error al guardar la información </strong>' + 
                                             data + '</div>';                                           
                    }                    
                    let spinner = document.getElementById("spinner");           
                    spinner.className="d-none";                                                        
                }).catch(function(error) {
                    let mensajeModal = document.getElementById("mensaje");    
                    mensajeModal.innerHTML='<div class="alert alert-danger">' +
                                            '<strong>Error! </strong>' +
                                            'No hay respuesta del servidor . Verifique su conexión de internet ' + error.message +
                                            '</div>';                    
                    let spinner = document.getElementById("spinner");           
                    spinner.className="d-none";     
                });              
            } else {
                let mensajeModal = document.getElementById("mensaje");                          
                mensajeModal.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Error! </strong>' +
                                            'No se pudo conectar con el servidor. Intente de nuevo.' +
                                        '</div>';                
                let spinner = document.getElementById("spinner");           
                spinner.className="d-none";                                                                       
            }
        }).catch(function(error) {
            let mensajeModal = document.getElementById("mensaje");                            
            mensajeModal.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Error! </strong>' +
                                            'Hubo un problema al conectar con el servidor: ' + error.message +
                                        '</div>';
            let spinner = document.getElementById("spinner");           
            spinner.className="d-none";                                                                                                            
        }).then();
    }
    return true;
}

function verifica_antes_guardar() {

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='';    

    let visitas_sitio={};

    let id_visita = parseInt(window.sessionStorage.getItem('id_visita'));
    if (id_visita<=0 || id_visita==null || id_visita == "undefined"  || id_visita == NaN) {

        let contenedorError = document.getElementById("mensaje");    
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                          'No ha iniciado sesión ...' +
                                      '</div>';
        let spinner = document.getElementById("spinner");           
        spinner.className="d-none";                                      
        return false;  

    }
    
    let contacto = document.getElementById('contacto');
    let telefono = document.getElementById('telefono');
    let direccion = document.getElementById('direccion');
    let correo = document.getElementById('correo');
   
    visitas_sitio.id_visita = id_visita;    
    visitas_sitio.persona_contacto = contacto.value;
    visitas_sitio.telefono = telefono.value;
    visitas_sitio.direccion = direccion.value;
    visitas_sitio.correo_institucional = correo.value;    

    return visitas_sitio;
}

function obtiene_visitas_sitio_id_para_guardar() {

    let id_visita = parseInt(window.sessionStorage.getItem('id_visita'));
    if (id_visita<=0 || id_visita==null || id_visita == "undefined"  || id_visita == NaN) {

      let contenedorError = document.getElementById("mensaje");    
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                          'No ha iniciado sesión ...' +
                                      '</div>';
      return false;  

    }    

    guardar();

}