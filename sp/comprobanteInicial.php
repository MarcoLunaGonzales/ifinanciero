<?php
set_time_limit(0);
error_reporting(-1);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();

?>


<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title">Cargado Inicial de Comprobantes</h4>
          </div>
          <div class="card-body">
                  
<?php

echo "<h6>Hora Inicio Proceso: " . date("Y-m-d H:i:s")."</h6>";
echo "CONEXION ESTABLECIDA!!!!";

//INICIO PROCESO
$fechaActual="2021-01-01"; //registro de comprobante
$gestion=2020;
$fecha="2020-12-31";
$fechaTitulo= explode("-",$fecha);
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];

$moneda=1;
//$unidades=array(829,9,5,8,272,10,270,271,2692,3000);
//$entidades=array(2,3); 
$unidades=array(5);
$entidades=array(2);   
$unidadInsertar=5;
$insert_str="";
$codEmpresa=1;
$codMoneda=1;
$codEstadoComprobante=1;
$tipoComprobanteInsertar=3;
$cod_gestion=3584;
$gestionInsertar=2021;
$mes_gestion=1;
$codComprobante=39084;//obtenerCodigoComprobante(); //comprobante INICIAL LA PAZ
/*$numeroComprobante=numeroCorrelativoComprobanteFijo($cod_gestion,$unidadInsertar,$tipoComprobanteInsertar,$mes_gestion); //datos gestion 3584 "2021" y 1 "ENERO"
$glosa="BALANCE INICIAL 2021";
$sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) 
values ('$codComprobante','$codEmpresa','$unidadInsertar','$gestionInsertar','$codMoneda','$codEstadoComprobante','$tipoComprobanteInsertar','$fechaActual','$numeroComprobante','$glosa')";
$stmtInsertCab = $dbh->prepare($sqlInsertCab);
$flagSuccess=$stmtInsertCab->execute();*/
$areas=array("prueba","prueba");
$html = ''; 
$index=1;
$tBolActivo=0;$tBolPasivo=0;
$sqlDelete="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante;";
$stmtDelete=$dbh->prepare($sqlDelete);
$flagSuccessDelete=$stmtDelete->execute();

// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=1 and (p.codigo=1 or p.codigo=2 or p.codigo=3) order by p.numero");
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
     $sumaNivel1=0;$html1="";
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
        
         $sumaNivel2=0;$html2="";
         $stmt3 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                              (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=3 and p.cod_padre='$codigo_2' order by p.numero");
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
            $sumaNivel3=0;$html3="";
            $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='$codigo_3' order by p.numero");
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
               $sumaNivel4=0;$html4="";           
              //listar los montos
              $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalleInsert($fechaFormateada,1,$unidades,$areas,$codigo_4,$gestion,"none");
               $vacio=0;
               while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
                   $cuentaX=$rowComp['cod_cuenta'];
                   $cuentaAuxiliarX=$rowComp['cod_cuentaauxiliar'];
                   $unidadX=$rowComp['cod_unidadorganizacional'];
                   $areaX=$rowComp['cod_area'];
                   $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                   $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
                   if($codigo==1){
                    if(number_format($montoX,2,'.','')>0){
                      $insert_str = "($codigoDetalleComprobante,'$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','$montoX','0','$glosa','$index')"; 
                    }elseif (number_format($montoX,2,'.','')<0) {
                      $montoX=$montoX*(-1);
                      $insert_str = "($codigoDetalleComprobante,'$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','0','$montoX','$glosa','$index')";   
                    }
                  }else{                    
                    //LE CAMBIAMOS EL SIGNO AL PASIVO Y PATRIMONIO                    
                    if(number_format($montoX,2,'.','')<0){
                      $montoX=$montoX*(-1);
                      $insert_str = "($codigoDetalleComprobante,'$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','0','$montoX','$glosa','$index')"; 
                    }elseif (number_format($montoX,2,'.','')>0) {
                      $insert_str = "($codigoDetalleComprobante,'$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','$montoX','0','$glosa','$index')";   
                    }
                    $vacio++;
                  }
                  $sqlInsertDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ".$insert_str.";";
                  $stmtInsertDet=$dbh->prepare($sqlInsertDet);                  
                  if($cuentaX==obtenerValorConfiguracion(78)){
                      $sqlEstados="INSERT into estados_cuenta(cod_comprobantedetalle, cod_plancuenta, monto,  cod_proveedor, fecha, cod_comprobantedetalleorigen, cod_cuentaaux, cod_cajachicadetalle, cod_tipoestadocuenta, glosa_auxiliar) 
                      values ('$codigoDetalleComprobante','$cuentaX','$montoX','0','$fechaActual','0','$cuentaAuxiliarX','0','1','$glosa')";
                      $stmtInsertEstados = $dbh->prepare($sqlEstados);
                  }
                  
               $index++;         
               }/* Fin del primer while*/

               $cuentaResultado=obtenerValorConfiguracion(47);
               if($codigo_4==obtieneCuentaPadre($cuentaResultado)&&$vacio==0){                  
                  $nombreResultado=nameCuenta($cuentaResultado);
                  $numeroResultado=obtieneNumeroCuenta($cuentaResultado);
                  $datosResultados=sumaMontosDebeHaberComprobantesDetalleResultadosInsert($fechaFormateada,1,$unidades,$areas,$gestion,"none");
                  while ($rowRes = $datosResultados->fetch(PDO::FETCH_ASSOC)) {
                    $unidadX=$rowRes['cod_unidadorganizacional'];
                    $areaX=$rowRes['cod_area'];
                     if($rowRes['tipo']==1){
                      $montoResultadoIngreso=$rowRes['t_debe']-$rowRes['t_haber'];
                      $montoResultadoIngreso=abs($montoResultadoIngreso);
                     }else{
                      $montoResultadoEgreso=$rowRes['t_debe']-$rowRes['t_haber'];
                     } 
                  }
                  $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
                  $index++;
                  //$montoResultado=$tbob
                  $montoResultado=$montoResultadoIngreso-$montoResultadoEgreso;
                  if(number_format($montoResultado,2,'.','')<0){
                      $montoResultado=$montoResultado*(-1);
                      $insert_str = "('$codComprobante','$cuentaResultado','0','$unidadX','$areaX','0','$montoResultado','$glosa','$index')"; 
                  }elseif (number_format($montoResultado,2,'.','')>0) {
                      $insert_str = "('$codComprobante','$cuentaResultado','0','$unidadX','$areaX','0','$montoResultado','$glosa','$index')";   
                  }
                  $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ".$insert_str.";";
                  $stmtInsertDet=$dbh->prepare($sqlInsertDet);
               }


            }//FIN NIVEL 4
          }//nivel 3
      } //nivel 2
    }//nivel 1
//FIN PROCESO


    //$insert_str = substr_replace($insert_str, '', -1, 1);
    /*$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ".$insert_str.";";
    $stmtInsertDet=$dbh->prepare($sqlInsertDet);
    $flagSuccess2=$stmtInsertDet->execute();*/

echo "<h6>HORA FIN PROCESO CARGADO INICIAL COMPROBANTES: " . date("Y-m-d H:i:s")."</h6>";
?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

<?php 
function listaSumaMontosDebeHaberComprobantesDetalleInsert($fechaFinal,$tipoBusqueda,$arrayUnidades,$arrayAreas,$padre,$gestion,$fechaInicio){
      $dbh = new Conexion();
      $sql="";
      $sqlAreas="";
      $sqlUnidades="";
      $fechaFinalMod=explode("/", $fechaFinal);

      $arrayUnidades=implode(",",$arrayUnidades);
      //formateando fecha
      if($fechaInicio=="none"){
        $fi=$fechaFinalMod[2]."-01-01";
      }else{
        $fechaFinalModIni=explode("/", $fechaInicio);
        $fi=$fechaFinalModIni[2]."-".$fechaFinalModIni[1]."-".$fechaFinalModIni[0];
      }
    
      $fa=$fechaFinalMod[2]."-".$fechaFinalMod[1]."-".$fechaFinalMod[0];
      $sql="SELECT cuentas_monto.* from plan_cuentas p join 
             (select d.cod_cuenta,d.cod_cuentaauxiliar,d.cod_unidadorganizacional,d.cod_area,sum(debe) as total_debe,sum(haber) as total_haber 
              from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante 
              join areas a on a.codigo=d.cod_area
              join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional
              join plan_cuentas p on p.codigo=d.cod_cuenta
              where c.fecha between '$fi 00:00:00' and '$fa 23:59:59' and d.cod_unidadorganizacional in ($arrayUnidades) and c.cod_estadocomprobante<>2 group by d.cod_cuenta,d.cod_cuentaauxiliar order by d.cod_cuenta) cuentas_monto
          on p.codigo=cuentas_monto.cod_cuenta where p.cod_padre=$padre order by p.numero";
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      return $stmt;
  }
