<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

session_start();
if(!isset($_GET['sol'])){
    header("location:listSolicitudRecursos.php");
}else{
    $codigo=$_GET['sol'];
    $moneda=2;
    $abrevMon=abrevMoneda($moneda);
    $nombreMonedaG=nameMoneda($moneda);
}

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
// Preparamos
$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_solicitud, u.abreviatura as unidad,u.nombre as nombre_unidad,a.abreviatura as area 
        from solicitud_recursos p,unidades_organizacionales u, areas a,estados_solicitudrecursos e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadosolicitudrecurso and p.codigo='$codigo' order by codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
            $stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('cod_personal', $codPersonalX);
            $stmt->bindColumn('fecha', $fechaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('nombre_unidad', $unidadNombreX);
            $stmt->bindColumn('estado_solicitud', $estadoX);
            $stmt->bindColumn('cod_estadosolicitudrecurso', $codEstadoX);
            $stmt->bindColumn('numero', $numeroX);
            $stmt->bindColumn('cod_simulacion', $codSimulacionX);
            $stmt->bindColumn('cod_simulacionservicio', $codSimulacionServicioX);
            $stmt->bindColumn('cod_proveedor', $codProveedorX);
            $stmt->bindColumn('idServicio', $idServicioX);
            $stmt->bindColumn('observaciones', $observacionesX);

while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {

    $nombreCliente=obtenerNombreClienteSimulacion($codSimulacionServicioX);

    $userEnvio=obtenerPersonaCambioEstado(2708,$codigoX,2722);
    if($userEnvio==0){
       $nombreEnviado="Sin registro";    
    }else{
       $nombreEnviado=namePersonal($userEnvio);    
    }

    $userAprobado=obtenerPersonaCambioEstado(2708,$codigoX,2723);
    if($userAprobado==0){
       $nombreAprobado="Sin registro";    
    }else{
       $nombreAprobado=namePersonal($userAprobado);    
    }
    
    $fechaC=$fechaX;
    $unidadC=$unidadNombreX;
    $codUC=$codUnidadX;
    $monedaC="Bs";
    $codMC=$moneda;
    $numeroC=$numeroX;
    $solicitante=namePersonal($codPersonalX);
    $codigoServicio="SIN SERVICIO";
            $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicioX";
            $stmt1=$dbh->prepare($sql);
            $stmt1->execute();
            while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
              $codigoServicio=$row1['codigo'];
              if($codigoServicio==""){
                $codigoServicio="SIN CODIGO";
              }
            }
    $observacionesC=$observacionesX;                
    if($observacionesX==""){
      $observacionesC="NINGUNO";  
    }            
}
//INICIAR valores de las sumas
$tDebeDol=0;$tHaberDol=0;$tDebeBol=0;$tHaberBol=0;

// Llamamos a la funcion para obtener el detalle de la solicitud

$data = obtenerSolicitudRecursosDetalle($codigo);
$tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaC)));
if($tc==0){$tc=1;}
$fechaActual=date("Y-m-d");
$tituloImporte="";
/*                        archivo HTML                      */

?>
<!-- formato cabeza fija para pdf-->
<html><head>
    <link href="../assets/libraries/plantillaPDFSolicitudesRecursos.css" rel="stylesheet" />
   </head><body>
<!-- fin formato cabeza fija para pdf--> 

