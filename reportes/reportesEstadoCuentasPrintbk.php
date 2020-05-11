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
$fecha=$_POST["fecha"];
$tipo_cp=$_POST["tipo_cp"];


$proveedoresString=implode(",", $proveedores);
$StringCuenta=implode(",", $cuenta);
$StringUnidades=implode(",", $unidad);

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
// for ($i=0; $i <cantidadF($cuenta) ; $i++) { 
//     // $stringGeneraCuentas.=$cuenta[i].", ";
// }
// $stringGeneraUnidades="";
// for ($i=0; $i <cantidadF($unidad) ; $i++) { 
//     // $stringGeneraUnidades.=$unidad[i].",";
// }
// echo "fecha: ".$fecha."<br>";
// echo $proveedoresString."<br>" ;
// echo $cuenta."<br>" ;


$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];


$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-icon">
                      <!-- <div class="float-right col-sm-2">
                        <h6 class="card-title">Exportar como:</h6>
                      </div> -->
                      <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                          Estado de Cuentas
                      </h4>
                      
                      <!-- <div class="row">
                        <div class="col-sm-6"><h5 class="card-title"><b>Unidades:</b> <small><?=$unidadAbrev?></small></h6></div>
                        <div class="col-sm-6"><h5 class="card-title"><b>Areas:</b> <small><?=$areaAbrev?></small></h6></div>
                      </div>  -->

                      <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                      <h6 class="card-title">Gestion: <?= $NombreGestion; ?></h6>                        
                      <h6 class="card-title">Cuenta: <?=$stringGeneraCuentas;?></h6>
                      <h6 class="card-title">Unidad:<?=$stringGeneraUnidades?></h6>             
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="tablePaginatorReport">
                                <thead>
                                    <tr class="">
                                        <th class="text-left">Of</th>
                                        <th class="text-left">Tipo</th>
                                        <th class="text-left">#</th>
                                        <th class="text-left">FechaComp</th>
                                        <th class="text-left">FechaEC</th>
                                        <th class="text-left">Proveedor/Cliente</th>
                                        <th class="text-left">Glosa</th>
                                        <th class="text-right">D&eacute;bito</th>
                                        <th class="text-right">Cr&eacute;dito</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($cuenta as $cuentai ) {
                                        $nombreCuenta=nameCuenta($cuentai);//nombre de cuenta    
                                        ?>
                                        <tr style="background-color:#9F81F7;">                                            
                                            <td class="text-left small" colspan="6">CUENTA</td>
                                            <td class="text-left small" colspan="4"><?=$nombreCuenta?></td>
                                        </tr> 


                                        <?php
                                         $sql="SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and (d.cod_cuenta in ($cuentai) or e.cod_cuentaaux in ($cuentai)) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' and cod_proveedor in ($proveedoresString) and e.fecha<='$fecha' and cc.cod_unidadorganizacional in ($StringUnidades) order by e.fecha";
                                        // echo $sql;
                                        $stmtUO = $dbh->prepare($sql);
                                        $stmtUO->execute();
                                        while ($row = $stmtUO->fetch()) {
                                            $codigoX=$row['codigo'];
                                            $codCompDetX=$row['cod_comprobantedetalle'];
                                            $codPlanCuentaX=$row['cod_plancuenta'];
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
                                            $nombreCuentaAuxiliarX=nameCuentaAuxiliar($codPlanCuentaAuxiliarX);
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

                                            $fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));
                                            //SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
                                            $sqlContra="SELECT sum(monto)as monto from estados_cuenta e where e.cod_comprobantedetalleorigen='$codigoX'";
                                            $stmtContra = $dbh->prepare($sqlContra);
                                            $stmtContra->execute();                                    
                                            $saldo=$montoX;//-$montoContra;                                    
                                            // echo "tipo:".$cod_tipoCuenta;
                                            if($tipo_cp==1){//proveedor
                                                $totalCredito=$totalCredito+$saldo;
                                                $nombreProveedorX=nameProveedor($codProveedor);
                                                ?>
                                                <tr class="bg-white det-estados">
                                                    <td class="text-left small"><?=$nombreUnidadO;?></td>
                                                    <td class="text-center small"><?=$nombreTipoComprobante;?></td>
                                                    <td class="text-center small"><?=$numeroComprobante;?></td>
                                                    <td class="text-left small"><?=$fechaComprobante;?></td>
                                                    <td class="text-left small"><?=$fechaX;?></td>
                                                    <td class="text-left small"><?=$nombreCuentaAuxiliarX;?>[<?=$nombreProveedorX;?>]</td>
                                                    <td class="text-left small"><?=$glosaMostrar;?></td>
                                                    <td class="text-right small"><?=formatNumberDec(0)?></td>
                                                    <td class="text-right small"><?=formatNumberDec($montoX)?></td>
                                                    <td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
                                                </tr> 

                                            <?php }else{ //cliente
                                                $nombreProveedorX=namecliente($codProveedor);
                                                $totalDebito=$totalDebito+$montoX;?>
                                                 <tr class="bg-white det-estados">
                                                    <td class="text-left small"><?=$nombreUnidadO;?></td>
                                                    <td class="text-center small"><?=$nombreTipoComprobante;?></td>
                                                    <td class="text-center small"><?=$numeroComprobante;?></td>
                                                    <td class="text-left small"><?=$fechaComprobante;?></td>
                                                    <td class="text-left small"><?=$fechaX;?></td>
                                                    <td class="text-left small"><?=$nombreCuentaAuxiliarX;?>[<?=$nombreProveedorX;?>]</td>
                                                    <td class="text-left small"><?=$glosaMostrar;?></td>
                                                    <td class="text-right small"><?=formatNumberDec($montoX)?></td>
                                                    <td class="text-right small"><?=formatNumberDec(0)?></td>
                                                    <td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
                                                </tr> 

                                            <?php }
                                            ?>  

                                                
                                            <?php

                                                $stmt_d = $dbh->prepare("SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra
                                                    from estados_cuenta e, comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
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
                                                    $glosaMostrar_d="";
                                                    if($glosaAuxiliar_d!=""){
                                                        $glosaMostrar_d=$glosaAuxiliar_d;
                                                    }else{
                                                        $glosaMostrar_d=$glosaX_d;
                                                    }
                                                    list($tipoComprobante_d, $numeroComprobante_d, $codUnidadOrganizacional_d, $mesComprobante_d, $fechaComprobante_d)=explode("|", $codigoExtra_d);
                                                    $nombreTipoComprobante_d=abrevTipoComprobante($tipoComprobante_d)."-".$mesComprobante_d;
                                                    $nombreUnidadO_d=abrevUnidad_solo($codUnidadOrganizacional_d);

                                                    $fechaComprobante_d=strftime('%d/%m/%Y',strtotime($fechaComprobante_d));
                                                     $saldo=$saldo-$montoX_d;
                                                     
                                                     
                                                     
                                                    if($tipo_cp==1){//proveedor
                                                        $nombreProveedorX_d=nameProveedor($codProveedor_d);
                                                        $totalDebito=$totalDebito+$montoX_d;?>
                                                        <tr  style="background-color:#ECCEF5;">
                                                            <td class="text-left small">&nbsp;&nbsp;&nbsp;&nbsp;<?=$nombreUnidadO_d;?></td>
                                                            <td class="text-center small"><?=$nombreTipoComprobante_d;?></td>
                                                            <td class="text-center small"><?=$numeroComprobante_d;?></td>
                                                            <td class="text-left small"><?=$fechaComprobante_d;?></td>
                                                            <td class="text-left small"><?=$fechaX_d;?></td>
                                                            <td class="text-left small"><?=$nombreProveedorX_d;?></td>  
                                                            <td class="text-left small"><?=$glosaMostrar_d;?></td>
                                                            <td class="text-right small"><?=formatNumberDec($montoX_d)?></td>
                                                            <td class="text-right small"><?=formatNumberDec(0)?></td>
                                                            <td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
                                                        </tr> 
                                                    <?php }else{ //cliente
                                                        $nombreProveedorX_d=namecliente($codProveedor_d);
                                                        if($nombreProveedorX_d=='0')$nombreProveedorX_d=nameProveedor($codProveedor_d);
                                                        $totalCredito=$totalCredito+$montoX_d;?>
                                                        <tr  style="background-color:#ECCEF5;">
                                                            <td class="text-left small">&nbsp;&nbsp;&nbsp;&nbsp;<?=$nombreUnidadO_d;?></td>
                                                            <td class="text-center small"><?=$nombreTipoComprobante_d;?></td>
                                                            <td class="text-center small"><?=$numeroComprobante_d;?></td>
                                                            <td class="text-left small"><?=$fechaComprobante_d;?></td>
                                                            <td class="text-left small"><?=$fechaX_d;?></td>
                                                            <td class="text-left small"><?=$nombreProveedorX_d;?></td>  
                                                            <td class="text-left small"><?=$glosaMostrar_d;?></td>
                                                            <td class="text-right small"><?=formatNumberDec(0)?></td>
                                                            <td class="text-right small"><?=formatNumberDec($montoX_d)?></td>
                                                            <td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
                                                        </tr> 

                                                    <?php }
                                                    ?>
                                                    
                                                    <?php
                                                }
                                                $i++;
                                                $indice++;
                                        }
                                    }
                                    $totalSaldo=$totalDebito-$totalCredito;
                                    if($totalSaldo<0){
                                        $totalSaldo=$totalSaldo*(-1);
                                    }
// <<<<<<< HEAD
                                        ?>
                                        <tr>
                                            <td class="text-right small" colspan="7">Total:</td>
                                            <td class="text-right small font-weight-bold"><?=formatNumberDec($totalDebito);?></td>
                                            <td class="text-right small font-weight-bold"><?=formatNumberDec($totalCredito);?></td>
                                            <td class="text-right small font-weight-bold"><?=formatNumberDec($totalSaldo);?></td>
                                        </tr>   
           

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

