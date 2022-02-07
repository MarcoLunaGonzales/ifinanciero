<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestion"];
$cuenta = $_POST["cuenta"];
$unidad = $_POST["unidad"];

$proveedores=$_POST["proveedores"];

$fecha_desde=$_POST["fecha_desde"];
$fecha=$_POST["fecha_hasta"];

$desde=$fecha_desde;
$hasta=$fecha;
$tipo_cp=$_POST["tipo_cp"];
$ver_saldo=$_POST["ver_saldo"];
$StringCuenta=implode(",", $cuenta);
$StringUnidades=implode(",", $unidad);

if($ver_saldo==3){//saldos Generales
     
   include "reportesEstadoCuentasPrint_saldos.php";
}else{

$proveedoresString=implode(",", $proveedores);

$proveedoresStringAux="and e.cod_cuentaaux in ($proveedoresString)";

if(count($proveedores)==(int)$_POST["numero_proveedores"]){
  $proveedoresStringAux="";
}





$stringGeneraCuentas="";

foreach ($cuenta as $cuentai ) {    
    $stringGeneraCuentas.=nameCuenta($cuentai).",";
    # code...
}
$stringGeneraUnidades="";
foreach ($unidad as $unidadi ) {    
    $stringGeneraUnidades.=" ".abrevUnidad($unidadi)." ";
    # code...
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
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon">
                      <!--div class="float-right col-sm-2">
                        <h6 class="card-title">Exportar como:</h6>
                      </div-->
                      <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                          Estado de Cuentas
                      </h4>
                      <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                      <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                      <h6 class="card-title">Gestion: <?= $NombreGestion; ?></h6>                        
                      <h6 class="card-title">Cuenta: <?=$stringGeneraCuentas;?></h6>
                      <h6 class="card-title">Unidad:<?=$stringGeneraUnidades?></h6>             
                      <div class="row">
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Oficina: </b> <small><?=$unidadAbrev?></small></h6></div>
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Area: </b> <small><?=$areaAbrev?></small></h6></div>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php 

                            $html='<table class="table table-bordered table-condensed" id="tablePaginatorFixedEstadoCuentas">'.
                                '<thead>'.
                                    '<tr class="">'.
                                        '<th class="text-left">Of.</th>'.
                                        '<th class="text-left">CC</th>'.
                                        '<th class="text-left">Tipo/#</th>'.
                                        '<th class="text-left">FechaComp</th>'.
                                        '<th class="text-left">FechaEC</th>'.
                                        '<th class="text-left">Proveedor/Cliente</th>'.
                                        '<th class="text-left">Glosa</th>'.
                                        '<th class="text-right">Debe</th>'.
                                        '<th class="text-right">Haber</th>'.
                                        '<th class="text-right">Saldo</th>'.
                                    '</tr>'.
                                '</thead>'.
                                '<tbody>';
                                    
                                    foreach ($cuenta as $cuentai ) {
                                        $nombreCuenta=nameCuenta($cuentai);//nombre de cuenta    
                                        
                                        $html.='<tr style="background-color:#9F81F7;">                                    
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td class="text-left small" colspan="5">CUENTA</td>
                                            <td class="text-left small" colspan="5">'.$nombreCuenta.'</td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>

                                        </tr>'; 
                                        
                                        $sqlFechaEstadoCuenta="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
                                            
                                        if(isset($_POST['cierre_anterior'])){
                                          $sqlFechaEstadoCuenta="and e.fecha<='$hasta 23:59:59'";  
                                         }

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
                                              $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' and cc.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and cc.cod_unidadorganizacional in ($StringUnidades) and e.cod_cuentaaux in ($proveedoresString) and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and e.codigo=$codigoX order by ca.nombre, cc.fecha");
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
                                            
                                            /*$html.='<tr style="background-color:#58D68D" class="'.$mostrarFilasEstado.'">
                                                <td colspan="10"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                            </tr>';*/
                                            
                                            }
                                            // $cod_tipoCuenta=$row['cod_tipoestadocuenta'];
                                            
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
                                            
                                            if(isset($_POST['cierre_posterior'])){
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

                                            if($tipoDebeHaber==2){//proveedor
                                                if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
                                                   $totalCredito=$totalCredito+$montoX;
                                                }
                                                
                                                $nombreProveedorX=nameProveedor($codProveedor);
                                                
                                                $html.='<tr class="bg-white det-estados '.$estiloEstados.' '.$mostrarFilasEstado.'" '.$estiloFilasEstado.'>
                                                    <td class="text-left small">'.$nombreUnidadCabecera.'</td>
                                                    <td class="text-left small">'.$nombreUnidadO.'-'.$nombreAreaCentroCosto.'</td>
                                                    <td class="text-center small">'.$nombreComprobanteX.'</td>
                                                    <td class="text-left small">'.$fechaComprobante.'</td>
                                                    <td class="text-left small">'.$fechaX.'</td>
                                                    <!--td class="text-left small">'.$nombreCuentaAuxiliarX.'['.$nombreProveedorX.']</td-->
                                                    <td class="text-left small">'.$nombreCuentaAuxiliarX.'</td>
                                                    <td class="text-left small">'.$glosaMostrar.'</td>
                                                    <td class="text-right text-muted font-weight-bold small">'.formatNumberDec($montoEstado).'</td>
                                                    <td class="text-right small">'.formatNumberDec($montoX).'</td>
                                                    <td class="text-right small font-weight-bold" '.$estiloFilasEstadoSaldo.'>'.formatNumberDec($montoX-$montoEstado).'</td>
                                                </tr>'; 

                                            }else{ //cliente
                                                $nombreProveedorX=namecliente($codProveedor);
                                                if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
                                                  $totalDebito=$totalDebito+$montoX;
                                                 }
                                                
                                                 $html.='<tr class="bg-white det-estados '.$estiloEstados.' '.$mostrarFilasEstado.'" '.$estiloFilasEstado.'>
                                                    <td class="text-left small">'.$nombreUnidadCabecera.'</td>
                                                    <td class="text-left small">'.$nombreUnidadO.'-'.$nombreAreaCentroCosto.'</td>
                                                    <td class="text-center small">'.$nombreComprobanteX.'</td>
                                                    <td class="text-left small">'.$fechaComprobante.'</td>
                                                    <td class="text-left small">'.$fechaX.'</td>
                                                    <!--td class="text-left small">'.$nombreCuentaAuxiliarX.'['.$nombreProveedorX.']</td-->
                                                    <td class="text-left small">'.$nombreCuentaAuxiliarX.'</td>
                                                    <td class="text-left small">'.$glosaMostrar.'</td>
                                                    <td class="text-right small">'.formatNumberDec($montoX).'</td>
                                                    <td class="text-right text-muted font-weight-bold small">'.formatNumberDec($montoEstado).'</td>
                                                    <td class="text-right small font-weight-bold" '.$estiloFilasEstadoSaldo.'>'.formatNumberDec($montoX-$montoEstado).'</td>
                                                </tr>';

                                            }    

                                            

                                                $stmt_d = $dbh->prepare("SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, c.codigo as codigocomprobante, c.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos
                                                    from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 $sqlFechaEstadoCuentaPosterior and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
                                                $stmt_d->execute();
                                                while ($row_d = $stmt_d->fetch()) {
                                                    $codigoX_d=$row_d['codigo'];
                                                    $codCompDetX_d=$row_d['cod_comprobantedetalle'];
                                                    $codPlanCuentaX_d=$row_d['cod_plancuenta'];
                                                    $codProveedor_d=$row_d['cod_proveedor'];
                                                    $montoX_d=$row_d['monto'];
                                                    $fechaX_d=$row_d['fecha'];
                                                    $fechaX_d=strftime('%d/%m/%Y',strtotime($fechaX_d));
                                                    $glosaAuxiliar_d=$row_d['glosa_auxiliar'];
                                                    $glosaX_d=$row_d['glosa'];
                                                    $debeX_d=$row_d['debe'];
                                                    $haberX_d=$row_d['haber'];
                                                    $codigoExtra_d=$row_d['extra'];
                                                    $codigoComprobanteY=$row_d['codigocomprobante'];
                                                    $codUnidadCabeceraY=$row_d['cod_unidad_cab'];
                                                    $codAreaCentroCostoY=$row_d['area_centro_costos'];
                                                    
                                                    $tituloMontoDebe=formatNumberDec($montoX_d);
                                                    if($montoX_d!=$debeX_d){
                                                        $tituloMontoDebe=formatNumberDec($montoX_d).' <b class="text-danger">(*'.formatNumberDec($debeX_d).'*)</b>';
                                                    }
                                                    $nombreComprobanteY=nombreComprobante($codigoComprobanteY);
                                                    $glosaMostrar_d="";
                                                    if($glosaAuxiliar_d!=""){
                                                        $glosaMostrar_d=$glosaAuxiliar_d;
                                                    }else{
                                                        $glosaMostrar_d=$glosaX_d;
                                                    }
                                                    list($tipoComprobante_d, $numeroComprobante_d, $codUnidadOrganizacional_d, $mesComprobante_d, $fechaComprobante_d)=explode("|", $codigoExtra_d);
                                                    $nombreTipoComprobante_d=abrevTipoComprobante($tipoComprobante_d)."-".$mesComprobante_d;
                                                    $nombreUnidadO_d=abrevUnidad_solo($codUnidadOrganizacional_d);
                                                    $nombreUnidadCabecera_d=abrevUnidad_solo($codUnidadCabeceraY);
                                                    $nombreAreaCentroCosto_d=abrevArea_solo($codAreaCentroCostoY);

                                                    $fechaComprobante_d=strftime('%d/%m/%Y',strtotime($fechaComprobante_d));
                                                     $saldo=$saldo-$montoX_d;
                                                     
                                                     
                                                     
                                                    if($tipoDebeHaber==2){//proveedor
                                                        $nombreProveedorX_d=nameProveedor($codProveedor_d);
                                                        if($mostrarFilasEstado!="d-none"&&$estiloEstados==""){
                                                          $totalDebito=$totalDebito+$montoX_d;    
                                                        }
                                                        
                                                        $html.='<tr style="background-color:#ECCEF5;" class="'.$estiloEstados.' '.$mostrarFilasEstado.' text-muted">
                                                            <td class="text-left small">&nbsp;&nbsp;&nbsp;&nbsp;'.$nombreUnidadCabecera_d.'</td>
                                                            <td class="text-left small">'.$nombreUnidadO_d.'-'.$nombreAreaCentroCosto_d.'</td>
                                                            <td class="text-center small">'.$nombreComprobanteY.'</td>
                                                            <td class="text-left small">'.$fechaComprobante_d.'</td>
                                                            <td class="text-left small">'.$fechaX_d.'</td>
                                                            <td class="text-left small">'.$nombreProveedorX_d.'</td>  
                                                            <td class="text-left small">'.$glosaMostrar_d.'</td>
                                                            <td class="text-right small">'.$tituloMontoDebe.'</td>
                                                            <td class="text-right small">'.formatNumberDec(0).'</td>
                                                            <td class="text-right small font-weight-bold"></td>
                                                        </tr>';/*formatNumberDec($saldo)*/
                                                    }else{ //cliente
                                                        $nombreProveedorX_d=namecliente($codProveedor_d);
                                                        if($nombreProveedorX_d=='0')$nombreProveedorX_d=nameProveedor($codProveedor_d);

                                                        if($mostrarFilasEstado!="d-none"&&$estiloEstados==""){
                                                          $totalCredito=$totalCredito+$montoX_d;    
                                                        }
                                                        
                                                        $html.='<tr  style="background-color:#ECCEF5;" class="'.$estiloEstados.' '.$mostrarFilasEstado.' text-muted">
                                                            <td class="text-left small">&nbsp;&nbsp;&nbsp;&nbsp;'.$nombreUnidadCabecera_d.'</td>
                                                            <td class="text-left small">'.$nombreUnidadO_d.'-'.$nombreAreaCentroCosto_d.'</td>
                                                            <td class="text-center small">'.$nombreComprobanteY.'</td>
                                                            <td class="text-left small">'.$fechaComprobante_d.'</td>
                                                            <td class="text-left small">'.$fechaX_d.'</td>
                                                            <td class="text-left small">'.$nombreProveedorX_d.'</td>  
                                                            <td class="text-left small">'.$glosaMostrar_d.'</td>
                                                            <td class="text-right small">'.formatNumberDec(0).'</td>
                                                            <td class="text-right small">'.formatNumberDec($montoX_d).'</td>
                                                            <td class="text-right small font-weight-bold"></td>
                                                        </tr>';/*formatNumberDec($saldo)*/

                                                     }
                                                    
                                                }
                                            }    
                                                $i++;
                                                $indice++;
                                    }
                                    $totalSaldo=$totalDebito-$totalCredito;
                                    if($totalSaldo<0){
                                        $totalSaldo=$totalSaldo*(-1);
                                    }                                        
                                        $html.='<tr>                                            
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td class="text-right small" colspan="7">Total:</td>
                                            <td class="text-right small font-weight-bold">'.formatNumberDec($totalDebito).'</td>
                                            <td class="text-right small font-weight-bold">'.formatNumberDec($totalCredito).'</td>
                                            <td class="text-right small font-weight-bold">'.formatNumberDec($totalSaldo).'</td>
                                        </tr>   
           

                                </tbody>
                            </table>';
                            echo $html;

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>


<?php

}

?>
