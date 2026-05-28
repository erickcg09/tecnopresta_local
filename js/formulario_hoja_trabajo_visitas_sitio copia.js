let global_id_activo = 0;
let global_visitas_estado_inicial_equipo_id = 0;
let global_codigoPresupuestario = 0;
let global_tipo_de_Indicador = "";
const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');
// let signaturePad = new SignaturePad(canvas);

window.onload = function() {
    cargaComboEstado();    
}

/* document.getElementById('clear').addEventListener('click', () => {
    signaturePad.clear();
}); */

function cargaComboEstado(){
    fetch('sql/selectEstadoVisitas_Gestor.php')
    .then(function(response) {          
      if(response.ok){
        response.json().then(function(data){        
            let cboEstado = document.getElementById("cboEstado");           
            data.forEach(element => {              
              let opt = document.createElement("option");
              opt.value = element.t_estado_visitas_id;
              opt.innerHTML = element.t_estado_visitas_descripcion;        
              cboEstado.append(opt);            
              });
            cboEstado.value=0;
            carga_visita_sitio_bd();            
        });  
      }}).then();
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

function carga_pantalla_hoja_trabajo(id_visita) {
    try {
        fetch('sql/select_visitas_sitio_hoja_trabajo_id_Gestor.php?'
                    + new URLSearchParams({id_visita: id_visita}))
                .then(function(response){                   
                    if(response.ok){        
                        response.json().then(function(data){

                            if (Object.keys(data).length>0) {                                                                                        
                                data.forEach(obj => {                                  
                                    document.getElementById("segundavisita").checked=obj.requiere_segunda_visita;
                                    document.getElementById("tiempo_atencion_hora").value=obj.horas_total_atencion;
                                    document.getElementById("tiempo_atencion_minuto").value=obj.minutos_total_atencion;      
                                });                                                                    
                            } 
                                                                     
                        });                    
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

function obtiene_hoja_trabajo_id_para_cargar_activos(id_visita) {
    try {
        fetch('sql/select_visitas_sitio_hoja_trabajo_id_Gestor.php?'
                    + new URLSearchParams({id_visita: id_visita}))
                .then(function(response){                   
                    if(response.ok){        
                        response.json().then(function(data){

                            let visitas_sitio_hoja_trabajo_id = 0;

                            if (Object.keys(data).length>0) {                                                                                        
                                data.forEach(obj => {                                  
                                    visitas_sitio_hoja_trabajo_id = obj.visitas_sitio_hoja_trabajo_id;      
                                });                                                                    
                            } 

                            if (visitas_sitio_hoja_trabajo_id>0) {
                                carga_pantalla_activos(visitas_sitio_hoja_trabajo_id);    
                            }
                                                                     
                        });                    
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

function obtiene_hoja_trabajo_id_para_cargar_estado_inicial_equipo(id_visita) {
    try {
        fetch('sql/select_visitas_sitio_hoja_trabajo_id_Gestor.php?'
                    + new URLSearchParams({id_visita: id_visita}))
                .then(function(response){                   
                    if(response.ok){        
                        response.json().then(function(data){

                            let visitas_sitio_hoja_trabajo_id = 0;

                            if (Object.keys(data).length>0) {                                                                                        
                                data.forEach(obj => {                                  
                                    visitas_sitio_hoja_trabajo_id = obj.visitas_sitio_hoja_trabajo_id;      
                                });                                                                    
                            } 

                            if (visitas_sitio_hoja_trabajo_id>0) {
                                carga_pantalla_estado_inicial_equipo(visitas_sitio_hoja_trabajo_id);    
                            }
                                                                     
                        });                    
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

function carga_pantalla_activos(visitas_sitio_hoja_trabajo_id) {
    try {
        fetch('sql/select_lista_activos_sitio_hoja_trabajo_Gestor.php?'
            + new URLSearchParams({visitas_sitio_hoja_trabajo_id: visitas_sitio_hoja_trabajo_id}))
            .then(function(response){                   
            if(response.ok){        
                response.json().then(function(data){
                    if (Object.keys(data).length>0) {                                                
                        cargaDatosPantallaArticulo(JSON.stringify(data));                                                     
                    }                                                                               
                });                    
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

function carga_pantalla_estado_inicial_equipo(visitas_sitio_hoja_trabajo_id) {
    try {
        fetch('sql/select_lista_estado_inicial_equipo_Gestor.php?'
            + new URLSearchParams({visitas_sitio_hoja_trabajo_id: visitas_sitio_hoja_trabajo_id}))
            .then(function(response){                   
            if(response.ok){        
                response.json().then(function(data){
                    if (Object.keys(data).length>0) {
                        console.log(data);                                                
                        cargaDatosPantalla_estado_inicial_equipo(JSON.stringify(data));                                                     
                    }                                                                               
                });                    
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

function carga_firma(id_visita) {

    try {
        fetch('sql/select_visitas_sitio_hoja_trabajo_id_Gestor.php?'
            + new URLSearchParams({id_visita: id_visita}))
            .then(function(response){                   
            if(response.ok){        
                response.json().then(function(data){
                    let visitas_sitio_hoja_trabajo_id = 0;
                    if (Object.keys(data).length>0) {                                                                                        
                        data.forEach(obj => {                                  
                            visitas_sitio_hoja_trabajo_id = obj.visitas_sitio_hoja_trabajo_id;      
                        });                                                                    
                    }
                    if (visitas_sitio_hoja_trabajo_id>0) {
                        cargra_pantalla_firma(visitas_sitio_hoja_trabajo_id)
                    }                                                                  
                });                    
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

function cargra_pantalla_firma(visitas_sitio_hoja_trabajo_id) {
    
    try {
        fetch('sql/select_firma_Gestor.php?'
                    + new URLSearchParams({visitas_sitio_hoja_trabajo_id: visitas_sitio_hoja_trabajo_id}))
                .then(function(response){                   
                    if(response.ok){        
                        response.json().then(function(data){
                            let visitas_sitio_hoja_trabajo_firma = "";
                            if (Object.keys(data).length>0) {                                                                                        
                                data.forEach(obj => {                                  
                                    visitas_sitio_hoja_trabajo_firma = obj.visitas_sitio_hoja_trabajo_firma;      
                                });                                                                    
                            }                           
                            const img = new Image();
                            img.onload = function () {
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                            }                            
                            img.src = visitas_sitio_hoja_trabajo_firma;
                        });                    
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
        let cboEstado = document.getElementById("cboEstado");
        cboEstado.value = obj.estado;
        id_visita = obj.id_visita;
        global_codigoPresupuestario=obj.codigo_institucion;       
      });
    
     if (id_visita > 0) {
        carga_pantalla_hoja_trabajo(id_visita);
        obtiene_hoja_trabajo_id_para_cargar_activos(id_visita);
        obtiene_hoja_trabajo_id_para_cargar_estado_inicial_equipo(id_visita);
        //carga_firma(id_visita);            
     } 

  } catch (error) {
      let contenedorError = document.getElementById("mensaje");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Error! </strong>' +
                                        error + '</div>';  
  }
  return true;
}

function guardar(visitas_sitio_hoja_trabajo_id) {

    document.getElementById("mensaje").innerHTML='';
    let spinner = document.getElementById("spinner");
    spinner.className="d-block"

    let visitas_sitio={};
    visitas_sitio = verifica_antes_guardar();    
    
    if (Object.keys(visitas_sitio).length>0){        
        
        visitas_sitio.visitas_sitio_hoja_trabajo_id = visitas_sitio_hoja_trabajo_id;
        console.log(visitas_sitio);
        const formData = new FormData();
        const json = JSON.stringify(visitas_sitio);
        formData.append('jsonDatos', json);       
        fetch('sql/insert_visitas_sitio_hoja_trabajo_Gestor.php', {
            method: 'POST',
            body: formData,})
            .then(function(response) {
            if(response.ok) {                                                  
                response.text().then(function(data){
                    console.log(data);                     
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
                 
    let cboEstado = document.getElementById('cboEstado');
    let visitas_sitio_hoja_trabajo_estado = cboEstado.value;

    if (visitas_sitio_hoja_trabajo_estado<=0 || visitas_sitio_hoja_trabajo_estado==null || 
        visitas_sitio_hoja_trabajo_estado == "undefined"  || visitas_sitio_hoja_trabajo_estado == NaN) {

        let contenedorError = document.getElementById("mensaje");    
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Error al guardar! </strong>' +
                                            'Seleccione el estado de la visita ...' +
                                        '</div>';
        let spinner = document.getElementById("spinner");           
        spinner.className="d-none";                                        
        return false;  
  
    }

    let arrlista_de_Activos = new Array();
    arrlista_de_Activos=lista_de_Activos();

    if (arrlista_de_Activos.length>0) {
        visitas_sitio.lista_de_activos=arrlista_de_Activos;
    } else {
        visitas_sitio.lista_de_activos=[];
    }

    let arrlista_de_indicadores = new Array();
    arrlista_de_indicadores=lista_de_Indicadores();
    if (arrlista_de_indicadores.length>0) {
        visitas_sitio.lista_de_indicador_estado_inicial=arrlista_de_indicadores;
    } else {
        visitas_sitio.lista_de_indicador_estado_inicial=[];
    }

    let segundavisita = document.getElementById('segundavisita');
    let requiere_segunda_visita = 0;
    if (segundavisita.checked) {
        requiere_segunda_visita = 1;
    }

    let horas_total_atencion = document.getElementById('tiempo_atencion_hora').value;
    if (horas_total_atencion<=0 || horas_total_atencion==null || 
        horas_total_atencion == "undefined"  || horas_total_atencion == NaN) {            
            horas_total_atencion=0;
    }

    let minutos_total_atencion = document.getElementById('tiempo_atencion_minuto').value;
    if (minutos_total_atencion<=0 || minutos_total_atencion==null || 
        minutos_total_atencion == "undefined"  || minutos_total_atencion == NaN) {            
            minutos_total_atencion=0;            
    }
    // let dataURL = signaturePad.toDataURL(); // Imagen en base64    
    visitas_sitio.id_visita = id_visita;    
    visitas_sitio.visitas_sitio_hoja_trabajo_estado=visitas_sitio_hoja_trabajo_estado;
    visitas_sitio.requiere_segunda_visita=requiere_segunda_visita;
    visitas_sitio.horas_total_atencion=horas_total_atencion;
    visitas_sitio.minutos_total_atencion=minutos_total_atencion;
    // visitas_sitio.visitas_sitio_hoja_trabajo_firma = dataURL;
    
    return visitas_sitio;
}

function obtiene_visitas_sitio_hoja_trabajo_id_para_guardar() {

    let id_visita = parseInt(window.sessionStorage.getItem('id_visita'));
    if (id_visita<=0 || id_visita==null || id_visita == "undefined"  || id_visita == NaN) {

      let contenedorError = document.getElementById("mensaje");    
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                          'No ha iniciado sesión ...' +
                                      '</div>';
      return false;  

    }    
    try {
    fetch('sql/select_visitas_sitio_hoja_trabajo_id_Gestor.php?'
                + new URLSearchParams({id_visita: id_visita}))
            .then(function(response){                   
                if(response.ok){        
                    response.json().then(function(data){
                        let visitas_sitio_hoja_trabajo_id = 0;
                        if (Object.keys(data).length>0) {                                                                                        
                            data.forEach(obj => {                                  
                                visitas_sitio_hoja_trabajo_id = obj.visitas_sitio_hoja_trabajo_id;      
                            });                                                                    
                        }
                        guardar(visitas_sitio_hoja_trabajo_id);                                         
                    });                    
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

function buscar() {
 
    cargaDatosBd_Buscar_Articulo(global_codigoPresupuestario);

    return true;   
}

function cargaDatosBd_Buscar_Indicador() {

    switch (global_tipo_de_Indicador) {
        case "estado-inicial":
            cargaDatosBd_Estado_Inicial_de_Equipo();   
            break;
    
        default:
            break;
    }

    return true;
    
}

function cargaDatosBd_Estado_Inicial_de_Equipo() {

    let mensajeModalBuscar = document.getElementById("mensajeModalBuscarAgregar_Indicador");
    mensajeModalBuscar.innerHTML='';

    let btnBuscar = document.getElementById("btnBuscarAgregar_Indicador");
    btnBuscar.disabled = true;
    btnBuscar.innerHTML = '<span id="spinnerBuscarIndicador" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    
    let valor = $('#txtBuscarAgregar_Indicador').val();
    muestra_Estado_Inicial_de_Equipo_bd(valor);

    btnBuscar.innerHTML='<img src="img/buscar.png" width="25" height="25" alt="" loading="lazy">';
    btnBuscar.disabled = false;

    return true;

}

function cargaDatosBd_Buscar_Articulo(codigoPresupuestario) {

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='';
  
    let mensajeModalBuscar = document.getElementById("mensajeModalBuscar");
    mensajeModalBuscar.innerHTML='';
  
    let btnBuscar = document.getElementById("btnBuscar");
    btnBuscar.disabled = true;
    btnBuscar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
              
    let valor = $('#txtBuscar').val();
    muestra_articulos_bd(valor, codigoPresupuestario);

    btnBuscar.innerHTML='<img src="img/buscar.png" width="25" height="25" alt="" loading="lazy">';
    btnBuscar.disabled = false;
     
    return true;             
  }
  
  function muestra_Estado_Inicial_de_Equipo_bd(valor) {

    fetch('sql/selectEstado_Inicial_de_Equipo_Gestor.php?'
        + new URLSearchParams({valor: valor}))
    .then(function(response) {
    if(response.ok) {
        response.json().then(function(data) {
            //console.log(data);
            cargaDatosPantallaBuscaIndicadores(data);        
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

  }

  function muestra_articulos_bd(valor, codigoPresupuestario) {

    fetch('sql/selectActivo_Hoja_Trabajo_Gestor.php?'
        + new URLSearchParams({valor: valor, codigo: codigoPresupuestario}))
    .then(function(response) {
    if(response.ok) {
        response.json().then(function(data) {                    
            cargaDatosPantallaBuscarArticulo(data);        
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
  }

function cerrarModal() {
    document.getElementById('txtBuscar').value = '';
    let contenedorError =document.getElementById('mensajeModalBuscar');
    contenedorError.innerHTML="";

    document.getElementById('txtBuscarAgregar_Indicador').value = '';
    let mensajeModalBuscarAgregar_Indicador =document.getElementById('mensajeModalBuscarAgregar_Indicador');
    mensajeModalBuscarAgregar_Indicador.innerHTML="";
}

function buscarRepetidoArticulo(id_activo, id_placa) {
      
    let activos = document.querySelectorAll('.activos')
  
    for (let i = 0; i < activos.length; i++) {    
      
        if (activos[i].getAttribute('data-id')==id_activo && 
            activos[i].getAttribute('data-placaid')==id_placa) {
            return true;
      }

    }

    return false;
}

function buscarRepetidoIndicador(id) {
      
    let indicadores = document.querySelectorAll('.indicadores')
  
    for (let i = 0; i < indicadores.length; i++) {    
      
        if (indicadores[i].getAttribute('data-id')==id) { 
            return true; 
        }

    }

    return false;
}

function agregarArticulo(jsonArray) {
    
    if (buscarRepetidoArticulo(jsonArray["id_activo"], jsonArray["id_placa"])) {
      
      let contenedorError = document.getElementById("mensajeModalBuscar");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                              '<strong>Este artículo ya lo agregaste a la Hoja de Trabajo !!</strong>' +                          
                              '</div>';
      return false;
    }

    let jsonArticulo = [];
    
    jsonArticulo.push(jsonArray);

    let json = JSON.stringify(jsonArticulo);
    
    cargaDatosPantallaArticulo(json);

    cerrarModal();
    $('#buscarModal').modal('hide'); ;
    
    return true;

}

function agregarIndicadores(jsonArray) {
    
    if (global_tipo_de_Indicador=="") { return false;}

    switch (global_tipo_de_Indicador) {
        case "estado-inicial":
            if (buscarRepetidoIndicador(jsonArray["visitas_estado_inicial_equipo_id"])) {
      
                let contenedorError = document.getElementById("mensajeModalBuscarAgregar_Indicador");
                contenedorError.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Este indicador ya lo agregaste a la Hoja de Trabajo !!</strong>' +                          
                                        '</div>';
                return false;
              }          
            break;
    
        default:
            break;
    }

    let jsonArticulo = [];
    
    jsonArticulo.push(jsonArray);

    let json = JSON.stringify(jsonArticulo);
    
    cargaDatosPantallaIndicadores(json);

    cerrarModal();
    $('#modalAgregar_Indicador').modal('hide'); ;    
    
    return true;

}


function cargaDatosPantallaBuscarArticulo(rs) {
    
    $('#colCardsBuscar').empty();
    $('#buscarModal').modal('show');
    let spinner = document.getElementById("spinnerBuscar");
    spinner.className="d-block";

    rs.forEach(obj => {
      
      let card = document.createElement('div');
      card.className = "card";    
  
      let cardbody = document.createElement('div');
      cardbody.className = "card-body";
  
      let contenedorTituloImagen = document.createElement('div');
      contenedorTituloImagen.className = "d-flex justify-content-center";
  
      //Fila Col titulo
      let filaTitulo = document.createElement('div');
      filaTitulo.className = "row";
  
      let colTitulo = document.createElement('div');
      colTitulo.className = "col";
  
      let h4 = document.createElement('h4');
      h4.className = "card-title";
      let createATextNombre = document.createTextNode(obj.clase + " " + 
                                                    obj.marca + " " + 
                                                    obj.modelo + " Placa: " + 
                                                    obj.placa + " Serie: " + 
                                                    obj.serie);
      h4.appendChild(createATextNombre);
  
      colTitulo.appendChild(h4);
      filaTitulo.appendChild(colTitulo);
      contenedorTituloImagen.appendChild(filaTitulo);
            
       //Boton solicitud
       let filaBotonsolicitud = document.createElement('div');
       filaBotonsolicitud.className = "form-group row justify-content-center";
   
       let botonSolicitud = document.createElement('a');
       botonSolicitud.id = "btnAgregar";
       botonSolicitud.className = "btn text-white bg-success col-auto text-center";
       botonSolicitud.href = "javascript:void(0)";
       botonSolicitud.onclick = function() { agregarArticulo(obj); };    
       
       let createATextBotonSolicitud = document.createTextNode("Seleccionar");
       botonSolicitud.appendChild(createATextBotonSolicitud);
   
       filaBotonsolicitud.appendChild(botonSolicitud);
   
      //Agrega al DOM        
      cardbody.appendChild(contenedorTituloImagen);    
      cardbody.appendChild(filaBotonsolicitud);
      card.appendChild(cardbody);
  
      document.getElementById('colCardsBuscar').appendChild(card);
         
    });

    spinner.className="d-none"
         
  }

  function cargaDatosPantallaBuscaIndicadores(rs) {
    
    $('#colCardsBuscarIndicadores').empty();
    $('#modalAgregar_Indicador').modal('show');
    let spinner = document.getElementById("spinnerBuscarIndicador");
    spinner.className="d-block";

    rs.forEach(obj => {
      
      let card = document.createElement('div');
      card.className = "card";    
  
      let cardbody = document.createElement('div');
      cardbody.className = "card-body";
  
      let contenedorTituloImagen = document.createElement('div');
      contenedorTituloImagen.className = "d-flex justify-content-center";
  
      //Fila Col titulo
      let filaTitulo = document.createElement('div');
      filaTitulo.className = "row";
  
      let colTitulo = document.createElement('div');
      colTitulo.className = "col";
  
      let h4 = document.createElement('h4');
      h4.className = "card-title";
      let createATextNombre = document.createTextNode(obj.visitas_estado_inicial_equipo_descripcion);
      h4.appendChild(createATextNombre);
  
      colTitulo.appendChild(h4);
      filaTitulo.appendChild(colTitulo);
      contenedorTituloImagen.appendChild(filaTitulo);
            
       //Boton solicitud
       let filaBotonsolicitud = document.createElement('div');
       filaBotonsolicitud.className = "form-group row justify-content-center";
   
       let botonSolicitud = document.createElement('a');
       botonSolicitud.className = "btn text-white bg-success col-auto text-center";
       botonSolicitud.href = "javascript:void(0)";
       botonSolicitud.onclick = function() { agregarIndicadores(obj); };
       
       let createATextBotonSolicitud = document.createTextNode("Seleccionar");
       botonSolicitud.appendChild(createATextBotonSolicitud);
   
       filaBotonsolicitud.appendChild(botonSolicitud);
   
      //Agrega al DOM        
      cardbody.appendChild(contenedorTituloImagen);    
      cardbody.appendChild(filaBotonsolicitud);
      card.appendChild(cardbody);
  
      document.getElementById('colCardsBuscarIndicadores').appendChild(card);
         
    });

    spinner.className="d-none"
         
  }

  function cargaDatosPantallaArticulo(data) {
      
    if (data && data.length>0) {
          
        let jsonDataArticulo = [];
        jsonDataArticulo = JSON.parse(data);
  
        jsonDataArticulo.forEach(obj => {
            
            let nombreArticulo = obj.clase + " " + obj.marca + " " + obj.modelo;
            let plantilla = `<div class="activos row border-top border-primary mb-2"
                                    id="${obj.id_activo}" data-id="${obj.id_activo}"
                                    data-placaid ="${obj.id_placa}">                
                                <div class="col">
                                    ${nombreArticulo}
                                </div>
                                <div class="col">
                                    ${obj.placa}
                                </div>
                                <div class="col">
                                    ${obj.serie}
                                </div>
                                <div class="col">
                                    <button class="btn btn-outline-danger mt-1"
                                            onclick="botonQuitarArticulo(\'${obj.id_activo}\');">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>            
                            </div>`;
            
            let contenedorPlantilla = document.createElement('span');
            contenedorPlantilla.innerHTML = plantilla;
            document.getElementById('datos').appendChild(contenedorPlantilla);                            
  
       });
  
    }    
    
    return false;
  
  }

  function cargaDatosPantallaIndicadores(data) {
      
    if (data && data.length>0) {

        let nombreClase = "";    
        switch (global_tipo_de_Indicador) {
            case "estado-inicial":
                nombreClase="indicadores"
                break;
        
            default:
                break;
        }
          
        let jsonDataArticulo = [];
        jsonDataArticulo = JSON.parse(data);
  
        jsonDataArticulo.forEach(obj => {


            let plantilla = `<div class="${nombreClase} row border-top border-primary mb-2"
                                id="${obj.visitas_estado_inicial_equipo_id}" data-id="${obj.visitas_estado_inicial_equipo_id}">                
                                <div class="col-10 col-md-10 col-lg-10">
                                    ${obj.visitas_estado_inicial_equipo_descripcion}
                                </div>
                                <div class="col">
                                <button class="btn btn-outline-danger mt-1"
                                        onclick="botonQuitarIndicador(\'${obj.visitas_estado_inicial_equipo_id}\');">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </div>            
                            </div>`;
                                   
            let contenedorPlantilla = document.createElement('span');
            contenedorPlantilla.innerHTML = plantilla;
            document.getElementById('datosIndicadores').appendChild(contenedorPlantilla);                            
  
       });
  
    }    
    
    return false;
  
  }
  
  function cargaDatosPantalla_estado_inicial_equipo(data) {
    
    if (data && data.length>0) {
      
        let jsonDataArticulo = [];
        jsonDataArticulo = JSON.parse(data);
  
        jsonDataArticulo.forEach(obj => {

            let plantilla = `<div class="indicadores row border-top border-primary mb-2"
                                id="${obj.visitas_estado_inicial_equipo_id}" data-id="${obj.visitas_estado_inicial_equipo_id}">                
                                <div class="col-10 col-md-10 col-lg-10">
                                    ${obj.visitas_estado_inicial_equipo_descripcion}
                                </div>
                                <div class="col">
                                <button class="btn btn-outline-danger mt-1"
                                        onclick="botonQuitarIndicador(\'${obj.visitas_estado_inicial_equipo_id}\');">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </div>            
                            </div>`;
                                   
            let contenedorPlantilla = document.createElement('span');
            contenedorPlantilla.innerHTML = plantilla;
            document.getElementById('datosIndicadores').appendChild(contenedorPlantilla);                            
  
       });
  
    }    
    
    return false;
  
  }

  function botonQuitarArticulo(id_activo) {
    
    let tituloMensaje = document.getElementById("tituloMensajeSiNoArticulo");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModalSiNoArticulo");
    contenedorError.innerText='';
    
    tituloMensaje.innerText = 'Aviso importante!' ;
    contenedorError.innerText = 'Realmente desea quitar el artículo de la Hoja de Trabajo ?';
    // Variable Global.
    global_id_activo=id_activo;
    $("#modalMensajeSiNoArticulo").modal('show');

    return false;

  }

  function botonQuitarIndicador(id) {
    
    let tituloMensaje = document.getElementById("tituloMensajeSiNoIndicador");
    tituloMensaje.innerText='';

    let contenedorError = document.getElementById("mensajeModalSiNoIndicador");
    contenedorError.innerText='';
    
    tituloMensaje.innerText = 'Aviso importante!' ;
    contenedorError.innerText = 'Realmente desea quitar este indicador de la Hoja de Trabajo ?';
    // Variable Global.
    global_visitas_estado_inicial_equipo_id=id;
    $("#modalMensajeSiNoIndicador").modal('show');

    return false;

  }

  function quitarElementoArrayArticulo() {

    let cards = document.getElementsByClassName('activos');

    for (let i = 0; i < cards.length; i++) {

        let dataActivo_id = cards[i].getAttribute('data-id');

        if (dataActivo_id==global_id_activo) {        
            let elem = document.getElementById(dataActivo_id);
            elem.parentElement.removeChild(elem);            
        }

    }

    $("#modalMensajeSiNoArticulo").modal('hide');

    return false;
  }

  function quitarElementoArrayIndicador() {

    let nombreClase = ""
    switch (global_tipo_de_Indicador) {
        case "estado-inicial":
            nombreClase="indicadores"
            break;
    
        default:
            break;
    }

    let cards = document.getElementsByClassName(nombreClase);

    for (let i = 0; i < cards.length; i++) {

        let dataActivo_id = cards[i].getAttribute('data-id');

        if (dataActivo_id==global_visitas_estado_inicial_equipo_id) {        
            let elem = document.getElementById(dataActivo_id);
            elem.parentElement.removeChild(elem);            
        }

    }

    $("#modalMensajeSiNoIndicador").modal('hide');

    return false;
  }


  function lista_de_Activos() {

    let contenedor = document.getElementById('datos');
    let activos = contenedor.querySelectorAll('.activos');   
    let arrlista_de_activos = [];
    activos.forEach((activo) => {
        let id_activo = activo?.getAttribute('data-id') || '';
        let id_placa = activo?.getAttribute('data-placaid') || '';               
        arrlista_de_activos.push({id_activo, id_placa});
      });
        
    return arrlista_de_activos;
}

function lista_de_Indicadores() {

    let contenedor = document.getElementById('datosIndicadores');
    let indicadores = contenedor.querySelectorAll('.indicadores');   
    let arrlista_de_indicadores = [];
    indicadores.forEach((indicador) => {
        let visitas_estado_inicial_equipo_id = indicador?.getAttribute('data-id') || 0;
        arrlista_de_indicadores.push({visitas_estado_inicial_equipo_id});
      });
        
    return arrlista_de_indicadores;
}

function ir_a_hoja_visita() {

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
    window.location.replace("formulario_hoja_visita_sitio.html");

    return true;

  }

  
  
  