<!--CONTENIDO-->
     <table class="table">
         <tr>
            <td class="s1 text-center" colspan="4">INSTITUTO BOLIVIANO DE NORMALIZACION Y CALIDAD</td>
            <td rowspan="3" class="text-center imagen-td"><img src="../assets/img/ibnorca_sol.png" alt="Ibnorca" width="100" height="90"></td>
        </tr>
        <tr>
            <td class="s2 text-center" colspan="4">REGISTRO</td>
        </tr>
        <tr>
            <td class="s2 text-center" colspan="4">SOLICITUD DE RECURSOS</td>
        </tr>
        <tr>
            <td class="s3 text-left bg-celeste" width="18%">Ciudad y Fecha:</td>
            <td class="s3 text-left" width="20%"><?=$unidadC?></td>
            <td class="s3 text-left" width="39%"><?=strftime('%d/ %m/ %Y',strtotime($fechaC))?></td>
            <td class="s3 text-left bg-celeste">N&uacute;mero:</td>
            <td class="s3 text-left" width="18%"><?=generarNumeroCeros(6,$numeroC)?></td>
        </tr>
        <tr>
            <td class="s3 text-left bg-celeste">Solicitante:</td>
            <td class="s3 text-left" colspan="2"><?=$solicitante?></td>
            <td class="s3 text-left bg-celeste">T/C</td>
            <td class="s3 text-left"><?=$tc?></td>
        </tr>
        <tr>
            <td class="s3 text-left bg-celeste">C&oacute;digo de Servicio:</td>
            <td class="s3 text-left" colspan="2"><?=$codigoServicio?></td>
            <td class="s3 text-left bg-celeste">Estado:</td>
            <td class="s3 text-left"><?=$estadoX?></td>
        </tr>
     </table>
      <table class="table">
        <tr class="bg-celeste">
            <td class="s3 text-center" rowspan="2">N°</td>
            <td class="s3 text-center" colspan="2">Seguimiento Presupuestal</td>
            <td class="s3 text-center" rowspan="2">C. C. (Area)</td>
            <td class="s3 text-center" rowspan="2">N° Factura</td>
            <td class="s3 text-center" rowspan="2">Descripci&oacute;n</td>
            <td class="s3 text-center">Importe</td>
            <td class="s3 text-center">Ret</td>
            <td class="s3 text-center">T. Ret</td>
            <td class="s3 text-center">Sub Total</td>
        </tr>
        <tr class="bg-celeste">
            <td class="s3 text-center">BOB</td>
            <td class="s3 text-center">%</td>
            <td class="s3 text-center">BOB</td>
            <td class="s3 text-center">BOB</td>
            <td class="s3 text-center"></td>
            <td class="s3 text-center">BOB</td>
        </tr>
        <?php
        $index=1;$totalImporte=0;$totalImportePres=0;
        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {

            $facturas=obtenerFacturasSoli($row['codigo']);
            $numeroFac="";
            while ($rowFac = $facturas->fetch(PDO::FETCH_ASSOC)) {
                $numeroFac=$rowFac['nro_factura'];          
            }
            $codCuentaX=$row['cod_plancuenta'];
            $codAreaXX=$row['cod_area'];
            $nombreArea=abrevArea_solo($codAreaXX);
            $detalleX=$row["detalle"];
            $importeX=$row["importe_presupuesto"];
            $importeSolX=$row["importe"];
            $proveedorX=nameProveedor($row["cod_proveedor"]);
            $retencionX=$row["cod_confretencion"];
            $totalImportePres+=$importeX;
            $totalImporte+=$importeSolX;

            if($importeX!=0){
             $importePorcent=($importeSolX*100)/$importeX;   
            }else{
             $importePorcent=0;
            }      
            if($retencionX!=0){
              $tituloImporte=abrevRetencion($retencionX);
              $porcentajeRetencion=porcentRetencion($retencionX);
              $montoImporte=$importeSolX*($porcentajeRetencion/100);
              $montoImporteRes=$importeSolX-$montoImporte;     
            }else{
             $tituloImporte="Ninguno";
             $montoImporte=0;
             $montoImporteRes=0; 
            }
            
            $numeroCuentaX=trim($row['numero']);
            $nombreCuentaX=trim($row['nombre']);
        ?>
        <tr>
            <td class="s3 text-center" width="4%"><?=$index?></td>
            <td class="s3 text-center"><?=number_format($importeX, 2, '.', '')?></td>
            <td class="s3 text-center"><?=number_format($importePorcent, 2, '.', '')?></td>
            <td class="s3 text-center" width="8%"><?=$nombreArea?></td>
            <td class="s3 text-center" width="8%"><?=$numeroFac?></td>
            <td class="s3 text-left" width="40%"><?="".$nombreCliente." F/".$numeroFac." ".$proveedorX." ".$detalleX?></td>
            <td class="s3 text-right"><?=number_format($montoImporte, 2, '.', '')?></td>
            <td class="s3 text-right"><?=number_format($montoImporteRes, 2, '.', '')?></td>
            <td class="s3 text-right"><?=$tituloImporte?></td>
            <td class="s3 text-right"><?=number_format($importeSolX, 2, '.', '')?></td>
        </tr> 
        <?php  
        $index++; 
        }
        ?>
        <!--<tr>
            <td class="s3 text-center" rowspan="2" colspan="3"></td>
            <td class="s3 text-right bg-celeste" colspan="3">Sub Total (BOB)</td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"><?=number_format($totalImporte, 2, '.', '')?></td>
        </tr>
        <tr>
            <td class="s3 text-right bg-celeste" colspan="2">Retenci&oacute;n de Impuestos</td>
            <td class="s3 text-center"><?=$tituloImporte?></td>
            <td class="s3 text-right"></td>
        </tr>-->
        <tr>
            <td class="s3 text-center" colspan="3"></td>
            <td class="s3 text-right bg-celeste" colspan="3">TOTAL (BOB)</td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"><?=number_format($totalImporte, 2, '.', '')?></td>
        </tr>
        <tr>
            <td class="s3 text-center" colspan="3">V°B° P-SA/P-DNAF</td>
            <td class="s3 text-right bg-celeste" colspan="3">TOTAL (USD)</td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"><?=number_format($totalImporte/6.96, 2, '.', '')?></td>
        </tr>
     </table>
     <table class="table">
        <tr class="bg-celeste">
            <td class="s3 text-center">OBSERVACIONES</td>
        </tr>
        <tr>
            <td class="s3 text-left"><?=$observacionesC?></td>
        </tr>
     </table>
     <table class="table">
        <tr>
            <td class="s3 text-center" height="80px"></td>
            <td class="s3 text-center" height="80px"></td>
            <td class="s3 text-center" height="80px"></td>
            <td class="s3 text-center" height="80px"></td>
        </tr>
        <tr>
            <td class="s3 text-center">Solicitante <?=$solicitante?></td>
            <td class="s3 text-center">Autorización <?=$nombreEnviado?></td>
            <td class="s3 text-center">Autorización <?=$nombreAprobado?></td>
            <td class="s3 text-center">Autorización <?=$nombreAprobado?></td>
        </tr>
        <tr>
            <td class="s3 text-left">Fecha:</td>
            <td class="s3 text-left">Fecha:</td>
            <td class="s3 text-left">Fecha:</td>
            <td class="s3 text-left">Fecha:</td>
        </tr>
     </table>

<!-- PIE DE PAGINA-->     
     <footer class="footer">
        <table class="table">
          <tr>
            <td class="s4 text-left" width="25%">IBNORCA</td>
            <td class="s4 text-left" width="25%">Codigo: REG-PRE-SA-04-01.05</td>
            <td class="s4 text-left" width="25%">V: 2015-09-21</td>
            <td class="s4 text-left" width="25%"></td>
          </tr>
       </table>
     </footer>


<!-- FIN CONTENIDO-->

<!-- formato pie fijo para pdf-->  
</body></html>
<!-- fin formato pie fijo para pdf-->