function numeroCorrelativoComprobanteFijo($codGestion,$unidad,$tipoComprobante,$codMes){
    $dbh = new Conexion();
    $mesActivo=$codMes;

    $sql1="SELECT g.nombre from gestiones g where codigo='$codGestion'";
    $stmt1 = $dbh->prepare($sql1);
    $stmt1->execute();
    $anio=2021;
    while ($row1= $stmt1->fetch(PDO::FETCH_ASSOC)) {
      $anio=$row1['nombre'];
    }

    $fechaInicio=$anio."-".$mesActivo."-01";
    $fechaFin=date('Y-m-d',strtotime($fechaInicio.'+1 month'));
    $fechaFin=date('Y-m-d',strtotime($fechaFin.'-1 day'));

    $sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from comprobantes c where c.cod_tipocomprobante='$tipoComprobante' and c.cod_unidadorganizacional=$unidad and c.fecha between '$fechaInicio 00:00:00' and '$fechaFin 23:59:59' and c.cod_estadocomprobante<>2";
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $nroCorrelativo=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $nroCorrelativo=$row['codigo'];
    }

    return $nroCorrelativo;
   }

 function sumaMontosDebeHaberComprobantesDetalleResultadosInsert($fechaFinal,$tipoBusqueda,$arrayUnidades,$arrayAreas,$gestion,$fechaInicio)
   {
     $dbh = new Conexion();
     $sql="";
     $sqlAreas="";
     $sqlUnidades="";
    $fechaFinalMod=explode("/", $fechaFinal);
    //formateando fecha
     if($fechaInicio=="none"){
      $fi=$fechaFinalMod[2]."-01-01";
     }else{
       $fechaFinalModIni=explode("/", $fechaInicio);
       $fi=$fechaFinalModIni[2]."-".$fechaFinalModIni[1]."-".$fechaFinalModIni[0];
     }
    
    $fa=$fechaFinalMod[2]."-".$fechaFinalMod[1]."-".$fechaFinalMod[0];
    //$fi=$fechaFinalMod[2]."-01-01";

     for ($i=0; $i < count($arrayAreas); $i++) {
        if($i==0){
          $sqlAreas.="and (";
        }
        if($i==(count($arrayAreas)-1)){
          $sqlAreas.="d.cod_area='".$arrayAreas[$i]."')";
         }else{
          $sqlAreas.="d.cod_area='".$arrayAreas[$i]."' or ";
         }  
     }
     //busqueda de unidades
     for ($i=0; $i < count($arrayUnidades); $i++) {
        if($i==0){
          $sqlUnidades.="and (";
        }
        if($i==(count($arrayUnidades)-1)){
          $sqlUnidades.="c.cod_unidadorganizacional='".$arrayUnidades[$i]."')";
         }else{
          $sqlUnidades.="c.cod_unidadorganizacional='".$arrayUnidades[$i]."' or ";
         }  
     }
     
     $sql="(SELECT cod_unidadorganizacional,cod_area,sum(total_debe) as t_debe,sum(total_haber) as t_haber,1 as tipo from plan_cuentas p join 
             (select d.cod_unidadorganizacional,d.cod_area,d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber 
              from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante 
              where (c.fecha between '$fi 00:00:00' and '$fa 23:59:59') $sqlUnidades and c.cod_gestion='$gestion' and c.cod_estadocomprobante<>2  group by (d.cod_cuenta) order by d.cod_cuenta) cuentas_monto
          on p.codigo=cuentas_monto.cod_cuenta where p.numero like '4%' and p.nivel=5 order by p.numero)
           UNION
           (SELECT cod_unidadorganizacional,cod_area,sum(total_debe) as t_debe,sum(total_haber) as t_haber,2 as tipo from plan_cuentas p join 
             (select d.cod_unidadorganizacional,d.cod_area,d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber 
              from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante
              where (c.fecha between '$fi 00:00:00' and '$fa 23:59:59') $sqlUnidades and c.cod_gestion='$gestion' and c.cod_estadocomprobante<>2 group by (d.cod_cuenta) order by d.cod_cuenta) cuentas_monto
          on p.codigo=cuentas_monto.cod_cuenta where p.numero like '5%' and p.nivel=5 order by p.numero)";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     return $stmt;
  }  
?>