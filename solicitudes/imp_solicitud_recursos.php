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
    
    $distribucionGlosa=obtenerResumenDistribucionSR($codigoX);
    
    $nombreCliente=obtenerNombreClienteSimulacion($codSimulacionServicioX);

    //
    $IdTipo=obtenerTipoServicioPorIdServicio($idServicioX);
    $codObjeto=obtenerCodigoObjetoServicioPorIdSimulacion($codSimulacionServicioX);

    $datosServicio=obtenerServiciosTipoObjetoNombre($codObjeto)." - ".obtenerServiciosClaServicioTipoNombre($IdTipo);


          $objeto_sol=2708;

          $nombreEstado_registro=obtenerNombreEstadoSol(1);
          $personal_registro=namePersonal($codPersonalX);
          $fecha_registro=obtenerFechaCambioEstado($objeto_sol,$codigoX,2721);//estado registro

          
          $userRevisado=obtenerPersonaCambioEstado($objeto_sol,$codigoX,2722);  //autorizado       
          $nombreEstado_revisado=obtenerNombreEstadoSol(4);
          if($userRevisado==0){
             $fecha_revisado="";    
             $personal_revisado="";    
          }else{
             $personal_revisado=namePersonal($userRevisado);
             $fecha_revisado=obtenerFechaCambioEstado($objeto_sol,$codigoX,2722);
          }

          $userAprobado=obtenerPersonaCambioEstado($objeto_sol,$codigoX,2723);
          $nombreEstado_aprobacion=obtenerNombreEstadoSol(3);
          if($userAprobado==0){
             $fecha_aprobacion="";    
             $personal_aprobacion="";    
          }else{
             $personal_aprobacion=namePersonal($userAprobado);
             $fecha_aprobacion=obtenerFechaCambioEstado($objeto_sol,$codigoX,2723);
          }

          $userprocesado=obtenerPersonaCambioEstado($objeto_sol,$codigoX,2725);//contabiliado        
          $nombreEstado_procesado=obtenerNombreEstadoSol(5);
          if($userprocesado==0){
             $personal_procesado="";    
             $fecha_procesado="";
          }else{
             $personal_procesado=namePersonal($userprocesado);    
             $fecha_procesado=obtenerFechaCambioEstado($objeto_sol,$codigoX,2725);
          }

          $userRevision=obtenerPersonaCambioEstado($objeto_sol,$codigoX,2822);//enviado autorizacion        
          $nombreEstado_revision=obtenerNombreEstadoSol(6);
          if($userRevision==0){
             $personal_revision="";    
             $fecha_revision="";
          }else{
             $personal_revision=namePersonal($userRevision);    
             $fecha_revision=obtenerFechaCambioEstado($objeto_sol,$codigoX,2822);
          }

          $userSIS=obtenerPersonaCambioEstado($objeto_sol,$codigoX,3107);//procesado        
          $nombreEstado_SIS=obtenerNombreEstadoSol(7);
          if($userSIS==0){
             $personal_SIS="";    
             $fecha_SIS="";
          }else{
             $personal_SIS=namePersonal($userSIS);    
             $fecha_SIS=obtenerFechaCambioEstado($objeto_sol,$codigoX,3107);
          }

    
    $fechaC=$fechaX;
    $unidadC=$unidadNombreX;
    $codUC=$codUnidadX;
    $monedaC="Bs";
    $codMC=$moneda;
    $numeroC=$numeroX;
    $solicitante=namePersonal($codPersonalX);
    $codigoServicio="-";
            $sql="SELECT codigo FROM ibnorca.servicios where idServicio=$idServicioX";
            $stmt1=$dbh->prepare($sql);
            $stmt1->execute();
            while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
              $codigoServicio=$row1['codigo'];
              if($codigoServicio==""){
                $codigoServicio="-";
              }
            }
    $observacionesC=$observacionesX;                
    if($observacionesX==""){
      $observacionesC="";  
    }
}
//INICIAR valores de las sumas
$tDebeDol=0;$tHaberDol=0;$tDebeBol=0;$tHaberBol=0;

// Llamamos a la funcion para obtener el detalle de la solicitud

$data = obtenerSolicitudRecursosDetalle($codigo);
$tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaC)));

$anioSol=strftime('%Y',strtotime($fechaC));
$mesSol=strftime('%m',strtotime($fechaC));
if($tc==0){$tc=1;}
$fechaActual=date("Y-m-d");
$tituloImporte="";
/*                        archivo HTML                      */

