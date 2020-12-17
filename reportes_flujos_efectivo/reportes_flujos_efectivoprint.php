<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
set_time_limit(0);
$fechaActual=date("Y-m-d");
$gestion=nameGestion($_POST['gestion']);

$fechaDesde=$_POST['fecha_desde'];
$fechaDesdeTitulo= explode("-",$fechaDesde);
$fechaFormateadaDesde=$fechaDesdeTitulo[2].'/'.$fechaDesdeTitulo[1].'/'.$fechaDesdeTitulo[0];

$fechaDesdeMenos=date("Y-m-d",strtotime($fechaDesde."- 1 days")); 
$fechaDesdeTituloMenos= explode("-",$fechaDesdeMenos);
$fechaFormateadaDesdeMenos=$fechaDesdeTituloMenos[2].'/'.$fechaDesdeTituloMenos[1].'/'.$fechaDesdeTituloMenos[0];
$gestionMenos=$fechaDesdeTituloMenos[0];
$fecha=$_POST['fecha_hasta'];
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

$areas=array("prueba","prueba");//$_POST['area_costo'];
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDFBalance.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>';
$html.=  '<header class="header">'.            
            '<img class="imagen-logo-izq" src="../assets/img/ibnorca2.jpg">'.
            '<div id="header_titulo_texto">Flujo de Efectivo</div>'.
         '<div id="header_titulo_texto_inf_pegado">Del '.$fechaFormateadaDesde.' al '.$fechaFormateada.'</div>'.
         '<div id="header_titulo_texto_inf_pegado_Max">Expresado en Bolivianos</div>'.
         '<table class="table pt-2">'.
            '<tr class="bold table-title">'.
              '<td class="td-border-none" width="22%">Entidad: '.$stringEntidades.'</td>'.
              '<td class="td-border-none" width="33%"></td>'.            
            '</tr>'.
            '<tr>'.
            '<td class="td-border-none" colspan="2">Oficinas: '.$tituloOficinas.'</td>'.
            '</tr>'.
         '</table>'.
         '</header>';

$cuentasNivel3=obtenerCuentasNivel3FlujoEfectivo(1);
$stringCuentasNivel3="";
if(count($cuentasNivel3)>0){
  $cadenaCuentasNivel3=implode(",",$cuentasNivel3);
  $stringCuentasNivel3="and p.codigo in ($cadenaCuentasNivel3)";
}
$html.='<br><table class="table">
            <thead>
               <tr class="bold table-title">'.
                '<td class="td-border-none text-left" colspan="2"></td>'.
                '<td class="td-border-none text-right">Bolivianos</td>
            </tr>
               <tr class="bold table-title">'.
                '<td class="td-border-bottom text-left" colspan="3">Saldo Inicial</td>
            </tr></thead>'.
           '<tbody>'; 
           $index=1;
           $tBolActivo=0;$tBolPasivo=0;
           $tBolDebe=0;$tBolHaber=0;
           $tBolDebeSaldo=0;$tBolHaberSaldo=0;
           $montoResultado=0;
// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=1 order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
     $sumaNivel1=0;$html1="";$sumaDebeNivel1=0;$sumaHaberNivel1=0;
     $stmt2 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                            (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=2 and p.cod_padre='$codigo' order by p.numero");
      $stmt2->execute();                      
      $stmt2->bindColumn('codigo', $codigo_2);
      $stmt2->bindColumn('numero', $numero_2);
      $stmt2->bindColumn('nombre', $nombre_2);
      $stmt2->bindColumn('cod_padre', $codPadre_2);
      $stmt2->bindColumn('nivel', $nivel_2);
      $stmt2->bindColumn('cod_tipocuenta', $codTipoCuenta_2);
      $stmt2->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_2);
      $index_2=1;
      while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
        
         $sumaNivel2=0;$html2="";$sumaDebeNivel2=0;$sumaHaberNivel2=0;
         $stmt3 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                              (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=3 and p.cod_padre='$codigo_2' $stringCuentasNivel3 order by p.numero");
         $stmt3->execute();                      
         $stmt3->bindColumn('codigo', $codigo_3);
         $stmt3->bindColumn('numero', $numero_3);
         $stmt3->bindColumn('nombre', $nombre_3);
         $stmt3->bindColumn('cod_padre', $codPadre_3);
         $stmt3->bindColumn('nivel', $nivel_3);
         $stmt3->bindColumn('cod_tipocuenta', $codTipoCuenta_3);
         $stmt3->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_3);
         $index_3=1;
         while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) { 
             $cuentasNivel4=obtenerCuentasNivel4FlujoEfectivo(1,$codigo_3);
             $stringCuentasNivel4="";
             if(count($cuentasNivel4)>0){
               $cadenaCuentasNivel4=implode(",",$cuentasNivel4);
               $stringCuentasNivel4="and p.codigo in ($cadenaCuentasNivel4)";
             }    

            $sumaNivel3=0;$html3="";$sumaDebeNivel3=0;$sumaHaberNivel3=0;
            $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='$codigo_3' $stringCuentasNivel4 order by p.numero");
            $stmt4->execute();                      
            $stmt4->bindColumn('codigo', $codigo_4);
            $stmt4->bindColumn('numero', $numero_4);
            $stmt4->bindColumn('nombre', $nombre_4);
            $stmt4->bindColumn('cod_padre', $codPadre_4);
            $stmt4->bindColumn('nivel', $nivel_4);
            $stmt4->bindColumn('cod_tipocuenta', $codTipoCuenta_4);
            $stmt4->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_4);
            $index_4=1;

            while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
               $cuentasNivel5=obtenerCuentasNivel5FlujoEfectivo(1,$codigo_4);
               $stringCuentasNivel5="";
              if(count($cuentasNivel5)>0){
                $cadenaCuentasNivel5=implode(",",$cuentasNivel5);
                $stringCuentasNivel5="and p.codigo in ($cadenaCuentasNivel5)";
               }
              
               $sumaNivel4=0;$html4="";$sumaDebeNivel4=0;$sumaHaberNivel4=0;           
              //listar los montos
              $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalleCuentasString($stringCuentasNivel5,$fechaFormateadaDesdeMenos,1,$unidades,$areas,$codigo_4,$gestion,"none");
               $vacio=0;
               while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
                   $numeroX=$rowComp['numero'];
                   $nombreX=trim($rowComp['nombre']);
                   $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                   if($codigo==1){
                    $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                    $tBolActivo+=$montoX;
                  }else{
                    $tBolPasivo+=$montoX;
                    if($codigo==3){
                      $vacio++;
                    }
                  }
                    $sumaDebeNivel4+=$rowComp['total_debe'];$sumaHaberNivel4+=$rowComp['total_haber'];
                    $sumaNivel4+=$montoX;  
                    if($montoX>0){
                      $tBolDebeSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%">'.number_format($montoX, 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX<0){
                      $tBolHaberSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.                           
                           '<td class="td-border-none text-right" width="20%">'.number_format(abs($montoX), 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX==0){
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%"></td>';   
                     $html4.='</tr>';      
                    }
                            
               $index++;         
               }

              if($sumaNivel4>0){
                $sumaNivel3+=$sumaNivel4; 
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4<0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4==0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              } 
            }
            if($sumaNivel3>0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format($sumaNivel3, 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }elseif($sumaNivel3<0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format(abs($sumaNivel3), 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }
            elseif($sumaNivel3==0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class="table-title td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class="table-title td-border-derecha text-right"></td>';   
              $html2.='</tr>';
            }
          }
          if($sumaNivel2>0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
          elseif($sumaNivel2<0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
           elseif($sumaNivel2==0){
             $sumaNivel1+=$sumaNivel2;
             $sumaDebeNivel1+=$sumaDebeNivel2; 
             $sumaHaberNivel1+=$sumaHaberNivel2;
             $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
             $monto_2=0;
             $html1.='';
              $html1.=$html2; 
          }
      }

    $nombre=formateaPlanCuenta($nombre, $nivel);
    $monto=0;
    if($sumaNivel1>0){
      $html.='';
     $html.=$html1;
    }elseif($sumaNivel1<0){
      $html.='';
     $html.=$html1;
    }
    elseif ($sumaNivel1==0) {
      $html.='';
     $html.=$html1;
    }
    
    $montoResultado+=$sumaNivel1;    
}


 $html.=    '</tbody></table>';
      $html.='<br><table class="table">'.
           '<tbody>';

     $html.='<tr class="">'.
                  '<td class="bold table-title text-left td-border-izquierda" width="80%" colspan="2">Total</td>'.
                  '<td class="text-right td-border-derecha">'.number_format(abs($montoResultado), 2, '.', ',').'</td>'.     
              '</tr>';
  $html.=    '</tbody></table>';

//FIN DEL FLUJO 1


$cuentasNivel3=obtenerCuentasNivel3FlujoEfectivo(2);
$stringCuentasNivel3="";
if(count($cuentasNivel3)>0){
  $cadenaCuentasNivel3=implode(",",$cuentasNivel3);
  $stringCuentasNivel3="and p.codigo in ($cadenaCuentasNivel3)";
}
$html.='<br><table class="table">
            <thead>
               <tr class="bold table-title">'.
                '<td class="td-border-none text-left" colspan="2"></td>'.
                '<td class="td-border-none text-right">Bolivianos</td>
            </tr>
               <tr class="bold table-title">'.
                '<td class="td-border-bottom text-left" colspan="3">Incremento de efectivo por:</td>
            </tr></thead>'.
           '<tbody>'; 
           $index=1;
           $tBolActivo=0;$tBolPasivo=0;
           $tBolDebe=0;$tBolHaber=0;
           $tBolDebeSaldo=0;$tBolHaberSaldo=0;
           $montoResultado=0;
// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=1 order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
     $sumaNivel1=0;$html1="";$sumaDebeNivel1=0;$sumaHaberNivel1=0;
     $stmt2 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                            (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=2 and p.cod_padre='$codigo' order by p.numero");
      $stmt2->execute();                      
      $stmt2->bindColumn('codigo', $codigo_2);
      $stmt2->bindColumn('numero', $numero_2);
      $stmt2->bindColumn('nombre', $nombre_2);
      $stmt2->bindColumn('cod_padre', $codPadre_2);
      $stmt2->bindColumn('nivel', $nivel_2);
      $stmt2->bindColumn('cod_tipocuenta', $codTipoCuenta_2);
      $stmt2->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_2);
      $index_2=1;
      while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
        
         $sumaNivel2=0;$html2="";$sumaDebeNivel2=0;$sumaHaberNivel2=0;
         $stmt3 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                              (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=3 and p.cod_padre='$codigo_2' $stringCuentasNivel3 order by p.numero");
         $stmt3->execute();                      
         $stmt3->bindColumn('codigo', $codigo_3);
         $stmt3->bindColumn('numero', $numero_3);
         $stmt3->bindColumn('nombre', $nombre_3);
         $stmt3->bindColumn('cod_padre', $codPadre_3);
         $stmt3->bindColumn('nivel', $nivel_3);
         $stmt3->bindColumn('cod_tipocuenta', $codTipoCuenta_3);
         $stmt3->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_3);
         $index_3=1;
         while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) { 
             $cuentasNivel4=obtenerCuentasNivel4FlujoEfectivo(2,$codigo_3);
             $stringCuentasNivel4="";
             if(count($cuentasNivel4)>0){
               $cadenaCuentasNivel4=implode(",",$cuentasNivel4);
               $stringCuentasNivel4="and p.codigo in ($cadenaCuentasNivel4)";
             }    

            $sumaNivel3=0;$html3="";$sumaDebeNivel3=0;$sumaHaberNivel3=0;
            $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='$codigo_3' $stringCuentasNivel4 order by p.numero");
            $stmt4->execute();                      
            $stmt4->bindColumn('codigo', $codigo_4);
            $stmt4->bindColumn('numero', $numero_4);
            $stmt4->bindColumn('nombre', $nombre_4);
            $stmt4->bindColumn('cod_padre', $codPadre_4);
            $stmt4->bindColumn('nivel', $nivel_4);
            $stmt4->bindColumn('cod_tipocuenta', $codTipoCuenta_4);
            $stmt4->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_4);
            $index_4=1;

            while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
              $cuentasNivel5=obtenerCuentasNivel5FlujoEfectivo(2,$codigo_4);
               $stringCuentasNivel5="";
              if(count($cuentasNivel5)>0){
                $cadenaCuentasNivel5=implode(",",$cuentasNivel5);
                $stringCuentasNivel5="and p.codigo in ($cadenaCuentasNivel5)";
               }
               $sumaNivel4=0;$html4="";$sumaDebeNivel4=0;$sumaHaberNivel4=0;           
              //listar los montos

              $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalleCuentasString($stringCuentasNivel5,$fechaFormateada,1,$unidades,$areas,$codigo_4,$gestion,"none");
               $vacio=0;
               while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
                   $detallesReporteMenos=listaSumaMontosDebeHaberComprobantesDetalleCuenta($rowComp['numero'],$fechaFormateadaDesdeMenos,1,$unidades,$areas,$codigo_4,$gestion,"none");
                   $resultAnterior = $detallesReporteMenos->fetch();    
                   $numeroXAnterior = $resultAnterior['numero'];
                   $nombreXAnterior = $resultAnterior['nombre'];
                   $debeXAnterior = $resultAnterior['total_debe'];
                   $haberXAnterior = $resultAnterior['total_haber'];

                   $numeroX=$rowComp['numero'];
                   $nombreX=trim($rowComp['nombre']);
                   $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                   $montoXAnterior=(float)($resultAnterior['total_debe']-$resultAnterior['total_haber']);
                   if($codigo==1){
                    $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                    $tBolActivo+=$montoX;
                  }else{
                    $tBolPasivo+=$montoX;
                    if($codigo==3){
                      $vacio++;
                    }
                  }
                  $montoX=$montoXAnterior-$montoX;

                    $sumaDebeNivel4+=$rowComp['total_debe'];$sumaHaberNivel4+=$rowComp['total_haber'];
                    $sumaNivel4+=$montoX;  
                    if($montoX>0){
                      $tBolDebeSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%">'.number_format($montoX, 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX<0){
                      $tBolHaberSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.                           
                           '<td class="td-border-none text-right" width="20%">'.number_format(abs($montoX), 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX==0){
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%"></td>';   
                     $html4.='</tr>';      
                    }
                            
               $index++;         
               }

              if($sumaNivel4>0){
                $sumaNivel3+=$sumaNivel4; 
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4<0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4==0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              } 
            }
            if($sumaNivel3>0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format($sumaNivel3, 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }elseif($sumaNivel3<0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format(abs($sumaNivel3), 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }
            elseif($sumaNivel3==0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class="table-title td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class="table-title td-border-derecha text-right"></td>';   
              $html2.='</tr>';
            }
          }
          if($sumaNivel2>0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
          elseif($sumaNivel2<0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
           elseif($sumaNivel2==0){
             $sumaNivel1+=$sumaNivel2;
             $sumaDebeNivel1+=$sumaDebeNivel2; 
             $sumaHaberNivel1+=$sumaHaberNivel2;
             $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
             $monto_2=0;
             $html1.='';
              $html1.=$html2; 
          }
      }

    $nombre=formateaPlanCuenta($nombre, $nivel);
    $monto=0;
    if($sumaNivel1>0){
      $html.='';
     $html.=$html1;
    }elseif($sumaNivel1<0){
      $html.='';
     $html.=$html1;
    }
    elseif ($sumaNivel1==0) {
      $html.='';
     $html.=$html1;
    }
    
    $montoResultado+=$sumaNivel1;    
}


 $html.=    '</tbody></table>';
      $html.='<br><table class="table">'.
           '<tbody>';

     $html.='<tr class="">'.
                  '<td class="bold table-title text-left td-border-izquierda" width="80%" colspan="2">Total</td>'.
                  '<td class="text-right td-border-derecha">'.number_format(abs($montoResultado), 2, '.', ',').'</td>'.     
              '</tr>';
  $html.=    '</tbody></table>';

//FIN DEL FLUJO 2


  $cuentasNivel3=obtenerCuentasNivel3FlujoEfectivo(3);
$stringCuentasNivel3="";
if(count($cuentasNivel3)>0){
  $cadenaCuentasNivel3=implode(",",$cuentasNivel3);
  $stringCuentasNivel3="and p.codigo in ($cadenaCuentasNivel3)";
}
$html.='<br><table class="table">
            <thead>
               <tr class="bold table-title">'.
                '<td class="td-border-none text-left" colspan="2"></td>'.
                '<td class="td-border-none text-right">Bolivianos</td>
            </tr>
               <tr class="bold table-title">'.
                '<td class="td-border-bottom text-left" colspan="3">Disminución de efectivo por:</td>
            </tr></thead>'.
           '<tbody>'; 
           $index=1;
           $tBolActivo=0;$tBolPasivo=0;
           $tBolDebe=0;$tBolHaber=0;
           $tBolDebeSaldo=0;$tBolHaberSaldo=0;
           $montoResultado=0;
// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=1 order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
     $sumaNivel1=0;$html1="";$sumaDebeNivel1=0;$sumaHaberNivel1=0;
     $stmt2 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                            (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=2 and p.cod_padre='$codigo' order by p.numero");
      $stmt2->execute();                      
      $stmt2->bindColumn('codigo', $codigo_2);
      $stmt2->bindColumn('numero', $numero_2);
      $stmt2->bindColumn('nombre', $nombre_2);
      $stmt2->bindColumn('cod_padre', $codPadre_2);
      $stmt2->bindColumn('nivel', $nivel_2);
      $stmt2->bindColumn('cod_tipocuenta', $codTipoCuenta_2);
      $stmt2->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_2);
      $index_2=1;
      while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
        
         $sumaNivel2=0;$html2="";$sumaDebeNivel2=0;$sumaHaberNivel2=0;
         $stmt3 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                              (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=3 and p.cod_padre='$codigo_2' $stringCuentasNivel3 order by p.numero");
         $stmt3->execute();                      
         $stmt3->bindColumn('codigo', $codigo_3);
         $stmt3->bindColumn('numero', $numero_3);
         $stmt3->bindColumn('nombre', $nombre_3);
         $stmt3->bindColumn('cod_padre', $codPadre_3);
         $stmt3->bindColumn('nivel', $nivel_3);
         $stmt3->bindColumn('cod_tipocuenta', $codTipoCuenta_3);
         $stmt3->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_3);
         $index_3=1;
         while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) { 
             $cuentasNivel4=obtenerCuentasNivel4FlujoEfectivo(3,$codigo_3);
             $stringCuentasNivel4="";
             if(count($cuentasNivel4)>0){
               $cadenaCuentasNivel4=implode(",",$cuentasNivel4);
               $stringCuentasNivel4="and p.codigo in ($cadenaCuentasNivel4)";
             }    

            $sumaNivel3=0;$html3="";$sumaDebeNivel3=0;$sumaHaberNivel3=0;
            $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='$codigo_3' $stringCuentasNivel4 order by p.numero");
            $stmt4->execute();                      
            $stmt4->bindColumn('codigo', $codigo_4);
            $stmt4->bindColumn('numero', $numero_4);
            $stmt4->bindColumn('nombre', $nombre_4);
            $stmt4->bindColumn('cod_padre', $codPadre_4);
            $stmt4->bindColumn('nivel', $nivel_4);
            $stmt4->bindColumn('cod_tipocuenta', $codTipoCuenta_4);
            $stmt4->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_4);
            $index_4=1;

            while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
              $cuentasNivel5=obtenerCuentasNivel5FlujoEfectivo(3,$codigo_4);
               $stringCuentasNivel5="";
              if(count($cuentasNivel5)>0){
                $cadenaCuentasNivel5=implode(",",$cuentasNivel5);
                $stringCuentasNivel5="and p.codigo in ($cadenaCuentasNivel5)";
               }
               $sumaNivel4=0;$html4="";$sumaDebeNivel4=0;$sumaHaberNivel4=0;           
              //listar los montos
              $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalleCuentasString($stringCuentasNivel5,$fechaFormateada,1,$unidades,$areas,$codigo_4,$gestion,"none");
               $vacio=0;
               while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
                   $numeroX=$rowComp['numero'];
                   $nombreX=trim($rowComp['nombre']);
                   $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                   if($codigo==1){
                    $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                    $tBolActivo+=$montoX;
                  }else{
                    $tBolPasivo+=$montoX;
                    if($codigo==3){
                      $vacio++;
                    }
                  }
                    $sumaDebeNivel4+=$rowComp['total_debe'];$sumaHaberNivel4+=$rowComp['total_haber'];
                    $sumaNivel4+=$montoX;  
                    if($montoX>0){
                      $tBolDebeSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%">'.number_format($montoX, 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX<0){
                      $tBolHaberSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.                           
                           '<td class="td-border-none text-right" width="20%">'.number_format(abs($montoX), 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX==0){
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%"></td>';   
                     $html4.='</tr>';      
                    }
                            
               $index++;         
               }

              if($sumaNivel4>0){
                $sumaNivel3+=$sumaNivel4; 
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4<0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4==0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              } 
            }
            if($sumaNivel3>0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format($sumaNivel3, 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }elseif($sumaNivel3<0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format(abs($sumaNivel3), 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }
            elseif($sumaNivel3==0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class="table-title td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class="table-title td-border-derecha text-right"></td>';   
              $html2.='</tr>';
            }
          }
          if($sumaNivel2>0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
          elseif($sumaNivel2<0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
           elseif($sumaNivel2==0){
             $sumaNivel1+=$sumaNivel2;
             $sumaDebeNivel1+=$sumaDebeNivel2; 
             $sumaHaberNivel1+=$sumaHaberNivel2;
             $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
             $monto_2=0;
             $html1.='';
              $html1.=$html2; 
          }
      }

    $nombre=formateaPlanCuenta($nombre, $nivel);
    $monto=0;
    if($sumaNivel1>0){
      $html.='';
     $html.=$html1;
    }elseif($sumaNivel1<0){
      $html.='';
     $html.=$html1;
    }
    elseif ($sumaNivel1==0) {
      $html.='';
     $html.=$html1;
    }
    
    $montoResultado+=$sumaNivel1;    
}


 $html.=    '</tbody></table>';
      $html.='<br><table class="table">'.
           '<tbody>';

     $html.='<tr class="">'.
                  '<td class="bold table-title text-left td-border-izquierda" width="80%" colspan="2">Total</td>'.
                  '<td class="text-right td-border-derecha">'.number_format(abs($montoResultado), 2, '.', ',').'</td>'.     
              '</tr>';
  $html.=    '</tbody></table>';

//FIN DEL FLUJO 3

  

  $cuentasNivel3=obtenerCuentasNivel3FlujoEfectivo(1);
$stringCuentasNivel3="";
if(count($cuentasNivel3)>0){
  $cadenaCuentasNivel3=implode(",",$cuentasNivel3);
  $stringCuentasNivel3="and p.codigo in ($cadenaCuentasNivel3)";
}
$html.='<br><table class="table">
            <thead>
               <tr class="bold table-title">'.
                '<td class="td-border-none text-left" colspan="2"></td>'.
                '<td class="td-border-none text-right">Bolivianos</td>
            </tr>
               <tr class="bold table-title">'.
                '<td class="td-border-bottom text-left" colspan="3">Saldo Final</td>
            </tr></thead>'.
           '<tbody>'; 
           $index=1;
           $tBolActivo=0;$tBolPasivo=0;
           $tBolDebe=0;$tBolHaber=0;
           $tBolDebeSaldo=0;$tBolHaberSaldo=0;
           $montoResultado=0;
// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=1 order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
     $sumaNivel1=0;$html1="";$sumaDebeNivel1=0;$sumaHaberNivel1=0;
     $stmt2 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                            (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=2 and p.cod_padre='$codigo' order by p.numero");
      $stmt2->execute();                      
      $stmt2->bindColumn('codigo', $codigo_2);
      $stmt2->bindColumn('numero', $numero_2);
      $stmt2->bindColumn('nombre', $nombre_2);
      $stmt2->bindColumn('cod_padre', $codPadre_2);
      $stmt2->bindColumn('nivel', $nivel_2);
      $stmt2->bindColumn('cod_tipocuenta', $codTipoCuenta_2);
      $stmt2->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_2);
      $index_2=1;
      while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
        
         $sumaNivel2=0;$html2="";$sumaDebeNivel2=0;$sumaHaberNivel2=0;
         $stmt3 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                              (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=3 and p.cod_padre='$codigo_2' $stringCuentasNivel3 order by p.numero");
         $stmt3->execute();                      
         $stmt3->bindColumn('codigo', $codigo_3);
         $stmt3->bindColumn('numero', $numero_3);
         $stmt3->bindColumn('nombre', $nombre_3);
         $stmt3->bindColumn('cod_padre', $codPadre_3);
         $stmt3->bindColumn('nivel', $nivel_3);
         $stmt3->bindColumn('cod_tipocuenta', $codTipoCuenta_3);
         $stmt3->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_3);
         $index_3=1;
         while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) { 
             $cuentasNivel4=obtenerCuentasNivel4FlujoEfectivo(1,$codigo_3);
             $stringCuentasNivel4="";
             if(count($cuentasNivel4)>0){
               $cadenaCuentasNivel4=implode(",",$cuentasNivel4);
               $stringCuentasNivel4="and p.codigo in ($cadenaCuentasNivel4)";
             }    

            $sumaNivel3=0;$html3="";$sumaDebeNivel3=0;$sumaHaberNivel3=0;
            $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='$codigo_3' $stringCuentasNivel4 order by p.numero");
            $stmt4->execute();                      
            $stmt4->bindColumn('codigo', $codigo_4);
            $stmt4->bindColumn('numero', $numero_4);
            $stmt4->bindColumn('nombre', $nombre_4);
            $stmt4->bindColumn('cod_padre', $codPadre_4);
            $stmt4->bindColumn('nivel', $nivel_4);
            $stmt4->bindColumn('cod_tipocuenta', $codTipoCuenta_4);
            $stmt4->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_4);
            $index_4=1;

            while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
              $cuentasNivel5=obtenerCuentasNivel5FlujoEfectivo(1,$codigo_4);
               $stringCuentasNivel5="";
              if(count($cuentasNivel5)>0){
                $cadenaCuentasNivel5=implode(",",$cuentasNivel5);
                $stringCuentasNivel5="and p.codigo in ($cadenaCuentasNivel5)";
               }
               $sumaNivel4=0;$html4="";$sumaDebeNivel4=0;$sumaHaberNivel4=0;           
              //listar los montos
              $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalleCuentasString($stringCuentasNivel5,$fechaFormateada,1,$unidades,$areas,$codigo_4,$gestion,"none");
               $vacio=0;
               while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
                   $numeroX=$rowComp['numero'];
                   $nombreX=trim($rowComp['nombre']);
                   $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                   if($codigo==1){
                    $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                    $tBolActivo+=$montoX;
                  }else{
                    $tBolPasivo+=$montoX;
                    if($codigo==3){
                      $vacio++;
                    }
                  }
                    $sumaDebeNivel4+=$rowComp['total_debe'];$sumaHaberNivel4+=$rowComp['total_haber'];
                    $sumaNivel4+=$montoX;  
                    if($montoX>0){
                      $tBolDebeSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%">'.number_format($montoX, 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX<0){
                      $tBolHaberSaldo+=$montoX;
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.                           
                           '<td class="td-border-none text-right" width="20%">'.number_format(abs($montoX), 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX==0){
                      $html4.='<tr class="">'.
                           '<td class="td-border-none text-left" width="15%">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left" width="65%">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right" width="20%"></td>';   
                     $html4.='</tr>';      
                    }
                            
               $index++;         
               }

              if($sumaNivel4>0){
                $sumaNivel3+=$sumaNivel4; 
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4<0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              }elseif($sumaNivel4==0){
                $sumaDebeNivel3+=$sumaDebeNivel4; 
                $sumaHaberNivel3+=$sumaHaberNivel4;
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='';
                $html3.=$html4;       
              } 
            }
            if($sumaNivel3>0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format($sumaNivel3, 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }elseif($sumaNivel3<0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class=" td-border-derecha text-right">'.number_format(abs($sumaNivel3), 2, '.', ',').'</td>';   
              $html2.='</tr>';
            }
            elseif($sumaNivel3==0){
              $sumaNivel2+=$sumaNivel3;
              $sumaDebeNivel2+=$sumaDebeNivel3; 
              $sumaHaberNivel2+=$sumaHaberNivel3;
              $nombre_3=trim($nombre_3);
              $html2.=$html3;
              $html2.='<tr class="bold">'.
                  '<td class="table-title td-border-izquierda text-left" colspan="2">SubTotal</td>'.
                  '<td class="table-title td-border-derecha text-right"></td>';   
              $html2.='</tr>';
            }
          }
          if($sumaNivel2>0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
          elseif($sumaNivel2<0){
            $sumaNivel1+=$sumaNivel2;
            $sumaDebeNivel1+=$sumaDebeNivel2; 
            $sumaHaberNivel1+=$sumaHaberNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='';
             $html1.=$html2; 
          }
           elseif($sumaNivel2==0){
             $sumaNivel1+=$sumaNivel2;
             $sumaDebeNivel1+=$sumaDebeNivel2; 
             $sumaHaberNivel1+=$sumaHaberNivel2;
             $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
             $monto_2=0;
             $html1.='';
              $html1.=$html2; 
          }
      }

    $nombre=formateaPlanCuenta($nombre, $nivel);
    $monto=0;
    if($sumaNivel1>0){
      $html.='';
     $html.=$html1;
    }elseif($sumaNivel1<0){
      $html.='';
     $html.=$html1;
    }
    elseif ($sumaNivel1==0) {
      $html.='';
     $html.=$html1;
    }
    
    $montoResultado+=$sumaNivel1;    
}


 $html.=    '</tbody></table>';
      $html.='<br><table class="table">'.
           '<tbody>';

     $html.='<tr class="">'.
                  '<td class="bold table-title text-left td-border-izquierda" width="80%" colspan="2">Total</td>'.
                  '<td class="text-right td-border-derecha">'.number_format(abs($montoResultado), 2, '.', ',').'</td>'.     
              '</tr>';
  $html.=    '</tbody></table>';

//FIN DEL FLUJO 4

  

$html.='</body>'.
      '</html>';
                    
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);
?>
