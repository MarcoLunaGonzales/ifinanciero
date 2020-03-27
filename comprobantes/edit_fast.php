<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$codigo=$_GET['codigo'];
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div id="pantalla_principal_comprobante">

</div>	
<script>
$(document).ready(function() {
var parametros={"codigo":'<?=$codigo?>'};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "edit_prueba.php",
        data: parametros,
        beforeSend: function () { 
          iniciarCargaAjax();
        },
        success:  function (resp) {
        	detectarCargaAjax();
        	$("#pantalla_principal_comprobante").html(resp);
            
        }
    });
 });

</script>