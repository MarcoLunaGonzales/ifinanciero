<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';

$dbh = new Conexion();

$resp1=$_GET['resp1'];

$responsable1=namePersonalCompleto($resp1);
$query = "SELECT codigo,codigoactivo,activo,cod_area,cod_unidadorganizacional from activosfijos where cod_responsables_responsable=$resp1 and cod_estadoactivofijo=1 order by codigoactivo";
$stmt = $dbh->query($query);
?>
<div class="row">
  
  <div class="col-sm-12">
    <div class="form-group"> 
      <div class="table-responsive">
        <table class="table table-bordered table-condensed table-sm">
          <thead>
            <tr class="fondo-boton">              
              <th><small><small><b>N°</b></small></small></th>
              <th><small><small><b>Codigo</b></small></small></th>
              <th><small><small><b>OF/Area</b></small></small></th>
              <th><small><small><b>Activo</b></small></small></th>
              <th width="20%"><small><small><b>Respo1</b></small></small></th>              
            </tr>
          </thead>
          <tbody>
            <?php
            $String_detalle="";
            $sw_aux=0;  
            $contador=0;
            while ($row = $stmt->fetch()){ 
              $String_detalle="123132";
              $contador++;
              $sw_aux=1;
              $codigo=$row["codigo"];
              $codigoactivo=$row["codigoactivo"];
              $activo=$row["activo"];
              $cod_area=$row["cod_area"];
              $cod_unidadorganizacional=$row["cod_unidadorganizacional"];              
              $nombrearea=abrevArea($cod_area);
              $nombreuo=abrevUnidad($cod_unidadorganizacional);
              ?>
              <tr>
                <td><small><small><?=$contador?></small></small></td>
                <td><small><small><?=$codigoactivo?></small></small></td>
                <td><small><small><?=$nombreuo?>/<?=$nombrearea?></small></small></td>
                <td class="text-left"><small><small><?=$activo?></small></small></td>
                <td class="text-left"><small><small><?=$responsable1?></small></small></td>                
              </tr>
            <?php }
            if($String_detalle==""){
              $String_detalle="NO ENCONTRADO";
            }
            ?>
          </tbody>
        </table>
      </div>
      <input type="hidden" name="detalle_comprobante" id="detalle_comprobante" class="form-control" readonly value="<?=$String_detalle?>" />
      <?php 
        if($sw_aux==0){?>
          <textarea name="detalle_comprobante" id="detalle_comprobante" class="form-control" readonly>   <?=$String_detalle?></textarea>
      <?php } ?>
    </div>  
  </div>
</div>


