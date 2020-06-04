<?php
session_start();
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalUser=$_SESSION["globalUser"];

$porcionesFechaDesde = explode("/", $_GET["fecha_i"]);
$porcionesFechaHasta = explode("/", $_GET["fecha_f"]);

$fechai=$porcionesFechaDesde[2]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[0];
$fechaf=$porcionesFechaHasta[2]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[0];

$codPlan=$_GET['cod_cuenta'];
$tipoSolicitud=2;
if(isset($_GET['tipo'])){
  $tipoProveedor=$_GET['tipo'];
  if($tipoProveedor==1){
   $detalle=obtenerDetalleSolicitudProveedorPlantillaSec($codPlan,$fechai,$fechaf,$globalUser);
$idFila=1;
$ibnorca=1;
?><script>numFilas=0;cantidadItems=0;itemFacturas=[];</script><?php
$cuentasCodigos=[];$conta=0;$auxAnio=-1;$detalleAux="";$contAux=0;$totalImportePres=0;$totalImporteSol=0;
           while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
            $areaXX=$row['cod_area'];
              $unidadXX=$row['cod_unidadorganizacional'];
                      $cod_plantilladetalle=$row['codigo_detalle'];
                      $cod_plantillauditor="";
                      $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla(false,$cod_plantilladetalle);

                      $codCuentaX=$row['codigo'];
                      $codDetalleX=0;
                      $detalleX=$row['glosa'];
                      $detalleXX=$row['glosa'];
                      $proveedorX="";
                      $retencionX="";
                      $codTipoPago=0;
                      $codCuentaBancaria=0;
                      $nombreBen="";
                      $apellidoBen="";
                      $cuentaBen="";
                      $tituloImporte="Importe";
                      if($ibnorca==1){
                              $importeX=$row['monto_total'];
                              $importeSolX=$row['monto_total'];
                      }else{
                              $importeX=$row['monto_externo'];
                              $importeSolX=$row['monto_externo'];
                      }
                     //
                      $tituloAnio="";
                     /*if($row['cod_anio']<=1&&$areaXX==38){
                       $tituloAnio="Año 1 - Et ".($row['cod_anio']+1);
                     }*/
                    $nombrePartidaX=$row['nombre_simulacion']." <b class='text-warning'>".$row['partida']."</b>";
                    $nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b> - <b class='text-dark'>".$tituloAnio."</b>";                            
              
                    $entro=0;
                    while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                          $entro=1;              
                    }
                    $numeroCuentaX=trim($row['numero']);
                    $nombreCuentaX=trim($row['nombre']);              
              
                    if($entro==0){
                            ?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                            include "addFila.php";
                            $idFila=$idFila+1;
                    }                                                          
            }
  }else{
    $detalle=obtenerDetalleSolicitudProveedorPlantillaTCPTCS($codPlan,$fechai,$fechaf,$globalUser);
$idFila=1;
$ibnorca=1;
?><script>numFilas=0;cantidadItems=0;itemFacturas=[];</script><?php
$cuentasCodigos=[];$conta=0;$auxAnio=-1;$detalleAux="";$contAux=0;$totalImportePres=0;$totalImporteSol=0;
            while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
              $areaXX=$row['cod_area'];
              $unidadXX=$row['cod_unidadorganizacional'];
              $codigo_fila=explode("###",$row['codigo_detalle']);
                     if($codigo_fila[1]=="DET-SIM"){
                          $cod_plantilladetalle=$codigo_fila[0];
                          $cod_plantillauditor="";
                          $solicitudDetalle=obtenerSolicitudRecursosDetallePlantilla(false,$cod_plantilladetalle);
                      }else{
                          $cod_plantilladetalle="";
                          $cod_plantillauditor=$codigo_fila[0];
                          $solicitudDetalle=obtenerSolicitudRecursosDetallePlantillaAud(false,$cod_plantillauditor);
                      }
                      $codCuentaX=$row['codigo'];
                      $codDetalleX=0;
                      $detalleX=$row['glosa_completa'];
                      $detalleXX=$row['glosa'];
                      $proveedorX="";
                      $retencionX="";
                      $codTipoPago=0;
                      $codCuentaBancaria=0;
                      $nombreBen="";
                      $apellidoBen="";
                      $cuentaBen="";
                      $tituloImporte="Importe";
                      if($ibnorca==1){
                              $importeX=$row['monto_total'];
                              $importeSolX=$row['monto_total'];
                      }else{
                              $importeX=$row['monto_externo'];
                              $importeSolX=$row['monto_externo'];
                      }
                     $tituloAnio="Año ".$row['cod_anio'];
                     if($row['cod_anio']<=1&&$areaXX==38){
                       $tituloAnio="Año 1 - Et ".($row['cod_anio']+1);
                     }
                    $nombrePartidaX=abrevArea_solo($areaXX)."-".$row['nombre_simulacion']." <b class='text-warning'>".$row['partida']."</b>";
                    $nombrePartidaDetalleX="<b class='text-warning'>Cuenta</b> - <b class='text-dark'>".$tituloAnio."</b>";                            
              
                    $entro=0;
                    while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                          $entro=1;              
                    }
                    $numeroCuentaX=trim($row['numero']);
                    $nombreCuentaX=trim($row['nombre']);              
              
                    if($entro==0){
                            ?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                            include "addFila.php";
                            $idFila=$idFila+1;
                    }  
            } 
  }//fin de else
}

            ?>
  <script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>         