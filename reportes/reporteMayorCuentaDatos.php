<h4 class="card-title text-center">Reporte Libro Mayor</h4>
<?php
    $codcuenta=array_unique($codcuenta);
    $cuentasTotal=[];
     
    for ($xx=0; $xx < cantidadF($codcuenta); $xx++) { 
      $porciones = explode("@", $codcuenta[$xx]);
      $cuentasTotal[$xx]=$porciones[0];
    } 

    $stringSoloCuentas=implode(",", $cuentasTotal);  
   $solaConsulta="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,d.glosa,d.debe,d.haber,
          p.codigo,p.numero,p.nombre,d.cod_cuentaauxiliar,
          u.abreviatura,a.abreviatura as areaAbrev,
          c.cod_unidadorganizacional as unidad,c.fecha, (c.codigo) as codigo_comprobante
          FROM comprobantes_detalle d 
          join plan_cuentas p on p.codigo=d.cod_cuenta 
          join areas a on d.cod_area=a.codigo 
          join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
          join comprobantes c on d.cod_comprobante=c.codigo
          where c.cod_gestion=$NombreGestion 
           and d.cod_cuenta in (".$stringSoloCuentas.") and c.cod_estadocomprobante<>2 
          and d.cod_unidadorganizacional in (".$unidadCostoArray.") 
          and d.cod_area in (".$areaCostoArray.") 
          and c.cod_unidadorganizacional in (".$unidadArray.") and (c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59')order by c.fecha;";

   //echo $solaConsulta;
   $query1="CALL listarMayorCuentasSoloAux(\"".$solaConsulta."\");";
   echo $query1;
   $stmt = $dbh->prepare($query1);
   $stmt->execute();   
   ?>
   <div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Cuenta: <?=$nombreCuentaTitle;?></h6>
  <h6 class="card-title">Oficina Origen Comprobante:<?=$unidadGeneral?></h6>
  <div class="row">
    <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Oficina: </b> <small><?=$unidadAbrev?></small></h6></div>
    <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Area: </b> <small><?=$areaAbrev?></small></h6></div>
  </div>
<table class="table table-sm text-dark" id="libro_mayor_rep">
  <thead >
            <tr class="text-center">
              <th>Oficina Origen</th>
              <th width="5%">Cbte</th>
              <th width="5%">CompDetalle</th>
              <th width="5%">Auxiliar</th>
              <th width="5%">Proveedor</th>
              <th width="7%">Fecha</th>
              <th width="5%">Centro de Costos</th>
              <th width="60%">Concepto</th>
              <th width="3%">t/c</th>
              <th width="5%">Debe</th>
              <th width="5%">Haber</th>
              <!--<th width="5%">Saldos</th>-->
            </tr>
           </thead>
  <tbody>
   <?php
      while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $fechaX=$rowComp['fecha'];
        $codigoX=$rowComp['cod_det'];
        $glosaX=$rowComp['glosa'];
        $unidadX=$rowComp['abreviatura'];
        $areaX=$rowComp['areaAbrev'];
        $debeX=$rowComp['debe'];
        $haberX=$rowComp['haber'];
        $codCuentaAuxiliar=$rowComp['cod_cuentaauxiliar'];
        $cuenta_auxiliarX=nameCuentaAuxiliar($codCuentaAuxiliar);
        $nombreUnidad=abrevUnidad_solo($rowComp['unidad']);
        $codComprobanteX=$rowComp['codigo_comprobante'];
        $nombreComprobanteX=nombreComprobante($codComprobanteX);
        //INICIAR valores de las sumas
        if($glosaLen==0){      
          if(strlen($glosaX)>obtenerValorConfiguracion(72)){
            $glosaX=substr($glosaX,0,obtenerValorConfiguracion(72))."...";
          }
        }
        $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaX)));
        if($tc==0){$tc=1;}
        
        $saldoX=0;
        if($saldoX<0){
          $saldoXFormato="(".formatNumberDec(abs($saldoX/$tc)).")";
        }else{
          $saldoXFormato=formatNumberDec($saldoX/$tc);
        }
       ?>
    <tr class="">
       <td class="font-weight-bold small"><?=$nombreUnidad?></td>
       <td class="font-weight-bold small"><?=$nombreComprobanteX?></td>
       <td class="font-weight-bold small"><?=$codigoX?></td>
       <td class="font-weight-bold small"><?=$codCuentaAuxiliar?></td>
       <td class="font-weight-bold small"><?=obtenerCodigoProveedorCuentaAux($codCuentaAuxiliar)?></td>
       <td class="font-weight-bold small"><?=strftime('%d/%m/%Y',strtotime($fechaX))?></td>
       <td class="font-weight-bold small"><?=$unidadX?>-<?=$areaX?></td>
       <td class="text-left small">[<?=$cuenta_auxiliarX?>] - <?=$glosaX?></td>
       <td class="font-weight-bold small"><?=$tc?></td>                
       <td class="text-right font-weight-bold small"><?=formatNumberDec($debeX/$tc)?></td>
       <td class="text-right font-weight-bold small"><?=formatNumberDec($haberX/$tc)?></td>
       <!--<td class="text-right font-weight-bold small"><?=$saldoXFormato?></td>-->
    </tr>             
<?php
      }/* Fin del primer while*/

?>
  </tbody></table>

  </div>
</div>

<script>
$(document).ready(function() {
  //exportTableToExcel('libro_mayor_rep');
});
</script>

     