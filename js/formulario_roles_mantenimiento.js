window.onload = function() {
  
    //obtienePermisosRoot();
    cargaComboRoles();
    cargaDatosBd();
      
    return false;
  
  };

function obtienePermisosRoot() {

let sesion = [];
sesion = window.sessionStorage.getItem('sesion');

if (sesion && sesion.length>0) {

    let jsonData = [];

    jsonData = JSON.parse(sesion);
   
    let rol = obtieneRolRoot(jsonData);
   
    return 1;    

} else {

    let tituloMensaje = document.getElementById("tituloMensaje");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModal");
    contenedorError.innerText='';

    let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
    mensajeModalParrafo.innerText='';

    tituloMensaje.innerText = 'Hubo un inconveniente!';
    contenedorError.innerText = 'No tiene permisos Root';
    mensajeModalParrafo.innerText ='Comuniquese con el Administrador de TecnoPresta';  
    
    $('#modalMensaje').modal('show');
                                
    return 0;    

}  
  
function obtieneRolRoot(jsonData) {

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
                
            if (rol != 1) {              

                let tituloMensaje = document.getElementById("tituloMensaje");
                tituloMensaje.innerText='';
            
                let contenedorError = document.getElementById("mensajeModal");
                contenedorError.innerText='';
      
                let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
                mensajeModalParrafo.innerText='';
      
                tituloMensaje.innerText = 'Hubo un inconveniente!';
                contenedorError.innerText = 'No tiene permisos Root';
                mensajeModalParrafo.innerText ='Comuniquese con el Administrador de TecnoPresta';  
              
                $('#modalMensaje').modal('show');
                
                return 0;

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

                return 0;
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

}

function salir() {

    window.location.assign("formulario_roles.html");
}

function cargaDatosBd() {

let urlParams = new URLSearchParams(window.location.search);
let id_lista_blanca = urlParams.get('id_lista_blanca');

fetch('sql/selectRolesMantenimientoGestor.php?'
+ new URLSearchParams({id_lista_blanca: id_lista_blanca}))
.then(function(response) {

    if(response.ok) {

    response.json().then(function(data) {  
        
        if (Object.keys(data).length>0) {

            cargaDatosPantalla(data);

        } else {
               
        let contenedorError = document.getElementById("mensaje");
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                '<strong>Atención! </strong>' +
                                    'No se encontró datos' +
                                '</div>';
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

return 1;
    
}

function cargaDatosPantalla(data) {

data.forEach(obj => {

    //console.log(data);

    let txtCodigo = document.getElementById("codigo");
    txtCodigo.value = data[0].codigo;

    let txtCedula = document.getElementById("cedula");
    txtCedula.value = data[0].cedula;

    let txtCorreo = document.getElementById("correo");
    txtCorreo.value = data[0].nombre;

    let cbocboRol = document.getElementById("cboRol");
    cbocboRol.value= data[0].id_rol; 
    
    $("#cboRol").selectpicker("refresh");

});

return true;
    
}

function cargaComboRoles() 
{
  
  fetch('sql/selectRolesTablaGestor.php')
  .then(function(response) 
  {
          
    if(response.ok) 
    {

      response.json().then(function(data) 
      {
        
        //console.log(data);
              
        data.forEach(element => 
          {

            let cboSoftware = document.getElementById("cboRol"); 
            let opt = document.createElement("option");
            opt.value = element.id_rol;
            opt.innerHTML = element.rol;        
            cboSoftware.append(opt);            

          });
          
          $("#cboRol").selectpicker("refresh");
    
      });
  
    }

  }).then(function(data){});

}