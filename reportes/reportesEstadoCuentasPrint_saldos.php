<?php //ESTADO FINALIZADO
$proveedoresString=implode(",", $proveedores);
$proveedoresStringAux="and e.cod_cuentaaux in ($proveedoresString)";
if(count($proveedores)==(int)$_POST["numero_proveedores"]){
  $proveedoresStringAux="";
}
$StringCuenta=implode(",", $cuenta);
$StringUnidades=implode(",", $unidad);
$stringGeneraCuentas="";

foreach ($cuenta as $cuentai ) {    
    $stringGeneraCuentas.=nameCuenta($cuentai).",";
}
$stringGeneraUnidades="";
foreach ($unidad as $unidadi ) {    
    $stringGeneraUnidades.=" ".abrevUnidad($unidadi)." ";
}
$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];

$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;

$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];

$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);
$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

$string_periodo="30,60,90";
$array_periodo=explode(",", $string_periodo);

require_once 'reportesEstadoCuentasPrint_saldos_detalle.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon">
                      <input type="hidden" name="unidades_x" id="unidades_x" value="<?=$StringUnidades?>">
                      <input type="hidden" name="gestion_x" id="gestion_x" value="<?=$gestion?>">
                      <input type="hidden" name="desde_x" id="desde_x" value="<?=$desde?>">
                      <input type="hidden" name="hasta_x" id="hasta_x" value="<?=$hasta?>">
                      <input type="hidden" name="cuentai_x" id="cuentai_x" value="<?=$StringCuenta?>">
                        <!--div class="float-right col-sm-2">
                            <h6 class="card-title">Exportar como:</h6>
                        </div-->
                        <h4 class="card-title"> 
                            <img  class="card-img-top" src="../marca.png" style="width:100%; max-width:50px;">
                            Reporte de Cuentas Por Cobrar Por Periodos
                        </h4>
                      <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                      <!--h6 class="card-title">Gestion: <?= $NombreGestion; ?></h6>
                      <h6 class="card-title">Cuenta: <?=$stringGeneraCuentas;?></h6>
                      <h6 class="card-title">Unidad:<?=$stringGeneraUnidades?></h6-->             
                      <div class="row">
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Oficina: </b> <small><?=$unidadAbrev?></small></h6></div>
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Area: </b> <small><?=$areaAbrev?></small></h6></div>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                          <label class="col-sm-1 col-form-label">Clientes</label>
                          <div class="col-sm-2">
                            <div class="form-group">
                              <select class="selectpicker form-control form-control-sm"  name="cuentas_auxiliares[]" id="cuentas_auxiliares" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-live-search="true">
                                <?php                   
                                  $sql="SELECT distinct(ca.codigo)as codigo, ca.nombre from estados_cuenta ec, cuentas_auxiliares ca  where ca.codigo=ec.cod_cuentaaux and ca.cod_cuenta in ($StringCuenta) order by ca.nombre";                                  
                                   $stmt3 = $dbh->prepare($sql);
                                   $stmt3->execute();
                                 while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoSel=$rowSel['codigo'];
                                  $nombreSelX=$rowSel['nombre'];
                                  ?><option value="<?=$codigoSel;?>"> <?=$nombreSelX?></option><?php 
                                 }
                                ?>
                              </select>
                            </div>
                          </div>
                   
                    <div class="col-sm-4">
                        <div class="row">
                        <label class="col-sm-4 col-form-label">Centro de Costos - Oficina</label>
                        <div class="col-sm-8">
                        <div class="form-group">
                              <?php
                          $sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo order by 2";
                          $stmt = $dbh->prepare($sqlUO);
                          $stmt->execute();
                          ?>
                            <select class="selectpicker form-control form-control-sm" name="unidad_costo[]" id="unidad_costo" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true"><?php 
                                while ($row = $stmt->fetch()){  ?>
                                <option value="<?=$row["codigo"];?>"  data-subtext="<?=$row["nombre"];?>" ><?=$row["abreviatura"];?></option><?php 
                                }  ?>
                            </select>
                        </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="row">
                       <label class="col-sm-4 col-form-label">Centro de Costos - Area</label>
                       <div class="col-sm-8">
                        <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="area_costo[]" id="area_costo" data-style="select-with-transition" multiple data-actions-box="true" required>
                               <?php
                               $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                             $stmt->execute();
                             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              $codigoX=$row['codigo'];
                              $nombreX=$row['nombre'];
                              $abrevX=$row['abreviatura'];
                             ?>
                             <option value="<?=$codigoX;?>" selected><?=$abrevX;?></option> 
                               <?php
                                 }
                                 ?>
                             </select>
                            </div>
                        </div>
                    </div>
              </div>
              <div class="col-sm-1">
                            <a href="#" onclick="cargarCuentasxCobrarPeriodo()" class="btn btn-white btn-sm" style="background:#F7FF5A; color:#07B46D;"><i class="material-icons">search</i> Buscar</a>
                          </div>  
                  </div><!--div row-->
                       
                        <br>
                        <div class="table-responsive" id="data_cuentasxcobrar">
                            <?php 
                            echo '<table class="table table-bordered table-condensed" id="tablePaginatorFixedEstadoCuentas">'.
                                '<thead>'.
                                    '<tr class="">'.
                                        '<th class="text-left">-</th>'.
                                        '<th class="text-left">Cliente</th>';
                                        $periodo=0;
                                        $x=0;

                                        foreach ($array_periodo as $periodo) {
                                            echo '<th class="text-right">'.$periodo.' Días</th>';
                                            $monto_periodo[$x]=0;
                                            $totales_array[$x]=0;
                                            $x++;
                                        }
                                        $monto_periodo[$x]=0;
                                        $totales_array[$x]=0;
                                        $totales_array[$x+1]=0;//para el total
                                        echo '<th class="text-right"> > '.$periodo.' Días</th>';
                                    echo '<th class="text-right">Total</th></tr>'.
                                '</thead>'.
                                '<tbody>';
                                foreach ($cuenta as $cuentai) {
                                    $nombreCuenta=nameCuenta($cuentai);//nombre de cuenta
                                    echo '<tr style="background-color:#9F81F7;">
                                        <td style="display: none;"></td>
                                        <td class="text-left small" colspan="2">CUENTA</td>
                                        <td class="text-left small" colspan="5">'.$nombreCuenta.'</td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>                                        
                                        <td style="display: none;"></td>
                                    </tr>';

                                    $sqlFechaEstadoCuenta="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'";
                                    if(isset($_POST['cierre_anterior'])){
                                      $sqlFechaEstadoCuenta="and e.fecha<='$hasta 23:59:59'";  
                                    }
                                    $sql="SELECT e.fecha,e.cod_cuentaaux,ca.nombre,(SELECT c.tipo from configuracion_estadocuentas c where c.cod_plancuenta=d.cod_cuenta)as tipoDebeHaber
                                        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY e.cod_cuentaaux  order by ca.nombre"; //ca.nombre, 
                                    // echo $sql;
                                    $stmtUO = $dbh->prepare($sql);
                                    $stmtUO->execute();
                                    $index=1;
                                    
                                    while ($row = $stmtUO->fetch()) {
                                        // $fechaX=$row['fecha'];
                                        // $monto_ecX=$row['monto_ec'];
                                        $cod_cuentaauxX=$row['cod_cuentaaux'];
                                        $nombreX=$row['nombre'];
                                        $tipoDebeHaberX=$row['tipoDebeHaber'];   
                                        // $periodo=0;
                                        if($tipoDebeHaberX==2){//proveedor

                                        }else{//cliente
                                            echo '<tr class="bg-white" >
                                                <td class="text-center small">'.$index.'</td>
                                                <td class="text-left small">'.$nombreX.'</td>';
                                                // include "reportesEstadoCuentasPrint_saldos_detalle.php";
                                                $array_totales=generarHTMLFacCliente($cuentai,$NombreGestion,$sqlFechaEstadoCuenta,$StringUnidades,$cod_cuentaauxX,$unidadCostoArray,$areaCostoArray,$desde,$hasta,$monto_periodo,$array_periodo);
                                                echo '</tr>';
                                                //para los totales **                                                
		                                        $x_total=0;
		                                        foreach ($array_totales as $monto_x) {          
		                                            $totales_array[$x_total]+=$monto_x;
		                                            $x_total++;
		                                        }
		                                        //totales fin
                                            }
                                        $index++;
                                    }    
                                }                                

                                echo '<tr>
                                    <td style="display: none;"></td>
                                    <td class="text-right small" colspan="2">Total:</td>';
	                                foreach ($totales_array as $monto_total) {
	                                    echo '<th class="text-right">'.formatNumberDec($monto_total).'</th>';	
	                                }    
                                echo '</tr>';  

                                

                                // echo '<tr>
                                //     <td style="display: none;"></td>
                                //     <td class="text-right small" colspan="2">Total:</td>
                                //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalDebito).'</td>
                                //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalCredito).'</td>
                                //     <td class="text-right small font-weight-bold">'.formatNumberDec($totalSaldo).'</td>
                                // </tr>';  
                                echo '</tbody>
                            </table>';
                            // echo $html;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>