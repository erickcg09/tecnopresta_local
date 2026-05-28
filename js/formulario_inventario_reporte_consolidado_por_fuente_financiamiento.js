
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

function cargaDatosBd() {
 
    let contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='<div class="alert alert-danger">' +
                                'Proceso iniciado. La desacarga de la información puede demorar varios minutos' +
                              '</div>';
   
  return false;
}

function limpiarMensaje() {

  let contenedorError = document.getElementById("mensaje");
  contenedorError.innerHTML='';
  
  return true;

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