?>
<!-- formato cabeza fija para pdf-->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="../assets/libraries/plantillaPDFSolicitudesRecursos.css" rel="stylesheet" />
   </head><body>
<!-- fin formato cabeza fija para pdf--> 

<!--CONTENIDO-->
     <table class="table">
         <tr>
            <td class="s1 text-center" colspan="4">INSTITUTO BOLIVIANO DE NORMALIZACION Y CALIDAD</td>
            <td rowspan="3" class="text-center imagen-td"><img class="imagen-logo-izq_2" src="../assets/img/logo_ibnorca_origen_3.jpg" width="90" height="90"></td>
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
        $segPres=0;$porcentSegPres=0;
        $tipoPago="";$beneficiarios="";
        while ($row = $data->fetch(PDO::FETCH_ASSOC)) {

            $facturas=obtenerFacturasSoli($row['codigo']);
            $numeroFac="";
            while ($rowFac = $facturas->fetch(PDO::FETCH_ASSOC)) {
                $numeroFac=$rowFac['nro_factura'];          
            }
            $codCuentaX=$row['cod_plancuenta'];
            $codAreaXX=$row['cod_area'];
            $codOficinaXX=$row['cod_unidadorganizacional'];
            $nombreArea=abrevArea_solo($codAreaXX);
            $detalleX=$row["detalle"];
            $importeX=$row["importe_presupuesto"];
            $importeSolX=$row["importe"];
            $proveedorX=nameProveedor($row["cod_proveedor"]);
            $retencionX=$row["cod_confretencion"];
            $codCuentaBancariaX=$row["cod_cuentabancaria"];

            

            if($importeX!=0){
             $importePorcent=($importeSolX*100)/$importeX;   
            }else{
             $importePorcent=0;
            }      
            if($retencionX!=0){
              $tituloImporte=abrevRetencion($retencionX);
              $porcentajeRetencion=100-porcentRetencionSolicitud($retencionX);
              $montoImporte=$importeSolX*($porcentajeRetencion/100);       
              if(($retencionX==8)||($retencionX==10)){ //validacion del descuento por retencion
                $montoImporte=$importeSolX;
              }
              $montoImporteRes=$importeSolX-$montoImporte;
            }else{
             $tituloImporte="Ninguno";
             $montoImporte=$importeSolX;
             $montoImporteRes=0; 
            }
            
            $numeroCuentaX=trim($row['numero']);
            $nombreCuentaX=trim($row['nombre']);
            $datosSeg=obtenerPresupuestoEjecucionDelServicio($codOficinaXX,$codAreaXX,$anioSol,(int)$mesSol,$numeroCuentaX);
            
            if($datosSeg->presupuesto!=null||$datosSeg->presupuesto!=0){
               $segPres=$datosSeg->presupuesto;
               $porcentSegPres=($datosSeg->ejecutado*100)/$datosSeg->presupuesto; 
            }
            $datosBen[$index-1]=trim($row["nombre_beneficiario"]);
            if($row["apellido_beneficiario"]==""){
              $datosBen[$index-1]=trim($row["nombre_beneficiario"])." ".trim($row["apellido_beneficiario"]);
            }
            $datosBen[$index-1] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $datosBen[$index-1]);
            
            if($row["cod_tipopagoproveedor"]==2){ //transferencia
              $datosBen[$index-1].=" / ".obtenerBancoBeneficiarioSolicitudRecursos($codCuentaBancariaX,$row["cod_proveedor"]).", Nro. Cuenta:"." ".trim($row["nro_cuenta_beneficiario"])."";
            }

            $datosTipo[$index-1]=nameTipoPago($row["cod_tipopagoproveedor"]);

            $codActividadX=$row["cod_actividadproyecto"];
            $tituloActividad="";
            //$tituloActividad=obtenerCodigoActividadesServicioImonitoreo($codActividadX);   
            $detalleActividadFila="";
            if(obtenerNombreDirectoActividadServicio($codActividadX)[0]!=""){
              $detalleActividadFila.="- Actividad: ".obtenerNombreDirectoActividadServicio($codActividadX)[0]."";
            }
            $codAccNum=$row["acc_num"]; 
            if(obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]!=""){
              $detalleActividadFila.="- Acc Num: ".obtenerNombreDirectoActividadServicioAccNum($codAccNum)[0]."";
            }

            if(trim($datosServicio)=="-"){
              $datosServicio="";  
            }else{
              $datosServicio="- ".$datosServicio;
            }
            

            $totalImportePres+=$importeX;
            $totalImporte+=$montoImporte;
        ?>
        <tr>
            <td class="s3 text-center" width="4%"><?=$index?></td>
            <td class="s3 text-center"><?=number_format($segPres, 0, '.', ',')?></td>
            <td class="s3 text-center"><?=number_format($porcentSegPres, 0, '.', '')?></td>
            <td class="s3 text-center" width="8%"><?=$nombreArea?></td>
            <td class="s3 text-center" width="8%"><?=$numeroFac?></td>
            <td class="s3 text-left" width="40%"><?="Beneficiario: ".$proveedorX." ".str_replace("-", "", $detalleX)." ".$datosServicio." ".$nombreCliente." ".$detalleActividadFila?></td> <!-- F/".$numeroFac."-->
            <td class="s3 text-right"><?=number_format($importeSolX, 2, '.', ',')?></td>
            <td class="s3 text-right"><?=number_format($montoImporteRes, 2, '.', ',')?></td>
            <td class="s3 text-right"><?=$tituloImporte?></td>
            <td class="s3 text-right"><?=number_format($montoImporte, 2, '.', ',')?></td>
        </tr> 
        <?php  
        $index++; 
        }

        //quitar valores repetidos y mostrar
        $beneficiarios=implode(",", array_unique($datosBen));
        $tipoPago=implode(",", array_unique($datosTipo));
        
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
            <td class="s3 text-center" colspan="3"></td>
            <td class="s3 text-right bg-celeste" colspan="3">TOTAL (USD)</td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"></td>
            <td class="s3 text-right"><?=number_format($totalImporte/6.96, 2, '.', '')?></td>
        </tr>
     </table>
     <table class="table">
        <tr class="bg-celeste">
            <td class="s3 text-center" colspan="2">FORMA DE PAGO</td>
        </tr>
        <tr>
            <td class="s3 text-left" width="30%"><?=strtoupper($tipoPago)?></td>
            <td class="s3 text-left"><?=strtoupper($beneficiarios)?></td>
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
     <?php 
   if($distribucionGlosa!=""){
    ?> 
    <table class="table">
        <tr class="bg-celeste">
            <td class="s3 text-center">DISTRIBUCIÓN</td>
        </tr>
        <tr>
            <td class="s3 text-left"><?=$distribucionGlosa?></td>
        </tr>
     </table>
<?php     
   }
  ?>
  <table class="table">
        <tr>
            <td class="s3 text-center"><b>Estado</b></td>
            <td class="s3 text-center"><b>Personal</b></td>
            <td class="s3 text-center"><b>Fecha</b></td>
        </tr>
       <tr>
                <td class="s3 text-left"><?=$nombreEstado_registro?>: </td><td class="s3 text-center"> <?=$personal_registro?></td><td class="s3 text-center"><?=$fecha_registro?></td>
              </tr>
              <tr>
                <td class="s3 text-left"><?=$nombreEstado_revision?>: </td><td class="s3 text-center"><?=$personal_registro?></td><td class="s3 text-center"><?=$fecha_revision?></td>
              </tr>
              <tr>
                <td class="s3 text-left"><?=$nombreEstado_SIS?>: </td><td class="s3 text-center"><?=$personal_SIS?></td><td class="s3 text-center"><?=$fecha_SIS?></td>
              </tr>            
              <tr>
                <td class="s3 text-left"><?=$nombreEstado_revisado?>: </td><td class="s3 text-center"><?=$personal_revisado?></td><td class="s3 text-center"><?=$fecha_revisado?></td>
              </tr>
              <tr>
                <td class="s3 text-left"><?=$nombreEstado_aprobacion?>: </td><td class="s3 text-center"><?=$personal_aprobacion?></td><td class="s3 text-center"><?=$fecha_aprobacion?></td>
              </tr>
              <tr>
                <td class="s3 text-left"><?=$nombreEstado_procesado?>: </td><td class="s3 text-center"><?=$personal_procesado?></td><td class="s3 text-center"><?=$fecha_procesado?></td>
              </tr>
              
     </table>
     <!--<table class="table">
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
     </table>-->

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