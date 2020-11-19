<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();
set_time_limit(0);
$fechaActual=date("Y-m-d");
$gestion=nameGestion($_POST['gestion']);
$fecha=$_POST['fecha'];
$fechaTitulo= explode("-",$fecha);

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

//CONSTANTES
//ACTIVO CORRIENTE
$datosCalculables['AC']=1000;
//PASIVO CORRIENTE
$datosCalculables['PC']=2;
//ACTIVO TOTAL
$datosCalculables['AT']=12345;
//INVENTARIO
$datosCalculables['I']=12345;
//PATRIMONIO NETO
$datosCalculables['PN']=12345;
//PASIVO TOTAL
$datosCalculables['PT']=12345;
//DISPONIBLE
$datosCalculables['DISP']=12345;
//PASIVO A LARGO PLAZO
$datosCalculables['PLP']=12345;
//ACTIVO NO CORRIENTE
$datosCalculables['ANC']=12345;
//VENTAS
$datosCalculables['V']=12345;
//VENTAS AL CRÉDITO
$datosCalculables['Vcr']=12345;
//COSTO MERCADERÍA VENDIDA
$datosCalculables['CMV']=12345;
//COMPRAS AL CRÉDITO
$datosCalculables['Ccr']=12345;
//UTILIDAD NETA
$datosCalculables['UN']=12345;
//UTILIDAD OPERATIVA
$datosCalculables['UO']=12345;
//UTILIDAD BRUTA
$datosCalculables['UB']=12345;
//CLIENTES
$datosCalculables['CL']=12345;
//PROVEEDORES
$datosCalculables['P']=12345;


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
                                         <td class="text-right"><?=$datosCalculables[$rowg['abreviatura']]?></td>
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
                      <hr>

                      <div class="col-sm-12">
                        <div class="table-responsive div-center col-sm-6">
                          <table class="table table-sm table-condensed table-bordered">
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">AC</td><td width="55%" class="font-weight-bold">ACTIVO CORRIENTE</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">PC</td><td width="55%" class="font-weight-bold">PASIVO CORRIENTE</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">AT</td><td width="55%" class="font-weight-bold">ACTIVO TOTAL</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">I</td><td width="55%" class="font-weight-bold">INVENTARIO</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">PN</td><td width="55%" class="font-weight-bold">PATRIMONIO NETO</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">PT</td><td width="55%" class="font-weight-bold">PASIVO TOTAL</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">DISP</td><td width="55%" class="font-weight-bold">DISPONIBLE</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">PLP</td><td width="55%" class="font-weight-bold">PASIVO A LARGO PLAZO</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">ANC</td><td width="55%" class="font-weight-bold">ACTIVO NO CORRIENTE</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">V</td><td width="55%" class="font-weight-bold">VENTAS</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">Vcr</td><td width="55%" class="font-weight-bold">VENTAS AL CRÉDITO</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">CMV</td><td width="55%" class="font-weight-bold">COSTO MERCADERÍA VENDIDA</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">Ccr</td><td width="55%" class="font-weight-bold">COMPRAS AL CRÉDITO</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">UN</td><td width="55%" class="font-weight-bold">UTILIDAD NETA</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">UO</td><td width="55%" class="font-weight-bold">UTILIDAD OPERATIVA</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">UB</td><td width="55%" class="font-weight-bold">UTILIDAD BRUTA</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">CL</td><td width="55%" class="font-weight-bold">CLIENTES</td></tr>
                                 <tr><td class="bg-amarillo font-weight-bold" width="45%">P</td><td width="55%" class="font-weight-bold">PROVEEDORES</td></tr>
                          </table>                          
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>



