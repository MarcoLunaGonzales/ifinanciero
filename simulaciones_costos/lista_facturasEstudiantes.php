<?php

require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'styles.php';

require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
$dbh = new Conexion();

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

$url_list_siat=obtenerValorConfiguracion(103);

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$ci=$_GET['ci'];
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}
$sql="SELECT f.cod_estadofactura,f.cod_solicitudfacturacion,f.codigo,f.nro_factura,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,DATE_FORMAT(f.fecha_factura,'%H:%i:%s')as hora_factura_x,f.nit,f.razon_social,IFNULL(idTransaccion_siat,0)as facturasiat from facturas_venta f, facturas_ventadetalle fd where f.codigo=fd.cod_facturaventa and fd.ci_estudiante=$ci GROUP BY f.codigo order by f.nro_factura";  
?>
<div class="content">
  <div class="container-fluid">
    <div style="overflow-y:scroll;">
      <div class="col-md-12">
      <form id="form111" class="form-horizontal" action="<?=$urlregistro_solicitud_facturacion_grupal_est;?>" method="post" onsubmit="return valida(this)">
        <?php
            if(isset($_GET['q'])){?>
              <input type="hidden" name="q" id="q" value="<?=$q?>">
              <input type="hidden" name="r" id="r" value="<?=$r?>">
              <input type="hidden" name="s" id="s" value="<?=$s?>">
              <input type="hidden" name="u" id="u" value="<?=$u?>">
            <?php }
            ?>     
        <div class="card">
          <div class="card-header card-header-warning card-header-icon">
            <div class="card-icon">
              <i class="material-icons">polymer</i>
            </div>
            <h4 class="card-title"><b>Facturas Generadas</b></h4>            
          </div>          
          <div class="card-body">                      
            <table class="table table-sm" id="tablePaginator50">
              <thead>
                  <tr>
                    <th width="6%">#Fac</th>
                    <th width="8%">Fecha<br>Factura</th>
                    <th width="5%">CI Alumno</th>
                    <th>Nombre</th>
                    <th width="25%">Razón Social</th>
                    <th width="9%">Nit</th>
                    <th width="9%">Estado</th>
                    <th width="10%" class="text-right">Opciones</th>
                  </tr>
              </thead>
              <tbody>                                
                  <?php 
                  $iii=1;
                  // $queryPr="SELECT * from ibnorca.ventanormas where (idSolicitudfactura=0 or idSolicitudfactura is null) order by Fecha desc limit 20";
                  // echo $queryPr;
                  $stmt = $dbh->prepare($sql);
                  $stmt->execute();
                  $stmt->bindColumn('codigo', $codigo);
                  $stmt->bindColumn('nro_factura', $nro_factura);
                  $stmt->bindColumn('nit', $nit);
                  $stmt->bindColumn('razon_social', $razon_social);
                  $stmt->bindColumn('fecha_factura_x', $fecha_factura_x);                  
                  $stmt->bindColumn('hora_factura_x', $hora_factura_x);
                  $stmt->bindColumn('cod_estadofactura', $cod_estadofactura);
                  $stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
                  $stmt->bindColumn('facturasiat', $facturaSiat);

                  while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $nombreAlumno=obtenerNombreEstudiante($ci);  
                    $correosEnviados=obtenerCorreosEnviadosFactura($codigo);  
                    $estadofactura=obtener_nombreestado_factura($cod_estadofactura);

                    /*ARMAMOS LA URL PARA LA VISTA DE LAS FACTURAS*/
                    $urlFacturaImprimir="";
                    if($facturaSiat==0){
                        $urlFacturaImprimir="simulaciones_servicios/generarFacturasPrint.php?codigo=".$codigo."&tipo=1";
                    }else{
                        $urlFacturaImprimir=$url_list_siat."formatoFacturaOnLine.php?codVenta=".$facturaSiat."";
                    }

                    switch ($cod_estadofactura) {
                      case 1://activo
                        $label='<span class="badge badge-success">';
                        break;
                      case 2://anulado
                        $label='<span class="badge badge-danger">';
                        //$observaciones_solfac = obtener_observacion_factura($cod_solicitudfacturacion);
                        break;
                      case 3://enviado
                        $label='<span class="badge badge-info">';
                        break;
                    }                
                    ?>
                    <tr>                      
                      <td class="text-right small"><?=$nro_factura;?></td>
                      <td class="text-right small"><?=$fecha_factura_x?><br><?=$hora_factura_x?></td>
                      <td class="text-right small"><?=$ci;?></td>
                      <td class="text-left small"><?=$nombreAlumno;?></td>
                      <td class="text-left small"><?=mb_strtoupper($razon_social);?></td>
                      <td class="text-left small"><?=$nit;?></td>
                      <td class="text-left small"><?=$label.$estadofactura;?></span></td>
                      <td class="td-actions text-right">
                        <a title="Imprimir Factura. <?=$correosEnviados?>" class="btn btn-success" href='<?=$urlFacturaImprimir;?>' target="_blank"><i class="material-icons">print</i></a>
                        <a  class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$cod_solicitudfacturacion;?>' target="_blank" title="Imprimir Solicitud Facturación"><i class="material-icons">print</i></a>
                      </td>
                    </tr>
                    <?php   
                      $iii++;
                  } ?>                   
              </tbody>              
            </table>         
          </div>
          <div class="card-footer fixed-bottom">            
            <?php
            if(isset($_GET['q'])){?>
              <a href='<?=$urlSolicitudfactura?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Volver Atrás">keyboard_return</i> Volver</a>
              <?php }else{?>
                  <a href='<?=$urlSolicitudfactura?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Volver Atrás">keyboard_return</i> Volver</a>                    
            <?php }                     
              ?> 
          </div>
        </div>
      </form>
      </div>
    </div>  
  </div>
</div>
<script type="text/javascript">
    function valida(f) {
      var ok = true;
      var msg = "Habilite los Items que desee Solicitar la factura...\n";  
      var aux=f.elements["contador_auxiliar"].value;
      // alert(aux);
      if(aux == 0 || aux < 0 || aux == '')
      {    
        ok = false;
      }
      if(ok == false)    
        Swal.fire("Informativo!",msg, "warning");
      return ok;
    }
</script>