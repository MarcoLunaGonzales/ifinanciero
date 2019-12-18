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

$fechaii=$_GET['fecha_i'];
$fechaff=$_GET['fecha_f'];
$fechai=strftime('%Y-%m-%d',strtotime($fechaii));
$fechaf=strftime('%Y-%m-%d',strtotime($fechaff));
$codPlan=$_GET['cod_cuenta'];
$codigo=$_GET['codigo'];

$detalle=obtenerDetalleSolicitudProveedor($codPlan,$fechai,$fechaf,3,$globalUser);
$centros=[];
$centros=obtenerCentroSolicitud($codigo);
$unidadSol=$centros[0];
$areaSol=$centros[1];
$idFila=1;
            while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
              $codCuentaX=$row['codigo'];
              $codDetalleX=0;
              $solicitudDetalle=obtenerSolicitudRecursosDetalleCuenta($codigo,$codCuentaX);
              $detalleX="";
              $importeX="";
              $proveedorX="";
              $retencionX="";
              $tituloImporte="Importe";
              ?><script>var nfac=[];itemFacturas.push(nfac);</script><?php
                while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                 $codDetalleX=$rowDetalles["codigo"]; 
                 $detalleX=$rowDetalles["detalle"];
                 $importeX=$rowDetalles["importe"];
                 $proveedorX=$rowDetalles["cod_proveedor"];
                 $retencionX=$rowDetalles["cod_confretencion"];
                 if($retencionX!=0){
                  $tituloImporte="Importe - ".nameRetencion($retencionX);
                 }                 
                            }
              $numeroCuentaX=trim($row['numero']);
              $nombreCuentaX=trim($row['nombre']);
              $nombrePartidaX=$row['partida'];

              include "addFila.php";
             
             $idFila=$idFila+1;
            }
            ?>
  <script>$("#cantidad_filas").val(<?=($idFila-1)?>)</script>         