
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

function imprimir() {    

        
      document.getElementById('menu').style.display ='none';
      document.getElementById('contenedor').style.display ='none';
      document.getElementById('boton').style.display ='none';
      document.getElementById('encabezado').style.display ='block';
      document.getElementById('contenedorFuente').style.display ='block';
      let tituloFuente = $("#cboFondos option:selected").text();
      document.getElementById('fuente').innerText=tituloFuente;
      document.getElementById('firma').style.display ='block';
              
      window.print();
     
      document.getElementById('menu').style.display ='block';
      document.getElementById('contenedor').style.display ='block';
      document.getElementById('boton').style.display ='block';
      document.getElementById('fuente').innerText='';
      document.getElementById('encabezado').style.display ='none';
      document.getElementById('contenedorFuente').style.display ='none';
      document.getElementById('firma').style.display ='none';

    return true;

}

function cargaDatosBd(id_fondos) {

  //console.log(id_fondos);
  fetch('sql/selectActivos_Ubicacion_por_Fondos_Gestor.php?' 
          + new URLSearchParams({id_fondos: id_fondos, codigo: codigoPresupuestario}))
    .then(function(response) {

    if(response.ok) {

      response.json().then(function(data) {  
       
          //console.log(data);
          if (Object.keys(data).length>0) {
            cargaDatosPantalla(data);
          } else {
            limpiaTotales();
            $('#plantilla').empty();
            let contenedorError = document.getElementById("mensaje");
            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                            'No hay datos para mostrar' +
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

  
  return false;

}

function limpiaTotales() {

  let muybuena = document.getElementById("muybuena");
  let buena = document.getElementById("buena");
  let regular = document.getElementById("regular");
  let mala = document.getElementById("mala");
  let robado = document.getElementById("robado");  
  let totalEstado = document.getElementById("totalEstado");
  let totalUtilizado = document.getElementById("totalUtilizado");  
  let totalDonados = document.getElementById("totalDonados");
    
  muybuena.innerHTML='';
  buena.innerHTML='';
  regular.innerHTML='';
  mala.innerHTML='';
  robado.innerHTML='';
  totalEstado.innerHTML='';
  totalUtilizado.innerHTML='';
  totalDonados.innerHTML='';

  let contenedorError = document.getElementById("mensaje");
  contenedorError.innerHTML='';
  
}

function cargaDatosTotales(rs) {

  limpiaTotales();

  rs.forEach(obj => {

    console.log(obj);
    let createATextmuybuena = document.createTextNode(obj.sumMuybuena);
    muybuena.appendChild(createATextmuybuena);
        
    let createATextbuena = document.createTextNode(obj.sumBuena);
    buena.appendChild(createATextbuena);

    let createATextregular = document.createTextNode(obj.sumRegular);
    regular.appendChild(createATextregular);

    let createATextmala = document.createTextNode(obj.sumMala);
    mala.appendChild(createATextmala);

    let createATextrobado = document.createTextNode(obj.sumRobado);
    robado.appendChild(createATextrobado);

    let createATexttotalEstado = document.createTextNode(obj.sumTotalEstado);
    totalEstado.appendChild(createATexttotalEstado);

    let createATexttotalUtilizado = document.createTextNode(obj.sumTotalUtilizado);
    totalUtilizado.appendChild(createATexttotalUtilizado);
        
    let createATexttotalDonados = document.createTextNode(obj.sumTotalDonados);
    totalDonados.appendChild(createATexttotalDonados);
    
 });      

  return false;
  
}

function cargaDatosPantalla(rs) {
  
  $('#plantilla').empty();

  let sumMuybuena = 0;
  let sumBuena = 0;
  let sumRegular = 0;
  let sumMala = 0;
  let sumRobado = 0;
  let sumTotalEstado = 0;
  let sumTotalUtilizado = 0;  
  let sumTotalDonados = 0;
  
  rs.forEach(obj => {
      
      let plantilla = document.getElementById("plantilla");
      
      let row = document.createElement('div');
      row.className = "form-group row";
      row.id = "item";                  
            
      let colNombre = document.createElement('div');
      colNombre.className = "col-2";
      let nombreArticulo = obj.clase + " " + obj.modelo + " " + obj.marca;
      let createATextNombre = document.createTextNode(nombreArticulo);
      colNombre.appendChild(createATextNombre);
                   
      //Placa
      let colPlaca = document.createElement('div');
      colPlaca.className = "col-2";      
      let createATextPlaca = document.createTextNode(obj.placa);
      colPlaca.appendChild(createATextPlaca);
            
      //Estado
      let colEstado = document.createElement('div');
      colEstado.className = "col-2";
     
      switch (obj.id_estado) {
        case 1:
            sumMuybuena = sumMuybuena+1;                   
            colEstado.innerHTML='Muy Buena';
            break;
        case 2:
            sumBuena = sumBuena+1;                               
            colEstado.innerHTML='Bueno';            
            break;                        
        case 3:
            sumRegular = sumRegular+1;
            colEstado.innerHTML='Regular';               
            break;
        case 4:
            sumMala=sumMala+1;            
            colEstado.innerHTML='Malo';
            break;
         case 5:
            sumRobado = sumRobado+1;
            colEstado.innerHTML='Robado o Hurtado';
            break;                                                         
      }

      //Ubicacion
      let colUbicacion = document.createElement('div');
      colUbicacion.className = "col-2";
      let createATextUbicacion = document.createTextNode(obj.lugar);
      colUbicacion.appendChild(createATextUbicacion);
            
     //Se usa si no
     //Estado
     let colSiNo = document.createElement('div');
     colSiNo.className = "col-2";
      
      switch (obj.enuso) {
        case 1:
            sumTotalUtilizado=sumTotalUtilizado+1;
            colSiNo.innerHTML=  'Sí';          
          break;      
        default:
            colSiNo.innerHTML=  'No';
        break;
      }     

      //Donacion
      let colDonar = document.createElement('div');
      colDonar.className = "col-2";
      switch (obj.donar) {
        case 1:
            sumTotalDonados=sumTotalDonados+1;
            colDonar.innerHTML=  'Sí';                   
          break;      
        default:
            colDonar.innerHTML=  'No';
          break;
      }
           
      row.appendChild(colNombre);
      row.appendChild(colPlaca);
      row.appendChild(colEstado);
      row.appendChild(colUbicacion);
      row.appendChild(colSiNo);
      row.appendChild(colDonar);            
      plantilla.appendChild(row);            
 
    });
    
    sumTotalEstado= sumMuybuena + sumBuena + sumRegular + sumMala + sumRobado;

    let jsonDataTotales = [];
    jsonDataTotales.push({sumMuybuena:sumMuybuena,
                          sumBuena:sumBuena,
                          sumRegular:sumRegular,
                          sumMala:sumMala,
                          sumRobado:sumRobado,
                          sumTotalEstado:sumTotalEstado,                         
                          sumTotalUtilizado:sumTotalUtilizado,
                          sumTotalDonados:sumTotalDonados});

    cargaDatosTotales(jsonDataTotales);

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