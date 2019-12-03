<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

  $json=json_decode($_GET["listRet"]);
  $idFila=$_GET['filas'];
  $filaActual=$_GET['fila_actual'];
  $importe=$_GET['debe'];
 // $areaDet=$_GET['area'];
 $nom_cuenta_auxiliar="";
 $descuentohaber=0;$descuentodebe=0;$contracuenta=0;
    //obtener datos de retenciones
    $codigoRet=$json[0];
    $stmtRetenciones = $dbh->prepare("SELECT cd.*,c.porcentaje_cuentaorigen from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigoRet order by cd.codigo");
    $stmtRetenciones->execute();
   while ($rowRet = $stmtRetenciones->fetch(PDO::FETCH_ASSOC)) {
    $cuenta=$rowRet['cod_cuenta'];
                              
    $codigoX=$rowRet['codigo'];                          
    $porcentajeX=$rowRet['porcentaje'];                         
    $glosaX=$rowRet['glosa'];
    $debehaberX=$rowRet['debe_haber'];
    $porcentajeCuentaX=($rowRet['porcentaje_cuentaorigen']/100)*$importe;

    $montoRetencion=($porcentajeX/100)*$importe;
    $idFila=$idFila+1;    
      
   if($debehaberX==1){
      $debe=$montoRetencion;
      $haber=0;
    }else{
      $debe=0;
      $haber=$montoRetencion;
    }
    if($rowRet['cod_cuenta']==0){
      //$cuentaNombre="Sin cuenta";
      $n_cuenta="";
      $nom_cuenta="";
      include "addFilaVacio.php";
      ?>         
      <script>$("#div"+<?=$idFila?>).bootstrapMaterialDesign();</script>
      <?php
    }else{
      $n_cuenta=obtieneNumeroCuenta($rowRet['cod_cuenta']);
      $nom_cuenta=nameCuenta($rowRet['cod_cuenta']);
      include "addFilaDatos.php";
      ?>         
       <script>$("#div"+<?=$idFila?>).bootstrapMaterialDesign();
        var nombreCuentaRetencion='<?=$n_cuenta?>';
        var inicioRetencion=nombreCuentaRetencion.substr(0,1);
        configuracionCentros(<?=$idFila?>,inicioRetencion);
       </script>
      <?php
    }
    
  }

  if($porcentajeCuentaX==100){
     ?><script>calcularImporteDespuesRetencion(<?=$importe?>,<?=$filaActual?>);calcularTotalesComprobante("null");</script><?php
   }else{
      ?><script>calcularImporteDespuesRetencion(<?=$porcentajeCuentaX?>,<?=$filaActual?>);calcularTotalesComprobante("null");</script><?php
   }


