<?php
set_time_limit(0);
error_reporting(-1);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();


error_reporting(E_ALL);
 ini_set('display_errors', '1');



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
$fechaActual="2023-01-01"; //registro de comprobante
$gestion=2023;
$fechaDesde="2023-01-01";
$fecha="2023-12-31";
$fechaTitulo= explode("-",$fecha);
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];

$moneda=1;
//$unidades=array(829,9,5,8,272,10,270,271,2692,3000);
//$entidades=array(2,3); 
//UNIDADES LA PAZ 5; SANTA CRUZ 10; COCHABAMBA 9
$unidades=array(829,9,5,8,272,10,270,271,2692);
$entidades=array(2);   
$unidadInsertar=5;
$insert_str="";
$codEmpresa=1;
$codMoneda=1;
$codEstadoComprobante=1;
$tipoComprobanteInsertar=3;

$cod_gestionAnterior=3685;
$cod_gestion=3686;

$gestionInsertar=2023;
$mes_gestion=1;
$glosa="BALANCE INICIAL 2024";

$codComprobante=93783;//obtenerCodigoComprobante(); // IBNORCA: 39843 LOCALHOST: 39084 comprobante INICIAL LA PAZ 
/*$numeroComprobante=numeroCorrelativoComprobanteFijo($cod_gestion,$unidadInsertar,$tipoComprobanteInsertar,$mes_gestion); //datos gestion 3584 "2021" y 1 "ENERO"

$sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) 
values ('$codComprobante','$codEmpresa','$unidadInsertar','$gestionInsertar','$codMoneda','$codEstadoComprobante','$tipoComprobanteInsertar','$fechaActual','$numeroComprobante','$glosa')";
$stmtInsertCab = $dbh->prepare($sqlInsertCab);
$flagSuccess=$stmtInsertCab->execute();*/
$areas=array(11,12,13,38,39,40,501,502,1235,4357,5000,6000,6001);
$html = ''; 
$index=1;
$tBolActivo=0;$tBolPasivo=0;
$sqlDelete="DELETE from comprobantes_detalle where cod_comprobante=$codComprobante;";
$stmtDelete=$dbh->prepare($sqlDelete);
//$flagSuccessDelete=$stmtDelete->execute();

$sqlDelete="DELETE from estados_cuenta where cod_comprobantedetalle in (SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante)";
$stmtDelete=$dbh->prepare($sqlDelete);
//$flag=$stmtDelete->execute();

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
               echo "Entro<br>";
              //listar los montos
               $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalleInsert($fechaFormateada,1,$unidades,$areas,$codigo_4,$gestion,"none");
               $vacio=0;
               while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
                   $cuentaX=$rowComp['cod_cuenta'];
                   //$glosaX=$rowComp['glosa'];
                   $glosaX="COMPROBANTE INICIAL 2024";
                   $cuentaAuxiliarX=0;
                   $datosExplode=explode(",",$rowComp['cod_unidadorganizacional']);
                   if(isset($datosExplode[1])){
                     $unidadX=$datosExplode[0];
                   }else{
                     $unidadX=$rowComp['cod_unidadorganizacional'];
                   }
                   
                   $datosExplode=explode(",",$rowComp['cod_area']);
                   
                   if(isset($datosExplode[1])){
                     $areaX=$datosExplode[0];
                   }else{
                     $areaX=$rowComp['cod_area'];
                   }
                   if($areaX==null||$areaX==""||$areaX==0){
                    $areaX=501;
                   }

                   $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);
                   $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
                   if($codigo==1){
                    if(number_format($montoX,2,'.','')>0){
                      $montoX=number_format($montoX,2,'.','');
                      $insert_str = "('$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','$montoX','0','$glosaX','$index')"; 
                    }elseif (number_format($montoX,2,'.','')<0) {
                      $montoX=$montoX*(-1);
                      $montoX=number_format($montoX,2,'.','');
                      $insert_str = "('$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','0','$montoX','$glosaX','$index')";   
                    }
                  }else{                    
                    //LE CAMBIAMOS EL SIGNO AL PASIVO Y PATRIMONIO                    
                    if(number_format($montoX,2,'.','')<0){
                      $montoX=$montoX*(-1);
                      $montoX=number_format($montoX,2,'.','');
                      $insert_str = "('$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','0','$montoX','$glosaX','$index')"; 
                    }elseif (number_format($montoX,2,'.','')>0) {
                      $montoX=number_format($montoX,2,'.','');
                      $insert_str = "('$codComprobante','$cuentaX','$cuentaAuxiliarX','$unidadX','$areaX','$montoX','0','$glosaX','$index')";   
                    }
                    $vacio++;
                  }
                  if(!($cuentaX==64 || $cuentaX==67 || $cuentaX==113 || $cuentaX==153 || $cuentaX==154 || $cuentaX==161)){
                    if($insert_str!=""){
                      $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
                      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ".$insert_str.";";
                      echo $sqlInsertDet."<br>";
                      $stmtInsertDet=$dbh->prepare($sqlInsertDet);                  
                      $flagSuccess2=$stmtInsertDet->execute();
                    }
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
                     $montoResultado=number_format($montoResultado,2,'.','');                     
                     $insert_str = "('$codComprobante','$cuentaResultado','0','$unidadX','$areaX','0','$montoResultado','$glosa','$index')"; 
                  }elseif (number_format($montoResultado,2,'.','')>0) {
                    $montoResultado=number_format($montoResultado,2,'.','');
                      $insert_str = "('$codComprobante','$cuentaResultado','0','$unidadX','$areaX','0','$montoResultado','$glosa','$index')";   
                  }
                  $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ".$insert_str.";";
                  $stmtInsertDet=$dbh->prepare($sqlInsertDet);
                  $flagSuccess2=$stmtInsertDet->execute();
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
function listaSumaMontosDebeHaberComprobantesDetalleInsertAux($fechaFinal,$tipoBusqueda,$arrayUnidades,$arrayAreas,$padre,$gestion,$fechaInicio){
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
              where c.fecha between '$fi 00:00:00' and '$fa 23:59:59' and d.cod_unidadorganizacional in ($arrayUnidades) and c.cod_estadocomprobante<>2 group by d.cod_cuenta order by d.cod_cuenta) cuentas_monto
          on p.codigo=cuentas_monto.cod_cuenta where p.cod_padre=$padre order by p.numero";
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      return $stmt;
  }

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
             (select d.cod_cuenta,GROUP_CONCAT(d.cod_unidadorganizacional) as cod_unidadorganizacional,GROUP_CONCAT(d.cod_area) AS cod_area,sum(debe) as total_debe,sum(haber) as total_haber 
              from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante 
              join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional
              join plan_cuentas p on p.codigo=d.cod_cuenta
              where c.fecha between '$fi 00:00:00' and '$fa 23:59:59' and d.cod_unidadorganizacional in ($arrayUnidades) and c.cod_estadocomprobante<>2 group by d.cod_cuenta order by d.cod_cuenta) cuentas_monto
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

