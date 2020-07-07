<?php

require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$v=$v;
$q=$q;
$s=$s;
$u=$u;

//sacamos datos para la facturacion
if(isset($_GET['v'])){
  // echo "string";
    $idPropuesta=obtenerIdPropuestaServicioIbnorca($v);
    $areaServicio=obtenerIdAreaServicioIbnorca($v);
     if($areaServicio==39||$areaServicio==38){
      $detalle="TCP"; //TCP Y TCS
     }else{
       $detalle="SIM"; // CAPACITACION SEC
     }     
    if($idPropuesta!="NONE"){//servicio CON PROPUESTA  ?>
      
      <script type="text/javascript">
        location = "<?=$urlSolicitudfactura;?>&cod=<?=$idPropuesta?>&cod_f=0&cod_sw=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>"
      </script>

    <?php }else{ //servicio SIN PROPUESTA "OI" ?>
        <script type="text/javascript">
        location = "<?=$urlRegisterSolicitudfactura_sp?>&cod_simulacion=0&IdServicio=<?=$v?>&cod_facturacion=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>"
      </script>
      
    <?php }
  }


?>
