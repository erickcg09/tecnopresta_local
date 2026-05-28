
let codigoPresupuestario;

window.onload = function() {
  
  let boologin = login();

  if (boologin) {
    
    cargaComboFondos();
     
  }  else {
   
    let contenedorError = document.getElementById("mensaje");
    
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                    '<strong>Error! </strong>' +
                                        'No ha iniciado sesión ...' +
                                    '</div>';

  }
      
  return false;

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

function cargaComboFondos() {
  
  fetch('sql/selectFondos_Gestor.php')
  .then(function(response) 
  {          
    if(response.ok) 
    {
      response.json().then(function(data) 
      {        
        //console.log(data);              
        data.forEach(element => 
          {
            let cboSeccion = document.getElementById("cboFondos"); 
            let opt = document.createElement("option");
            opt.value = element.id_fondos;
            opt.innerHTML = element.fondos;        
            cboSeccion.append(opt);            
          });          
          $("#cboFondos").selectpicker("refresh");    
      });  
    }

  }).then(function(data){});

}

function exportar_excel() {

  /* let cboEstado = document.getElementById("cboEstado");
  let cboEstadoArray = new Array();
  cboEstadoArray = [...cboEstado.options]
                      .filter((x) => x.selected)
                      .map((x)=>x.value);
  let json_cboEstadoArray = new Array();                         
  for (let i = 0; i < cboEstadoArray.length; i++) {            
    json_cboEstadoArray.push({"estado_id":cboEstadoArray[i]});           
  }
  let jsonEstado = JSON.stringify(json_cboEstadoArray);   */
  
/*   fetch('exportar_reporte_centro_de_consultas.php')
        .then(function(response){                    
            if(response.ok){        
                response.json().then(function(data){                                                                                                      
                    if (Object.keys(data).length>0) {        
                        //console.log(data);                        
                    }                                       
                });                                           
            }}).then();
 */    
    let data = new FormData();
    fetch('exportar_reporte_centro_de_consultas.php',{
        method: 'POST', 
        body: data,     
    }).then(function(response){
        if(response.ok) {                       
          response.text().then(function(data){  
            //console.log(data);                         
          }).catch(function(error){                        
            console.log(error);                
          });
        }}).catch(function(error){
             console.log(error);                    
        }).then();                
}