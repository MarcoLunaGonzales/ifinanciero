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
$codigo=$_GET['codigo'];

$detalle=obtenerDetalleSolicitudProveedor($codPlan,$fechai,$fechaf,3,$globalUser);
$centros=[];
$centros=obtenerCentroSolicitud($codigo);
$unidadSol=$centros[0];
$areaSol=$centros[1];
$idFila=1;
$cuentasCodigos=[];$conta=0;
            while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
              $codCuentaX=$row['codigo'];
              $codSimulacionX=$row['cod_simulacion'];
              $codDetalleX=0;
              $ibnorca=obtenerIbnorcaCheck($codSimulacionX);
              $solicitudDetalle=obtenerSolicitudRecursosDetalleCuenta($codigo,$codCuentaX);
              $detalleX="";
              $importeX="";
              $proveedorX="";
              $retencionX="";
              $tituloImporte="Importe";
              if($ibnorca==1){
                $importeX=$row['monto_local'];
                $importeSolX=$row['monto_local'];
              }else{
                $importeX=$row['monto_externo'];
                $importeSolX=$row['monto_externo'];
              }
              ?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                 $cuentasCodigos[$conta]=$rowDetalles["codigo"]; 
                 $codDetalleX=$rowDetalles["codigo"]; 
                 $detalleX=$rowDetalles["detalle"];
                 $importeX=$rowDetalles["importe_presupuesto"];
                 $importeSolX=$rowDetalles["importe"];
                 $proveedorX=$rowDetalles["cod_proveedor"];
                 $retencionX=$rowDetalles["cod_confretencion"];
                 if($retencionX!=0){
                  $tituloImporte="Importe - ".nameRetencion($retencionX);
                 }                 
                            }
              $numeroCuentaX=trim($row['numero']);
              $nombreCuentaX=trim($row['nombre']);
              $nombrePartidaX=$row['simulacion'];
              $nombrePartidaDetalleX=$row['partida'];
              include "addFila.php";
             
             $idFila=$idFila+1;
            }
            $solicitudDetalle=obtenerSolicitudRecursosDetalle($codigo);
            $tituloImporte="Importe";
            while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                $codigoDetX=$rowDetalles["codigo"];
                $encontrar=0;
              for ($i=0; $i < count($cuentasCodigos); $i++) { 
                if($codigoDetX==$cuentasCodigos[$i]){
                  $encontrar++;
                 }   
              }
             if($encontrar==0){
                 $codDetalleX=$rowDetalles["codigo"]; 
                 $detalleX=$rowDetalles["detalle"];
                 $importeX=$rowDetalles["importe_presupuesto"];
                 $importeSolX=$rowDetalles["importe"];
                 $proveedorX=$rowDetalles["cod_proveedor"];
                 $retencionX=$rowDetalles["cod_confretencion"];
                 if($retencionX!=0){
                  $tituloImporte="Importe - ".nameRetencion($retencionX);
                 }
                 $numeroCuentaX=trim($rowDetalles['numero']);
                 $nombreCuentaX=trim($rowDetalles['nombre']);
                 $nombrePartidaX="SIMULACION";
                 $nombrePartidaDetalleX="Cuenta";
                 include "addFila.php";                  
                   $idFila=$idFila+1;
             } 
            }
            ?>
  <script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>         