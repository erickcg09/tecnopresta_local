
window.jsonData = []; //Variable global para almacenar el JSON del ws

window.onload = function() {
  
  sesionCompatibildad();
      
  return false;

};

function sesionCompatibildad() {
  if (typeof(Storage) !== 'undefined') {
    // Código cuando Storage es compatible    
  } else {
   // Código cuando Storage NO es compatible
   let contenedorError = document.getElementById("contenedorError");
   contenedorError.innerHTML='<div class="alert alert-danger">' +
   '<strong>Error! </strong>' +
       'No es compatible con el objeto Storage' +
   '</div>';        
  }

  if (typeof(window.FormData) !== 'undefined') {
    // Código cuando Storage es compatible    
  } else {
   // Código cuando Storage NO es compatible
   let contenedorError = document.getElementById("contenedorError1");
   contenedorError.innerHTML='<div class="alert alert-danger">' +
   '<strong>Error! </strong>' +
       'No es compatible con el objeto window.FormData' +
   '</div>';        
  }
}

$(document).ready(function() {    
  
  $("#fila").on("click", "a", function(event) {
        
        event.preventDefault();        
        //console.log( $(this).data('id'));
        let indice = $(this).data('id');
        inicioSesion(indice);       
        return false;
      });

});    

function inicioSesion(indice) {

  const json = JSON.stringify(jsonData[indice]);
  window.sessionStorage.setItem('sesion',json);
  window.location.replace('formulario_menu_principal.html');
  
  return false;
  
}

function login() {      
        
    let btnIngresar = document.getElementById("btnIngresar");
    btnIngresar.disabled = true;
    let contenedorError = document.getElementById("contenedorError");
    btnIngresar.innerHTML = '<span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    let spinner = document.getElementById("spinner");
    let txtCorreo = $('#txtCorreo').val();
    let txtPass = $('#txtPass').val();

    const formDataLogin = new FormData();    
    formDataLogin.append('correo', txtCorreo.trim());
    formDataLogin.append('pass', txtPass.trim());
  
    fetch('sql/selectLoginGestor.php', {
      method: 'POST', 
      body: formDataLogin,     
    }).then(function(response) {
              if(response.ok) { 
                  response.json().then(function(data) {  
                      
                      //console.log(data);
                      jsonData = data;  // Pasa los datos a la variable global
                                        // que se usará en la función inicioSesion() 

                      //el usario puede tener varios códigos presupuestarios
                      
                      if (Object.keys(data).length==1){ // Un solo código presupuestario
                        
                          inicioSesion(0); // Indice 0 porque es un único elemento 

                      } else if (Object.keys(data).length>1) { //Varios códigos presupuestarios

                        cargaModal();

                      } else if (Object.keys(data).length==0) { //Array vacío []

                            spinner.style.visibility = 'hidden';
                            btnIngresar.innerText="Ingresar";
                            btnIngresar.disabled = false;
                            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                                    '<strong>Error! </strong>' +
                                                        'No se encontró el usuario' +
                                                    '</div>';                        
                      }
                  }).catch(function(error) {
                            spinner.style.visibility = 'hidden';
                            btnIngresar.innerText="Ingresar";
                            btnIngresar.disabled = false;
                            contenedorError.innerHTML='<div class="alert alert-danger">' +
                                                    '<strong>Error! </strong>' +
                                                    'No hay respuesta del servidor MEP. Verifique su conexión de internet ' +
                                                    '</div>';              
                          });              
              }
        })
        .then().catch(function(error) {
                      spinner.style.visibility = 'hidden';
                      btnIngresar.innerText="Ingresar";
                      btnIngresar.disabled = false;
                      contenedorError.innerHTML='<div class="alert alert-danger">' +
                                              '<strong>Error! </strong>' +
                                                  'Hubo un problema con la petición Fetch de login: ' + error.message +
                                              '</div>';        
        });

    return false;    
}

function cargaModal() {
  
  $('.list-group-flush').remove();
  let i=0;

  jsonData.forEach(obj => {
      
      let list = document.createElement('div');
      list.className = "list-group-flush";
      
      let linkList = document.createElement('a');      
      linkList.setAttribute("data-id", i);
      linkList.setAttribute('href', "formulario_menu_principal.html");
      linkList.className = "list-group-item list-group-item-action";
      
      let contenedorCodPre = document.createElement('div');
      contenedorCodPre.className = "d-flex w-100 justify-content-between-group-flush";
      
      let columnaCodigo = document.createElement('div');
      columnaCodigo.className = "col-sm-2";
      
      let h5codigo = document.createElement('h5');
      h5codigo.className = "mb-1";

      let codPre = obj.Codigo_Presupuestario;             
      let createATextCodigo = document.createTextNode(codPre.substr(-4));
      h5codigo.appendChild(createATextCodigo);
      
      columnaCodigo.appendChild(h5codigo);
           
      let columnaNombre = document.createElement('div');
      columnaNombre.className = "col-sm-10";
      
      let h5nombre = document.createElement('h5');
      h5nombre.className = "mb-1";
      let createATextNombre = document.createTextNode(obj.Dependencia);
      h5nombre.appendChild(createATextNombre);
      
      columnaNombre.appendChild(h5nombre); 

      contenedorCodPre.appendChild(columnaCodigo);
      contenedorCodPre.appendChild(columnaNombre);
      linkList.appendChild(contenedorCodPre);
      
      list.appendChild(linkList);
      document.getElementById('fila').appendChild(list);
      
      i=i+1;             
    
  });

  $("#loginModal").modal(); //si hay varios se selecciona uno

  return false;
}