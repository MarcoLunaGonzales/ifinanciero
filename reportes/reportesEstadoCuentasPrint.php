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
$proveedores=$_POST["proveedores"];
$fecha=$_POST["fecha"];

$proveedoresString=implode(",", $proveedores);

// echo "fecha: ".$fecha."<br>";
// echo $proveedoresString."<br>" ;
// echo $cuenta."<br>" ;


$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];

$sql="SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$cuenta or e.cod_cuentaaux=$cuenta) and e.cod_comprobantedetalleorigen=0 and YEAR(e.fecha)= '$NombreGestion' and cod_proveedor in ($proveedoresString) and e.fecha<='$fecha' order by e.fecha";
$stmtUO = $dbh->prepare($sql);
$stmtUO->execute();
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
                      <div class="float-right col-sm-2">
                        <h6 class="card-title">Exportar como:</h6>
                      </div>
                      <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                          Estados de Cuentas
                      </h4>

                      <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                      <h6 class="card-title">
                        Gestion: <?= $NombreGestion; ?><br>                    
                      </h6>                
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="tablePaginatorFixedEstadoCuentas">
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
                                        $montoContra=0;
                                        // while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
                                        //   $montoContra=$rowContra['monto'];
                                        // }
                                        // $debeX=$montoContra;
                                        //FIN SACAR LOS PAGOS
                                        

                                         $saldo=$montoX;//-$montoContra;
                                         $totalCredito=$totalCredito+$saldo;
                                         $nombreProveedorX=nameProveedor($codProveedor);


                                        // if(($row['cod_cuentaaux']!=""||$row['cod_cuentaaux']!=0)){
                                        //     if($tipoProveedorCliente==1){
                                        //         $proveedorX=obtenerProveedorCuentaAux($row['cod_cuentaaux']);
                                        //     }else{
                                        //         if(($row['cod_cuentaauxiliar']!=0)){
                                        //             $proveedorX=obtenerClienteCuentaAux($row['cod_cuentaauxiliar']);
                                        //         }else{
                                        //             $proveedorX="Sin Cliente";
                                        //         }       
                                        //     }
                                        // }else{
                                        //     if($tipoProveedorCliente==1){
                                        //         $proveedorX="Sin Proveedor";
                                        //     }else{
                                        //         $proveedorX="Sin Cliente";
                                        //     }
                                        // }



                                      // $stmt2 = $dbh->prepare("SELECT *
                                      //           from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
                                      //           WHERE af.cod_estadoactivofijo=1 and m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                      //            and af.cod_unidadorganizacional=:cod_unidadorganizacional and m.mes = ".$mes2." and m.gestion= ".$gestion);
                                        // Ejecutamos
                                        // $stmt2->bindParam(':cod_unidadorganizacional',$cod_unidadorganizacional);
                                        // $stmt2->execute();
                                        // //resultado
                                        // $stmt2->bindColumn('codigoactivo', $codigoactivo);
                                        // $stmt2->bindColumn('activo', $activo);
                                        ?>                            
                                            <tr class="bg-white det-estados">
                                                <td class="text-center small"><?=$nombreUnidadO;?></td>
                                                <td class="text-center small"><?=$nombreTipoComprobante;?></td>
                                                <td class="text-center small"><?=$numeroComprobante;?></td>
                                                <td class="text-left small"><?=$fechaComprobante;?></td>
                                                <td class="text-left small"><?=$fechaX;?></td>
                                                <td class="text-left small"><?=$nombreProveedorX;?></td>
                                                <td class="text-left small"><?=$glosaMostrar;?></td>
                                                <td class="text-right small"><?=formatNumberDec($montoContra)?></td>
                                                <td class="text-right small"><?=formatNumberDec($montoX)?></td>
                                                <td class="text-right small font-weight-bold"><?=formatNumberDec($saldo)?></td>
                                            </tr> 
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
                                                //SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
                                                // $sqlContra_d="SELECT sum(monto)as monto from estados_cuenta e where e.cod_comprobantedetalleorigen='$codigoX_d'";
                                                // $stmtContra_d = $dbh->prepare($sqlContra_d);
                                                // $stmtContra_d->execute();
                                                // // $montoContra_d=0;
                                                // // while ($rowContra = $stmtContra_d->fetch(PDO::FETCH_ASSOC)) {
                                                // //   $montoContra_d=$rowContra['monto'];
                                                // // }
                                                // $debeX_d=$montoContra_d;
                                                //FIN SACAR LOS PAGOS
                                                
                                                 $saldo=$saldo-$montoX_d;
                                                 $totalDebito=$totalDebito+$montoX_d;
                                                 $nombreProveedorX_d=nameProveedor($codProveedor_d);
                                                 

                                            ?>
                                            <tr class="bg-white det-estados">
                                                <td class="text-center small"><?=$nombreUnidadO_d;?></td>
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
                                        <?php
                                        }
                                        $i++;
                                        $indice++;
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-right small" colspan="7">Total:</td>
                                        <td class="text-right small font-weight-bold"><?=formatNumberDec($totalDebito);?></td>
                                        <td class="text-right small font-weight-bold"><?=formatNumberDec($totalCredito);?></td>
                                        <td class="text-right small font-weight-bold"></td>
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

