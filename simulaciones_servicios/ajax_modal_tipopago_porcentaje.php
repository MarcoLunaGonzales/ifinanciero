<?php
require_once 'configModule.php';
$cod_tipopago=$_GET['cod_tipopago'];
$monto_total=$_GET['monto_total'];
?>
<label class="col-sm-4 col-form-label text-right" style="color:#000000; ">Monto Total de Items</label>
<div class="col-sm-6">
  <div class="form-group">
    <input type="hidden" name="monto_total_ingreso_tipopago" id="monto_total_ingreso_tipopago" value="<?=$monto_total?>" readonly="true">
    <input type="number" class="form-control"  value="<?=number_format($monto_total,2,".","");?>" readonly="true" style="background-color:#E3CEF6;text-align: left">
  </div>
</div>  

<!-- <table class="table table-bordered table-condensed table-sm">
   <thead>
        <tr class="fondo-boton">          
          <th>#</th>
          <th>Tipo de Pago</th>
          <th>Porcentaje(%)</th>
          <th>Monto(BOB)</th>          
        </tr>
    </thead>
    <tbody>                                
      <?php 
      $iii=1;            
      $queryPr="SELECT * from tipos_pago ";
     // echo $queryPr;
      $stmt = $dbh->prepare($queryPr);
      $stmt->execute();
      while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) { 
        $codigoX=$rowPre['codigo'];
        $nombreX=$rowPre['nombre'];        
        ?>

        
        <tr>
          <td><?=$iii?></td>                                                  
          <td><input type="hidden" name="codigo_tipopago<?=$iii?>" id="codigo_tipopago<?=$iii?>" value="<?=$codigoX?>"><?=$nombreX?></td>  
          <?php            
            if($cod_tipopago==$codigoX){?>
              <td class="text-right"><input type="number" step="0.01" class="form-control" name="monto_porcentaje_tipopago<?=$iii?>" id="monto_porcentaje_tipopago<?=$iii?>" onkeyup="convertir_a_bolivianos_tipopago(<?=$iii?>)" value="100"></td>
              <td class="text-right"><input type="number" class="form-control" name="monto_bob_tipopago<?=$iii?>" id="monto_bob_tipopago<?=$iii?>" onkeyup="convertir_a_porcentaje_tipopago(<?=$iii?>)" value="<?=$monto_total?>"></td>
            <?php }else{?>
              <td class="text-right"><input type="number" step="0.01" class="form-control" name="monto_porcentaje_tipopago<?=$iii?>" id="monto_porcentaje_tipopago<?=$iii?>" onkeyup="convertir_a_bolivianos_tipopago(<?=$iii?>)"></td>
              <td class="text-right"><input type="number" class="form-control" name="monto_bob_tipopago<?=$iii?>" id="monto_bob_tipopago<?=$iii?>" onkeyup="convertir_a_porcentaje_tipopago(<?=$iii?>)"></td>
            <?php }?> 
        
        </tr>
        <?php $iii++;      
      } ?>   
      <tr>
          <td></td>                                                  
          <td>TOTAL</td>   
          <td class="text-right">
            <input type="hidden" name="total_monto_porcentaje_a_tipopago" id="total_monto_porcentaje_a_tipopago" value="100">
            <input type="text" step="0.01" class="form-control" name="total_monto_porcentaje_tipopago" id="total_monto_porcentaje_tipopago" value="<?=number_format(100,2,".","");?>" readonly="true">
          </td>
          <td class="text-right">
            <input type="hidden" name="total_monto_bob_a_tipopago" id="total_monto_bob_a_tipopago" value="<?=$monto_total?>">
            <input type="text" class="form-control" name="total_monto_bob_tipopago" id="total_monto_bob_tipopago" value="<?=number_format($monto_total,2,".","");?>" readonly="true">
          </td>            
        </tr>                     
    </tbody>
</table>
<input type="hidden" id="total_items_tipopago" name="total_items_tipopago" value="<?=$iii?>"> -->