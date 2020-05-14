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
<input type="hidden" value="0" id="auxiliares_det">
<?php
$anio=2020;
$unidad=5;
//
$sql="SELECT DISTINCT codigo from plan_cuentas where nivel=5 and cuenta_auxiliar=1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nro_registros=0;
$nro_errorescuentas=0;$nro_erroresMontoHaber=0;$nro_erroresMontoDebe=0;$nro_erroresauxiliares=0;
$nro_erroresauxPadre=0;$nro_erroresauxiliaresPadre=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codCuenta=$row['codigo'];
  $nombreCuenta=nameCuenta($codCuenta);
  ?>
  <center><h4>Codigo:<b><?=$codCuenta?></b>, Cuenta: <b><?=$nombreCuenta?></b></h4></center>
  <div class="col-sm-11 div-center">
   <table class="table table-condensed table-striped table-bordered small">
          <tr class="fondo-boton">
            <td>C COMP.</td>
            <td>CUENTA</td>
            <td>MONTO</td>
            <td>DEBE</td>
            <td>HABER</td>
            <td>COD. AUX</td>
            <td>AUX COMP.</td>
            <td>GLOSA</td>
            <td>NOMBRE AUX COMP.</td>
            <td>NUEVO COD AUX</td>  
          </tr>
      <?php 
        $sql2="(SELECT cd.* from comprobantes c, comprobantes_detalle cd where c.codigo=cd.cod_comprobante and cd.cod_cuenta=$codCuenta and c.fecha<='$anio-01-01' and c.cod_gestion=$anio)";
        $stmt2 = $dbh->prepare($sql2);
        $stmt2->execute();
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
          $nro_registros++;
          $codCompDet=$row2['codigo'];
          $codCompEst=$row2['codigo'];
          $selCompDet=$row2['codigo'];
          $selCompEst=$row2['codigo'];

          $montoEstado=$row2['monto'];
          $debe=$row2['debe'];
          $haber=$row2['haber'];
          $codPlan=$row2['cod_cuenta'];
          $codCuentaAux=$row2['cod_cuentaauxiliar'];
          $codCuentaAuxiliar=$row2['cod_cuentaauxiliar'];
          $selCuentaAux=$row2['cod_cuentaauxiliar'];
          $selCuentaAuxiliar=$row2['cod_cuentaauxiliar'];
          $glosa=$row2['glosa'];
          $nomCuentaAux=nameCuentaAux($codCuentaAux);
          $nomCuentaAuxiliar=nameCuentaAux($codCuentaAuxiliar);
          
          $estiloNomCuentaAuxiliar="";
          if (strpos(strtolower(eliminar_acentos($glosa)), strtolower(eliminar_acentos($nomCuentaAuxiliar))) !== false) {
              $estiloNomCuentaAuxiliar="bg-warning";
          }

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

          
          $estiloAuxiliarPadre="";
          if($codCuenta!=obtieneCuentaPadreAux($codCuentaAuxiliar)){
           $estiloAuxiliarPadre="text-danger";
           $codCuentaAuxiliarPadre='PADRE: '.obtieneCuentaPadreAux($codCuentaAuxiliar).' <a href="#" onclick="" class="btn btn-sm btn-danger">CA:'.$codCuentaAuxiliar.'</a>';
           $nro_erroresauxiliaresPadre++;
          }else{
            $codCuentaAuxiliarPadre='PADRE: '.$codCuenta.' <a href="#" onclick="" class="btn btn-sm btn-success">CORRECTO:'.$codCuentaAuxiliar.'</a>';
          }



          $estiloAuxiliar="";
          if($codCuentaAux!=$codCuentaAuxiliar){
           $estiloAuxiliar="text-primary";
           $codCuentaAux.=" / ".$codCuentaAuxiliar;
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
            <td class="<?=$estiloAuxiliarPadre?>"><?=$codCuentaAuxiliarPadre?></td>
            <td><?=$glosa?></td>  
            <td><b class="<?=$estiloNomCuentaAuxiliar?>"><?=$nomCuentaAuxiliar?></b></td>
            <td width="10%">
              <?php //if ($estiloAuxiliar!=""){
                ?>
                <div class="dropdown">
                    <button type="button" class="btn btn-danger dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">save</i>
                    </button>
                    <div class="dropdown-menu">                        
                        <a href='#' onclick="cambiarCuentaAuxiliarDetalle(<?=$codCuenta?>,4,<?=$selCompDet?>,0,<?=$selCuentaAuxiliar?>,0);return false;" class="dropdown-item">NUEVO - COMP</a>
                    </div>
                  </div>
                <?php
              //}
                ?>
            </td>
          </tr>
          <?php
        }
      ?> 
     <tr>
       <td></td>
     </tr>
  </table>
</div>
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
                             <div class="form-group" id="div_codigo_nuevo">
                                <input type="text" readonly class="form-control" name="cod_nuevo" id="cod_nuevo" value="0">
                             </div>
                             <div id="div_codigo_nuevo_sel" class="form-group"></div>
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

<!-- small modal -->
<div class="modal fade modal-primary" id="cambioCodigoAuxiliarManual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
               <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h4>CAMBIO DE CODIGO AUXILIAR MANUAL</h4>      
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
