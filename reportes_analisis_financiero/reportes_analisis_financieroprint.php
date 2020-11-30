<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';
require_once '../assets/libraries/CifrasEnLetras.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();
set_time_limit(0);
$fechaActual=date("Y-m-d");
$gestion=nameGestion($_POST['gestion']);
$fecha=$_POST['fecha'];
$fechaTitulo= explode("-",$fecha);
$fechaDesde=$fechaTitulo[0]."-01-01";
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];



$moneda=1; //$_POST["moneda"];
$unidades=$_POST['unidad'];
$entidades=$_POST['entidad'];
// $StringEntidad=implode(",", $entidad);

$stringEntidades="";
foreach ($entidades as $valor ) {    
    $stringEntidades.=nameEntidad($valor).",";
}    


$tituloOficinas="";
for ($i=0; $i < count($unidades); $i++) { 
  $tituloOficinas.=abrevUnidad_solo($unidades[$i]).",";
}
$arrayUnidades=implode(",", $unidades);
//CONSTANTES
//ACTIVO CORRIENTE
$cuentaActivoCorriente=6;
$datosCalculables['AC']=number_format(obtenerBalanceHijosCuenta($cuentaActivoCorriente,obtenerNivelCuenta($cuentaActivoCorriente),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//PASIVO CORRIENTE
$cuentaPasivoCorriente=107;
$datosCalculables['PC']=number_format(obtenerBalanceHijosCuenta($cuentaPasivoCorriente,obtenerNivelCuenta($cuentaPasivoCorriente),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//ACTIVO TOTAL
$cuentaActivoTotal=1;
$datosCalculables['AT']=number_format(obtenerBalanceHijosCuenta($cuentaActivoTotal,obtenerNivelCuenta($cuentaActivoTotal),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//INVENTARIO
$cuentaInventarios=70;
$datosCalculables['I']=number_format(obtenerBalanceHijosCuenta($cuentaInventarios,obtenerNivelCuenta($cuentaInventarios),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//PATRIMONIO NETO
$cuentaPatrimonioNeto=3;
$datosCalculables['PN']=number_format(obtenerBalanceHijosCuenta($cuentaPatrimonioNeto,obtenerNivelCuenta($cuentaPatrimonioNeto),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//PASIVO TOTAL
$cuentaPasivoTotal=2;
$datosCalculables['PT']=number_format(obtenerBalanceHijosCuenta($cuentaPasivoTotal,obtenerNivelCuenta($cuentaPasivoTotal),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//DISPONIBLE
$cuentaDisponible=7;
$datosCalculables['DISP']=number_format(obtenerBalanceHijosCuenta($cuentaDisponible,obtenerNivelCuenta($cuentaDisponible),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//PASIVO A LARGO PLAZO
$cuentaObligacionesLargo=171;
$datosCalculables['PLP']=number_format(obtenerBalanceHijosCuenta($cuentaObligacionesLargo,obtenerNivelCuenta($cuentaObligacionesLargo),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//ACTIVO NO CORRIENTE
$cuentaActivoNoCorriente=88;
$datosCalculables['ANC']=number_format(obtenerBalanceHijosCuenta($cuentaActivoNoCorriente,obtenerNivelCuenta($cuentaActivoNoCorriente),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//VENTAS
$cuentaVentas=4;
$datosCalculables['V']=number_format(obtenerBalanceHijosCuenta($cuentaVentas,obtenerNivelCuenta($cuentaVentas),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//VENTAS AL CRÉDITO
$datosCalculables['Vcr']=12345;
//COSTO MERCADERÍA VENDIDA
$datosCalculables['CMV']=12345;
//COMPRAS AL CRÉDITO
$datosCalculables['Ccr']=12345;

//datos Extra
$cuentaEgresos=5;
$montoEgresos=obtenerBalanceHijosCuenta($cuentaEgresos,obtenerNivelCuenta($cuentaEgresos),$fechaDesde,$fecha,$arrayUnidades);
$cuentaImpuesto=141;
$montoImpuesto=obtenerBalanceHijosCuenta($cuentaImpuesto,obtenerNivelCuenta($cuentaImpuesto),$fechaDesde,$fecha,$arrayUnidades);
$cuentaDepre=256;
$montoDepre=obtenerBalanceHijosCuenta($cuentaDepre,obtenerNivelCuenta($cuentaDepre),$fechaDesde,$fecha,$arrayUnidades);
//UTILIDAD NETA
$datosCalculables['UN']=number_format($datosCalculables['V']-$montoEgresos-$montoImpuesto,2,'.','');
//UTILIDAD OPERATIVA
$datosCalculables['UO']=number_format($datosCalculables['V']-$montoEgresos-$montoImpuesto-$montoDepre,2,'.','');
//UTILIDAD BRUTA
$datosCalculables['UB']=number_format($datosCalculables['V']-$montoEgresos,2,'.','');
//CLIENTES
$cuentaClientes=67;
$datosCalculables['CL']=number_format(obtenerBalanceHijosCuenta($cuentaClientes,obtenerNivelCuenta($cuentaClientes),$fechaDesde,$fecha,$arrayUnidades),2,'.','');
//PROVEEDORES
$cuentaProveedoresServicio=153;
$cuentaOtrosProveedores=154;
$cuentaHonorariosProfesionales=235;
$cuentaHonorariosDocencia=217;

$montoPS=obtenerBalanceHijosCuenta($cuentaProveedoresServicio,obtenerNivelCuenta($cuentaProveedoresServicio),$fechaDesde,$fecha,$arrayUnidades);
$montoOP=obtenerBalanceHijosCuenta($cuentaOtrosProveedores,obtenerNivelCuenta($cuentaOtrosProveedores),$fechaDesde,$fecha,$arrayUnidades);
$montoHP=obtenerBalanceHijosCuenta($cuentaHonorariosProfesionales,obtenerNivelCuenta($cuentaHonorariosProfesionales),$fechaDesde,$fecha,$arrayUnidades);
$montoHD=obtenerBalanceHijosCuenta($cuentaHonorariosDocencia,obtenerNivelCuenta($cuentaHonorariosDocencia),$fechaDesde,$fecha,$arrayUnidades);
$datosCalculables['P']=number_format($montoPS+$montoOP+$montoHP+$montoHD,2,'.','');


function evaluarDatos($monto,$rango){
  if(count($rango)==1){
   if($monto==$rango[0]){
     return "BUENA";
   }else if($monto>$rango[0]){
     return "EXCELENTE";
   }else if($monto<$rango[0]){
     return "MALA";
   }else{
     return "";
   }   
  }else{
    if($monto>=$rango[0]&&$monto<=$rango[1]){
     return "BUENA";
    }else if($monto>$rango[1]){
     return "EXCELENTE";
   }else if($monto<$rango[0]){
     return "MALA";
   }else{
     return "";
   } 
  }
}
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon">
                      <div class="float-right col-sm-2">
                      </div>
                      <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                          Analisís Financiero
                      </h4>
                      <h6 class="card-title">Practicado al: <?=$fechaFormateada?></h6>
                      <h6 class="card-title">Gestion: <?= $gestion; ?></h6>                        
                      <h6 class="card-title">Entidad: <?=$stringEntidades;?></h6>
                      <h6 class="card-title">Oficina:<?=$tituloOficinas?></h6>             
                    </div>
                    <div class="card-body">
                      <br>
                      <div class="col-sm-12">
                        <div class="table-responsive div-center col-sm-6">
                          <table class="table table-sm table-condensed table-bordered">
                               <thead>
                                <tr class="bg-dark text-white"><th width="30%">VARIABLE</th><th width="40%">DESCRIPCION</th><th width="30%">MONTO</th></tr>
                               </thead>
                               <tbody>                               
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">AC</td><td width="40%" class="font-weight-bold">ACTIVO CORRIENTE</td><td width="30%" class="text-right"><?=$datosCalculables['AC']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">PC</td><td width="40%" class="font-weight-bold">PASIVO CORRIENTE</td><td width="30%" class="text-right"><?=$datosCalculables['PC']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">AT</td><td width="40%" class="font-weight-bold">ACTIVO TOTAL</td><td width="30%" class="text-right"><?=$datosCalculables['AT']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">I</td><td width="40%" class="font-weight-bold">INVENTARIO</td><td width="30%" class="text-right"><?=$datosCalculables['I']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">PN</td><td width="40%" class="font-weight-bold">PATRIMONIO NETO</td><td width="30%" class="text-right"><?=$datosCalculables['PN']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">PT</td><td width="40%" class="font-weight-bold">PASIVO TOTAL</td><td width="30%" class="text-right"><?=$datosCalculables['PT']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">DISP</td><td width="40%" class="font-weight-bold">DISPONIBLE</td><td width="30%" class="text-right"><?=$datosCalculables['DISP']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">PLP</td><td width="40%" class="font-weight-bold">PASIVO A LARGO PLAZO</td><td width="30%" class="text-right"><?=$datosCalculables['PLP']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">ANC</td><td width="40%" class="font-weight-bold">ACTIVO NO CORRIENTE</td><td width="30%" class="text-right"><?=$datosCalculables['ANC']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">V</td><td width="40%" class="font-weight-bold">VENTAS</td><td width="30%" class="text-right"><?=$datosCalculables['V']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">Vcr</td><td width="40%" class="font-weight-bold">VENTAS AL CRÉDITO</td><td width="30%" class="text-right"><?=$datosCalculables['Vcr']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">CMV</td><td width="40%" class="font-weight-bold">COSTO MERCADERÍA VENDIDA</td><td width="30%" class="text-right"><?=$datosCalculables['CMV']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">Ccr</td><td width="40%" class="font-weight-bold">COMPRAS AL CRÉDITO</td><td width="30%" class="text-right"><?=$datosCalculables['Ccr']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">UN</td><td width="40%" class="font-weight-bold">UTILIDAD NETA</td><td width="30%" class="text-right"><?=$datosCalculables['UN']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">UO</td><td width="40%" class="font-weight-bold">UTILIDAD OPERATIVA</td><td width="30%" class="text-right"><?=$datosCalculables['UO']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">UB</td><td width="40%" class="font-weight-bold">UTILIDAD BRUTA</td><td width="30%" class="text-right"><?=$datosCalculables['UB']?> (<?=$montoEgresos?>)</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">CL</td><td width="40%" class="font-weight-bold">CLIENTES</td><td width="30%" class="text-right"><?=$datosCalculables['CL']?></td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="30%">P</td><td width="40%" class="font-weight-bold">PROVEEDORES</td><td width="30%" class="text-right"><?=$datosCalculables['P']?></td></tr>
                                </tbody> 
                          </table>                          
                        </div>
                      </div>
                      <br><br>
                      <hr>
                      <div class="col-sm-12">
                        <div class="table-responsive div-center col-sm-12">
                          <table class="table table-sm table-condensed table-bordered">
                              <thead>
                                <tr>
                                   <th colspan="6" class="bg-dark text-white" height="70px">ANALISIS FINANCIERO</th>
                                </tr>
                              </thead>                      
                              <tbody>
                                <?php
                                $sql="SELECT rd.*,r.nombre as razon FROM razones_financierasdetalle rd join razones_financieras r on r.codigo=rd.cod_razonesfinancieras where r.cod_estadoreferencial=1 and rd.cod_estadoreferencial=1 order by rd.cod_razonesfinancieras,rd.orden";
                                $stmtg = $dbh->prepare($sql);
                                $stmtg->execute();
                                $codigo_razon=0;$nombreRazon="";
                                while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                                  $cod_razon=$rowg['cod_razonesfinancieras'];
                                  $nombreRazon=$rowg['razon'];
                                  $nombreRazonDetalle=$rowg['nombre'];
                                  $descripcionFormula=$rowg['abreviatura']."=".str_replace(["#_","_#"],"",$rowg['formula']);
                                  $descripcionPromedio="".str_replace("#"," - ",$rowg['promedio'])."";
                                  $rangoMonto=explode("#",$rowg['promedio']);                                  
                                  $datosCalculables[$rowg['abreviatura']]=0;
                                  
                                  //para reemplazar las variables #_variable_# al string de formula                                  
                                  $descripcionCalculo=str_replace("#_","\$datosCalculables['",$rowg['formula']);
                                  $descripcionCalculo=str_replace("_#","']",$descripcionCalculo).";"; 
                                  //ejecutamos el string para obtener el resultado
                                  eval("\$datosCalculables[\$rowg['abreviatura']]=".$descripcionCalculo);
                                  
                                  //para reemplazar las variables, pero que se mantenga como string
                                  $descripcionCalculo2=str_replace("#_","'.\$datosCalculables['",$rowg['formula']);
                                  $descripcionCalculo2=str_replace("_#","'].'",$descripcionCalculo2); 
                                  //ejecutamos el string para obtener el resultado                                  
                                  eval("\$descripcionCalculoTitle='".$descripcionCalculo2."';");
                                   
                                  $evaluacion_vertical=evaluarDatos($datosCalculables[$rowg['abreviatura']],$rangoMonto);
                                  $evaluacion_vertical="-";
                                  $recomendacion="NINGUNA"; 
                                  if($evaluacion_vertical=="MALA"){
                                    $recomendacion="AUMENTAR LA ".strtoupper($nombreRazon);
                                  }
                                  if($codigo_razon!=$cod_razon){                                    
                                    if($codigo_razon==0){
                                     ?>
                                     <tr class="bg-celeste text-white">                                        
                                         <td class="font-weight-bold">ANALISIS</td>
                                         <td class="font-weight-bold">FORMULAS</td>
                                         <td class="font-weight-bold">CÁLCULO</td>
                                         <td class="font-weight-bold"><?=$gestion?></td>
                                         <td class="font-weight-bold">PROMEDIO</td>
                                         <td class="font-weight-bold">EVALUACIÓN VERTICAL</td>
                                         <!--<td class="font-weight-bold">RECOMENDACIÓN</td>-->
                                  </tr>
                                     <?php 
                                    }else{
                                      ?>
                                      <tr class="bg-celeste text-white">                                        
                                         <td class="font-weight-bold">&nbsp;</td>
                                         <td class="font-weight-bold"></td>
                                         <td class="font-weight-bold"></td>
                                         <td class="font-weight-bold"></td>
                                         <td class="font-weight-bold"></td>
                                         <td class="font-weight-bold"></td>
                                         <!--<td class="font-weight-bold"></td>-->
                                    </tr>
                                      <?php
                                    }
                                    ?>
                                     
                                  <tr class="">                                        
                                         <td class="bg-amarillo font-weight-bold text-left"><?=strtoupper($nombreRazon)?></td>
                                         <td class="font-weight-bold text-left"></td>
                                         <td class="font-weight-bold text-center"></td>
                                         <td class="font-weight-bold text-right"></td>
                                         <td class="font-weight-bold text-center"></td>
                                         <td class="font-weight-bold text-center"></td>
                                         <!--<td class="font-weight-bold text-left"></td>-->
                                  </tr>
                                    <?php
                                  }
                                  ?>
                                   <tr class="">                                        
                                         <td class="text-left"><?=$nombreRazonDetalle?></td>
                                         <td class="text-left"><?=$descripcionFormula?></td>
                                         <td class="text-center"><?=$descripcionCalculoTitle?></td>
                                         <td class="text-right"><?=number_format($datosCalculables[$rowg['abreviatura']],2,'.','')?></td>
                                         <td class="text-center"><?=$descripcionPromedio?></td>
                                         <td class="text-center"><?=$evaluacion_vertical?></td>
                                         <!--<td class="text-left"><?=$recomendacion?></td>-->
                                  </tr>
                                  <?php    
                                  $codigo_razon=$cod_razon;
                                }
                                ?>
                              </tbody>
                          </table>                          
                        </div>
                      </div>
                      <br><br><br>
                      
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>



