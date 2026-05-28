window.onload = function() {

  cargaComboEstado();
  sessionStorage.removeItem('id_visita');
}

function obtener_cedula() {

  window.userData = [];
  userData = window.sessionStorage.getItem('sesion');
  
  if (userData && userData.length>0) {
    let jsonData = [];
    jsonData = JSON.parse(userData);
    cedula = jsonData["EMPCED"];
  } else {
    return false;    
  }

  return cedula;

}

function cargarVisitasSitio() {

    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='';
    let spinner = document.getElementById("spinner");
    spinner.className="d-block"

    let cedula = obtener_cedula();
    if (cedula==false) {

      let contenedorError = document.getElementById("mensaje");    
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                      '<strong>Error! </strong>' +
                                          'No ha iniciado sesión ...' +
                                      '</div>';
      return false;  

    }

    let cboEstado = document.getElementById("cboEstado");
    let estado = cboEstado.value;
    if (estado==0) {
      spinner.className="d-none"; 
      document.getElementById('filas-datos').innerHTML='';
      return false;
    }

    try {        
        fetch('sql/selectVisitasAsignadas_x_cedulaGestor.php?'
            + new URLSearchParams({cedula: cedula, estado: estado}))
        .then(function(response){                    
            if(response.ok){        
                response.json().then(function(data){
                    if (Object.keys(data).length>0) {                        
                        carga_pantalla(data);                          
                    } else {                         
                      let contenedorError = document.getElementById("mensaje");
                      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                                  '<strong>No hay información para mostrar</strong>' +
                                                  '</div>';                      
                    }                                         
                });                    
                let spinner = document.getElementById("spinner");           
                spinner.className="d-none";
                document.getElementById('filas-datos').innerHTML='';
            }        
        }).then();
                
    } catch (error) {
        let contenedorError = document.getElementById("mensaje");
        contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          error + '</div>';
        let spinner = document.getElementById("spinner");           
        spinner.className="d-none";
        document.getElementById('filas-datos').innerHTML='';                  
    }   
}

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
        });  
      }}).then();
}

function carga_pantalla(data) {
  try {
      data.forEach(obj => {           
        
        let fecha = obj.fecha_visita;
        let [year, month, day] = fecha.split('-');
        let formattedDate = `${day}-${month}-${year}`;

        let hora = obj.hora_visita;
        let now = new Date(fecha + " " + hora);
        let formatter = new Intl.DateTimeFormat('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true,
        });
        let formattedTime = formatter.format(now);      

        let contenedorPlantilla = document.createElement('span');
        let plantilla = `<div class="row justify-content-center mt-2 mb-3">
                            <div class="col col-md-8">                                
                                <div class="row input-group border-top border-2 border-primary"
                                    style="font-family: Georgia, serif;">
                                    <div class="row justify-content-center mt-2">
                                      <span class="col-auto badge bg-danger">
                                              ${"Prioridad " + obj.prioridad}                    
                                      </span>
                                    </div>
                                    <div class="row mt-2">                    
                                        <div class="col text-center h4 fw-bold">${obj.nombre_institucion}</div>                                        
                                    </div>
                                    <div class="row">
                                        <div class="col text-center fs-5">Fecha y hora de la visita:</div>                
                                    </div>
                                    <div class="row">
                                        <div class="col text-center h5">${formattedDate + " " + formattedTime}</div>    
                                    </div>                                            
                                    <div class="row">
                                        <div class="col text-center fs-5 fst-italic">${obj.titulo_problema}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col text-center">${obj.descripcion_problema}</div>    
                                    </div>                
                                    <div class="row justify-content-center">
                                        <div class="col text-end mt-1">
                                            <button class="btn btn-primary fs-5 fw-bold"
                                              onclick="ir_a_hoja_visita(\'${obj.id_visita}\');"
                                              >
                                                <i class="bi bi-ui-checks"                                    
                                                    > Hoja de Visita
                                                </i>                                                                
                                            </button>
                                        </div>
                                        <div class="col mt-1">
                                            <button class="btn btn-success fs-5 fw-bold"
                                              onclick="ir_a_hoja_trabajo(\'${obj.id_visita}\');"
                                              >
                                                <i class="bi bi-gear"                                    
                                                > 
                                                    Hoja de Trabajo
                                                </i>                                   
                                            </button>
                                        </div>                        
                                    </div>                                
                                </div>                        
                            </div>
                        </div>`;
        contenedorPlantilla.innerHTML = plantilla;
        document.getElementById('filas-datos').appendChild(contenedorPlantilla);
      });                 
  } catch (error) {
      let contenedorError = document.getElementById("mensaje");
      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                        '<strong>Error! </strong>' +
                                        error + '</div>';  
  }
  return true;
}

function ir_a_hoja_trabajo(id_visita) { 
  window.sessionStorage.setItem('id_visita', id_visita);
  window.location.replace("formulario_hoja_trabajo_visitas_sitio.html");
  return true;
}

function ir_a_hoja_visita(id_visita) {
  window.sessionStorage.setItem('id_visita', id_visita);
  window.location.replace("formulario_hoja_visita_sitio.html");
  return true;
}