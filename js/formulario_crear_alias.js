
 $(document).ready(function() {    
    
  $("#fila").on("click", "a", function(event) {
        
        event.preventDefault();        
        //console.log( $(this).data('id'));
        var nombreArchivo = $(this).data('id');
        if (nombreArchivo != "") {
          cargaImagen_x_Nombre(nombreArchivo);
          $('#imagenModal').modal('hide'); ;//Cierra el Modal form
        }

     });

  });    

function cargaImagen() {  
  
    $('#columna').remove();
  
    $.getJSON("sql/selectImagenGestor.php").done(
      function(data){             
       // console.log(data); 
      $.each(data, function(i, linkData) { 
         
        var columna = document.createElement('div');
        columna.className = "col-md-4";
        columna.id = "columna";        

        var linkImagen = document.createElement('a');
        linkImagen.id = "linkImagen";
        linkImagen.setAttribute("data-id", linkData.archivo)
        linkImagen.setAttribute('href', "javascript:void(0)");
        linkImagen.innerHTML = '<img class="card-img-top" src="img/alias/' + linkData.archivo + '">';
        linkImagen.innerHTML = linkImagen.innerHTML + '<p class="text-center"><small class="text-primary">' + linkData.nombre + '</small></p>';

        columna.appendChild(linkImagen);
        document.getElementById('fila').appendChild(columna);              
        
      });
        
      }).fail(function(jqXHR, textStatus, error) {			
      console.log("Error de la aplicación: " + error);            
      });
  
      return false;
  
  }
  
  function cargaImagen_x_Nombre(nombreArchivo) {     
    
    $('.card').remove();
         
    var card = document.createElement('div');
    card.className = "card";
    
    var rownogutters = document.createElement('div');
    rownogutters.className = "row no-gutters";

    // Imagen
    var colmd4 = document.createElement('div');
    colmd4.className = "col-md-8";

    var cardimg = document.createElement('img');
    cardimg.className = "card-img";      
    cardimg.src = './img/alias/' + nombreArchivo;
  
    colmd4.appendChild(cardimg);
    rownogutters.appendChild(colmd4);//Imagen
    card.appendChild(rownogutters);      
    document.getElementById('colCards').appendChild(card);    

    document.getElementById('imagen').value=nombreArchivo;

    return false;
  
  }

  window.onload = function() {

    $('.card').remove();
    cargaImagen();
    
    return false;
  
  };