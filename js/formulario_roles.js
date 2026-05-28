let id_lista_blanca = 0;
let codigo = 0;

window.onload = function() {
  
    //obtienePermisosRoot();
      
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
    
    $('#modalMensajeCerrar').modal('show');
                                
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
              
                $('#modalMensajeCerrar').modal('show');
                
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

function cerrar() {
    //Regresa al Menú Principal si no tiene permiso Root    
    window.location.assign("formulario_menu_principal.html");
}

function buscar() {

    //obtienePermisosRoot();

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='';
    
    let mensajeModalParrafo = document.getElementById("mensajeModalParrafo");
    mensajeModalParrafo.innerText='';
  
    let btnBuscar = document.getElementById("btnBuscar");
     
    btnBuscar.disabled = true;
    
    btnBuscar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    let spinner = document.getElementById("spinner"); 
    let codigoPresupuestario = $('#txtBuscar').val();
  
    cargaDatosBd(codigoPresupuestario);
  
    spinner.style.visibility = 'hidden';
    btnBuscar.innerHTML='<img src="img/buscar.png" width="25" height="25" alt="" loading="lazy">';
    btnBuscar.disabled = false;

    return 1;
  
  }

  function cargaDatosBd(codigoPresupuestario) {

    $('#plantilla').empty();
    
    id_lista_blanca = 0;
    codigo = 0;

    fetch('sql/selectRolesGestor.php?'
    + new URLSearchParams({codigo: codigoPresupuestario}))
    .then(function(response) {

      if(response.ok) {
  
        response.json().then(function(data) {  
            
         if (Object.keys(data).length>0) {

          cargaDatosPantalla(data);

         } else {
          
         document.getElementById('txtBuscar').value = '';
         $('#plantilla').empty();

          let contenedorError = document.getElementById("mensaje");
          contenedorError.innerHTML='<div class="alert alert-danger">' +
                                  '<strong>Atención! </strong>' +
                                      'No se encontró datos asociados a ' + codigoPresupuestario +
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

    //console.log(data);
    data.forEach(obj => {
    
        let rowEncabezado = document.createElement('div');
        rowEncabezado.className = "form-group row";

        let colImagen = document.createElement('div');
        colImagen.className = "col-4 col-md-1";

        let cardimg = document.createElement('img');
        cardimg.className = "card-img";
        cardimg.src = './img/' + obj.imagen;
        cardimg.width="40";
        cardimg.height-"40";
        cardimg.alt=""; 
        cardimg.loading="lazy";

        colImagen.appendChild(cardimg);

        let colCorreo = document.createElement('div');
        colCorreo.className = "col-md-6";
        let createATextCorreo = document.createTextNode(obj.nombre);
        colCorreo.appendChild(createATextCorreo);

        rowEncabezado.appendChild(colImagen);
        rowEncabezado.appendChild(colCorreo);

        document.getElementById('plantilla').appendChild(rowEncabezado);

        let rowDetalle = document.createElement('div');
        rowDetalle.className = "form-group row";

        let colCodigo = document.createElement('div');
        colCodigo.className = "col-md-1";
        let createATextCodigo = document.createTextNode("Código " + obj.codigo);
        colCodigo.appendChild(createATextCodigo);

        rowDetalle.appendChild(colCodigo);

        let colCedula = document.createElement('div');
        colCedula.className = "col-md-2";
        let createATextCedula = document.createTextNode("Cédula: " + obj.cedula);
        colCedula.appendChild(createATextCedula);

        rowDetalle.appendChild(colCedula);

        let colRol = document.createElement('div');
        colRol.className = "col-md-2";
        let createATextRol = document.createTextNode(obj.rol);
        colRol.appendChild(createATextRol);

        rowDetalle.appendChild(colRol);

        let colDescripcion = document.createElement('div');
        colDescripcion.className = "col-md-2";
        let createATextDescripcion = document.createTextNode(obj.descripcion);
        colDescripcion.appendChild(createATextDescripcion);

        rowDetalle.appendChild(colDescripcion);

        let colModificar = document.createElement('div');
        colModificar.className = "col-6 col-md-2";

        let botonModificar = document.createElement('a');
        botonModificar.className = "btn text-white bg-secondary";
        botonModificar.href = "javascript:void(0)";
        botonModificar.onclick =  function () {
                                            modificar(obj);
                                            };
        let createATextBotonModificar = document.createTextNode("Modificar");
        botonModificar.appendChild(createATextBotonModificar);

        colModificar.appendChild(botonModificar);

        rowDetalle.appendChild(colModificar);

        let colElimnar = document.createElement('div');
        colElimnar.className = "col-6 col-md-2";

        let botonEliminar = document.createElement('a');
        botonEliminar.className = "btn text-white bg-danger";
        botonEliminar.href = "javascript:void(0)";
        botonEliminar.onclick =  function () {
                                            botonBorrar(obj);
                                            };
        let createATextBotonEliminar = document.createTextNode("Borrar");
        botonEliminar.appendChild(createATextBotonEliminar);

        colElimnar.appendChild(botonEliminar);

        rowDetalle.appendChild(colElimnar);
        
        document.getElementById('plantilla').appendChild(rowDetalle);

    });

    return true;
      
  }

  function modificar(data) {
  
    window.location.assign("formulario_roles_mantenimiento.html?id_lista_blanca="+data["id_lista_blanca"]);
    return true;
      
  }

  function botonBorrar(data) {

    let tituloMensaje = document.getElementById("tituloMensajeSiNo");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModalSiNo");
    contenedorError.innerText='';
    
    tituloMensaje.innerText = 'Aviso importante!' ;
    contenedorError.innerText = 'Realmente desea eliminar a ' + data["nombre"] + ' Código ' + data["codigo"] + ' ?' ;
    
    id_lista_blanca = data["id_lista_blanca"];
    codigo = data["codigo"];
   
    $("#modalMensajeSiNo").modal('show');

    return true;
      
  }

  function borrar() {

    const formData = new FormData();
    formData.append('id_lista_blanca', id_lista_blanca);

    fetch('sql/deleteRolGestor.php',{
    method: 'POST', 
    body: formData,     
    }).then(function(response) {

        if(response.ok) {

          response.text().then(function(data) 
          { 
              //console.log(data);
              cargaDatosBd(codigo);             
                                          
          }).catch(function(error) {

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

              return false;

          })

        } else {                

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

                return false;
        }

    })
    .catch(function(error) {         

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

          return false;
       
    }).then();

    $("#modalMensajeSiNo").modal('hide');
      
  }