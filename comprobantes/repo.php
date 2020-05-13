<?php
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();


?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<?php
$anio=2020;
$unidad=5;
//
$sql="SELECT DISTINCT cod_plancuenta FROM estados_cuenta";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nro_registros=0;
$nro_errorescuentas=0;$nro_erroresMontoHaber=0;$nro_erroresMontoDebe=0;$nro_erroresauxiliares=0;
$nro_erroresauxPadre=0;$nro_erroresauxiliaresPadre=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codCuenta=$row['cod_plancuenta'];
  $nombreCuenta=nameCuenta($codCuenta);
  ?>
  <center><h4>Codigo:<b><?=$codCuenta?></b>, Cuenta: <b><?=$nombreCuenta?></b></h4></center>
   <table class="table table-condensed table-striped">
          <tr>
            <td>COD COMP DET</td>
            <td>CUENTA</td>
            <td>MONTO</td>
            <td>DEBE</td>
            <td>HABER</td>
            <td>C AUXILIAR</td>
            <td>AUX ESTADO</td>
            <td>AUX COMPROBANTE</td> 
          </tr>
      <?php 
        $sql2="SELECT e.*,d.haber,d.debe,d.glosa as glosa_comp,d.cod_cuentaauxiliar from estados_cuenta e ,(select cd.* from comprobantes c, comprobantes_detalle cd where c.codigo=cd.cod_comprobante and cd.cod_cuenta=$codCuenta and c.cod_unidadorganizacional=$unidad and c.fecha<='$anio-01-01' and c.cod_gestion=$anio) d where e.cod_comprobantedetalle=d.codigo";
        $stmt2 = $dbh->prepare($sql2);
        $stmt2->execute();
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
          $nro_registros++;
          $codCompDet=$row2['cod_comprobantedetalle'];
          $codCompEst=$row2['codigo'];
          $montoEstado=$row2['monto'];
          $debe=$row2['debe'];
          $haber=$row2['haber'];
          $codPlan=$row2['cod_plancuenta'];
          $codCuentaAux=$row2['cod_cuentaaux'];
          $codCuentaAuxiliar=$row2['cod_cuentaauxiliar'];

          $estilo="";
          if($codPlan!=$codCuenta){
           $estilo="text-danger";
           $codPlan.=" C: ".$codCuenta;
           $nro_errorescuentas++;
          }
           
           if($haber==0){
            $estiloMonto="";
             if($montoEstado!=$debe){
              $estiloMonto="text-warning";
              $montoEstado.=" D:".$debe;
              $nro_erroresMontoDebe++;
             }
           }else{
            $estiloMonto="";
             if($montoEstado!=$haber){
              $estiloMonto="text-info";
              $montoEstado.=" H:".$haber;
              $nro_erroresMontoHaber++;
             }
           }

           
          
          $estiloAuxPadre="";
          if($codCuenta!=obtieneCuentaPadreAux($codCuentaAux)){
           $estiloAuxPadre="text-danger";
           $codCuentaAuxPadre='PADRE: '.obtieneCuentaPadreAux($codCuentaAux).' <a href="#" onclick="cambiarCuentaAuxiliarDetalle(1,0,'.$codCompEst.','.$codCuentaAux.','.$codCuentaAuxiliar.')" class="btn btn-sm btn-danger">CA:'.$codCuentaAux.' -> '.$codCuentaAuxiliar.'</a>';
           $nro_erroresauxPadre++;
          }else{
            $codCuentaAuxPadre='<a href="#" onclick="" class="btn btn-sm btn-success">CORRECTO:'.$codCuentaAux.'</a> PADRE: '.$codCuenta;
          }
          
          $estiloAuxiliarPadre="";
          if($codCuenta!=obtieneCuentaPadreAux($codCuentaAuxiliar)){
           $estiloAuxiliarPadre="text-danger";
           $codCuentaAuxiliarPadre='PADRE: '.obtieneCuentaPadreAux($codCuentaAuxiliar).' <a href="#" onclick="cambiarCuentaAuxiliarDetalle(2,'.$codCompDet.',0,'.$codCuentaAuxiliar.','.$codCuentaAux.')" class="btn btn-sm btn-danger">CA:'.$codCuentaAuxiliar.' -> '.$codCuentaAux.'</a>';
           $nro_erroresauxiliaresPadre++;
          }else{
            $codCuentaAuxiliarPadre='PADRE: '.$codCuenta.' <a href="#" onclick="" class="btn btn-sm btn-success">CORRECTO:'.$codCuentaAuxiliar.'</a>';
          }



          $estiloAuxiliar="";
          if($codCuentaAux!=$codCuentaAuxiliar){
           $estiloAuxiliar="text-primary";
           $codCuentaAux.=" A: ".$codCuentaAuxiliar;
           $nro_erroresauxiliares++;
          }

          ?>
          <tr>
            <td><?=$codCompDet?></td>
            <td class="<?=$estilo?>"><?=$codPlan?></td>
            <td class="<?=$estiloMonto?>"><?=$montoEstado?></td>
            <td><?=$debe?></td>
            <td><?=$haber?></td>
            <td class="<?=$estiloAuxiliar?>"><?=$codCuentaAux?></td>
            <td class="<?=$estiloAuxPadre?>"><?=$codCuentaAuxPadre?></td> 
            <td class="<?=$estiloAuxiliarPadre?>"><?=$codCuentaAuxiliarPadre?></td>  
          </tr>
          <?php
        }
      ?> 
     <tr>
       <td></td>
     </tr>
  </table>
  <?php 
}
?>
<br><br><br><br><br><br>
<div class="card-footer fixed-bottom">
  <a onclick="" class="btn btn-danger text-white"><i class="material-icons">warning</i> Errores en cuentas <?=$nro_errorescuentas?>/<?=$nro_registros?></a>
  <a onclick="" class="btn btn-warning text-white"><i class="material-icons">warning</i> Errores en DEBE <?=$nro_erroresMontoDebe?>/<?=$nro_registros?></a>
  <a onclick="" class="btn btn-info text-white"><i class="material-icons">warning</i> Errores en HABER <?=$nro_erroresMontoHaber?>/<?=$nro_registros?></a>
  <a onclick="" class="btn btn-primary text-white"><i class="material-icons">warning</i> Errores en CUENTA AUXILIAR <?=$nro_erroresauxiliares?>/<?=$nro_registros?></a>
</div>


<!-- small modal -->
<div class="modal fade modal-primary" id="cambioCodigoAuxiliar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
               <div class="card-header card-header-primary card-header-text">
                  <div class="card-text">
                    <h4>CAMBIO DE CODIGO AUXILIAR</h4>      
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                  <input type="hidden" name="tipo" id="tipo" value="0">
                    <input type="hidden" name="cod_comprobantedetalle" id="cod_comprobantedetalle" value="0">
                    <input type="hidden" name="cod_estadocuenta" id="cod_estadocuenta" value="0">
                      <div class="row">
                          <label class="col-sm-2 col-form-label">CODIGO ANTIGUO</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="text" readonly class="form-control" name="cod_antiguo" id="cod_antiguo" value="0">
                             </div>
                           </div>
                           <label class="col-sm-2 col-form-label">CODIGO NUEVO</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                                <input type="text" readonly class="form-control" name="cod_nuevo" id="cod_nuevo" value="0">
                             </div>
                           </div>           
                      </div>
                    
                      <hr>
                      <div class="form-group float-right">
                        <button type="button"  class="btn btn-danger" onclick="cambiarCodigoAuxiliar()">CAMBIAR</button>
                      </div> 
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->