function listaMontosDebeHaberEstadosCuentasComprobantesDetalleInsert($gestion,$cuentaIn,$unidad,$area,$fecha_desde,$fecha,$tipo_cp){
 $dbh = new Conexion();
//RECIBIMOS LAS VARIABLES
$cuenta[0]=$cuentaIn;
$desde=$fecha_desde;
$hasta=$fecha;
$ver_saldo=1; //1 SOLO SALDOS 2 //VER TODO
$proveedoresStringAux="";
$StringCuenta=implode(",", $cuenta);
$StringUnidades=implode(",", $unidad);
$stringGeneraCuentas="";
$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];
$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;
$unidadCosto=$unidad;
$areaCosto=$area;
$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);

$cierre_anterior=1; //1 SI; 2 NO
$datosEstadosCuenta=null;
$jindex=0;
//INICIO QUERY DE ESTADOS DE CUENTAS
  foreach ($cuenta as $cuentai ) {
     $sqlFechaEstadoCuenta="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
                                            
     if($cierre_anterior==1){
       $sqlFechaEstadoCuenta="and e.fecha<='$hasta 23:59:59'";  
      }

      $sql="SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, d.cod_cuenta, ca.nombre, cc.codigo as codigocomprobante, cc.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) order by e.fecha"; //ca.nombre, 
      //echo $sql;
      $stmtUO = $dbh->prepare($sql);
      $stmtUO->execute();
      $codPlanCuentaAuxiliarPivotX=-10000;
      
      $sql="SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, d.cod_cuenta, ca.nombre, cc.codigo as codigocomprobante, cc.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) order by e.fecha"; //ca.nombre, 
                                        //echo $sql;
                                        $stmtUO = $dbh->prepare($sql);
                                        $stmtUO->execute();
                                        $codPlanCuentaAuxiliarPivotX=-10000;
                                        while ($row = $stmtUO->fetch()) {
                                            $codigoX=$row['codigo'];
                                            
                                            $existeCuentas=0;
                                            $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad
                                                    from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
                                            $stmtCantidad->execute();
                                            while ($rowCantidad = $stmtCantidad->fetch()) {
                                                $existeCuentas=$rowCantidad['cantidad'];
                                            }
                                            
                                            $existeCuentas2=0;
                                              $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' and cc.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and cc.cod_unidadorganizacional in ($StringUnidades) and $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and e.codigo=$codigoX order by ca.nombre, cc.fecha");
                                              $stmtCantidad->execute();
                                              while ($rowCantidad = $stmtCantidad->fetch()) {
                                                  $existeCuentas2=$rowCantidad['cantidad'];
                                              }

                                            $mostrarFilasEstado="";
                                            $estiloFilasEstado="";
                                            $estiloFilasEstadoSaldo="";
                                            if($sqlFechaEstadoCuenta==""){
                                                if($existeCuentas==0){
                                                  if($existeCuentas2==0){
                                                     $mostrarFilasEstado="d-none";
                                                  }
                                                }else{
                                                    if($existeCuentas2==0){
                                                     $estiloFilasEstado="style='background:#F9F9FC !important;color:#D6D6DA  !important;'";
                                                     $estiloFilasEstadoSaldo="style='color:red !important;'";
                                                    }      
                                                }
                                                
                                            }

                                            $codCompDetX=$row['cod_comprobantedetalle'];
                                            $codPlanCuentaX=$row['cod_cuenta'];
                                            $codProveedor=$row['cod_proveedor'];
                                            $montoX=$row['monto'];
                                            $fechaX=$row['fecha'];
                                            $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
                                            $glosaAuxiliar=$row['glosa_auxiliar'];
                                            $glosaX=$row['glosa'];
                                            $debeX=$row['debe'];
                                            $haberX=$row['haber'];
                                            $codigoExtra=$row['extra'];
                                            $codPlanCuentaAuxiliarX=$row['cod_cuentaaux'];
                                            $codigoComprobanteX=$row['codigocomprobante'];
                                            $codUnidadCabecera=$row['cod_unidad_cab'];
                                            $codAreaCentroCosto=$row['area_centro_costos'];

                                            $nombreComprobanteX=nombreComprobante($codigoComprobanteX);
                                            $nombreCuentaAuxiliarX=nameCuentaAuxiliar($codPlanCuentaAuxiliarX);
                                            $tipoDebeHaber=verificarTipoEstadoCuenta($codPlanCuentaX);


                                            if($codPlanCuentaAuxiliarX!=$codPlanCuentaAuxiliarPivotX){
                                                $saldo=0;
                                                $codPlanCuentaAuxiliarPivotX=$codPlanCuentaAuxiliarX;
                                            
                                            }
                                            
                                            $glosaMostrar="";
                                            if($glosaAuxiliar!=""){
                                                $glosaMostrar=$glosaAuxiliar;
                                            }else{
                                                $glosaMostrar=$glosaX;
                                            }
                                            list($tipoComprobante, $numeroComprobante, $codUnidadOrganizacional, $mesComprobante, $fechaComprobante)=explode("|", $codigoExtra);
                                            $nombreTipoComprobante=abrevTipoComprobante($tipoComprobante)."-".$mesComprobante;

                                            $nombreUnidadO=abrevUnidad_solo($codUnidadOrganizacional);
                                            $nombreUnidadCabecera=abrevUnidad_solo($codUnidadCabecera);
                                            $nombreAreaCentroCosto=abrevArea_solo($codAreaCentroCosto);

                                            $fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));
                                            $sqlFechaEstadoCuentaPosterior="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
                                            
                                            if(isset($cierre_posterior)){
                                              $sqlFechaEstadoCuentaPosterior="and e.fecha >= '$desde 00:00:00'";  
                                             }
                                            //SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
                                            $sqlContra="SELECT sum(e.monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen='$codigoX'";
                                            //echo $sqlContra;
                                            $stmtContra = $dbh->prepare($sqlContra);
                                            $stmtContra->execute();                                    
                                            $saldo+=$montoX;//-$montoContra;                                    
                                            // echo "tipo:".$cod_tipoCuenta;
                                            $montoEstado=0;$estiloEstados="";
                                            $stmtSaldo = $dbh->prepare("SELECT sum(e.monto) as monto
                                                    from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 $sqlFechaEstadoCuentaPosterior and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
                                            $stmtSaldo->execute();
                                            while ($rowSaldo = $stmtSaldo->fetch()) {
                                                $montoEstado=$rowSaldo['monto'];
                                            }

                                          if(formatNumberDec($montoX)==formatNumberDec($montoEstado)&&$ver_saldo==1){
                                              //validacion para saldos 0 si esta filtrado
                                             $estiloEstados="d-none";
                                           }
                                          
                                          if($mostrarFilasEstado!="d-none"&&$estiloEstados!="d-none"){
                                           $datosEstadosCuenta[$jindex]=(object)array(
                                              'codigo' => $codigoX,
                                              'montoEnvio' => ($montoX-$montoEstado),
                                              'codProveedorEnvio' =>$codProveedor,
                                              'codPlanCuentaAuxiliarEnvio' =>$codPlanCuentaAuxiliarX,
                                              'codPlanCuentaEnvio' =>$codPlanCuentaX,
                                              'glosaMostrarEnvio' =>$glosaMostrar,
                                              'codAreaCentroCostoEnvio' =>$codAreaCentroCosto,
                                              'codUnidadOrganizacionalEnvio' =>$codUnidadOrganizacional
                                              );                                       
                                            $jindex++;
                                            
                                          }
                                          //FIN DATOS
                                        } //FIN WHILE    
                 $i++;
                 $indice++;
  }
  return $datosEstadosCuenta;
}   
?>