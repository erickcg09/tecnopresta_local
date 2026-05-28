window.onload = function() {
    document.getElementById("txtBuscar").focus();
    let spinner = document.getElementById("spinner");
    spinner.style.visibility = 'hidden'; //'hidden' 'visible'
    return true;
}

function removeAllChildNodes(parent) {
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
}

function buscar() {
    carga_datos();
    return true;
}

function carga_datos() {
    const contenedorError = document.getElementById("mensaje");
    contenedorError.innerHTML='';
    const container = document.querySelector('#filas-datos');
    removeAllChildNodes(container);
    let strBuscar = document.getElementById("txtBuscar").value;
    spinner.style.visibility = 'visible'; //'hidden' 'visible'
    fetch('sql/selectInstitucionGestor.php?'
            + new URLSearchParams({institucion: strBuscar.trim()}))
    .then(function(response){            
        if(response.ok){
            response.json().then(function(data){                                                                                              
              if (Object.keys(data).length>0) {
                //console.log(data);
                carga_pantalla(data);                  
              } else {                  
                  contenedorError.innerHTML='<div class="alert alert-danger">' +
                                          '<strong>Error! </strong>' +
                                          'No se encontraron datos </div>';              
              }              
              spinner.style.visibility = 'hidden'; //'hidden' 'visible'                 
            });    
        }
    }).then();    
    return true;
}

function carga_pantalla(datosInstitucion) {
    
    datosInstitucion.forEach(obj => {

        let fila = document.createElement('div');
        fila.className = "row justify-content-center input-group mb-2";

        let itemCodigo = document.createElement('a');
        itemCodigo.className = "col-2 col-md-2 text-decoration-none fs-5 text-end";
        itemCodigo.setAttribute('href',"#");
        itemCodigo.onclick = new Function("creaSessionStorage(" + JSON.stringify(obj) + "); return false;");
		let createATextItemCodigo = document.createTextNode(obj.codigo);                        
        itemCodigo.appendChild(createATextItemCodigo);

        let itemInstitucion = document.createElement('a');
        itemInstitucion.className = "col-8 col-md-6 text-decoration-none fs-5";
        itemInstitucion.setAttribute('href',"#");
        itemInstitucion.onclick = new Function("creaSessionStorage(" + JSON.stringify(obj) + "); return false;");
		let createATextItemInstitucion = document.createTextNode(obj.institucion);                        
        itemInstitucion.appendChild(createATextItemInstitucion);

        fila.appendChild(itemCodigo);
        fila.appendChild(itemInstitucion);
        //fila.appendChild(columna)
        document.getElementById('filas-datos').appendChild(fila);
        
    });

    return true;

}

function creaSessionStorage(obj){                                      
    window.sessionStorage.setItem('institucion', JSON.stringify(obj));
    paginaDeOrigen = window.sessionStorage.getItem('pagina-origen-buscar');
    ir_a_Pagina(paginaDeOrigen);    
    return true;
}

function ir_a_Pagina(pagina) {
    window.location.replace(pagina);    
    return true;
}
