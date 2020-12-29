<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codDepreciacion=$codigo;


//SE DEBE PARAMETRIZAR ESTE CODIGO DE CUENTA PARA LA DEPRECIACION
$codCuentaDepreciacion=298;
$codCuentaDepreciacionAF=256;

$sqlMes="SELECT m.mes from mesdepreciaciones m where m.codigo='$codDepreciacion'";
$stmtMes = $dbh->prepare($sqlMes);
$stmtMes -> execute();
$codMes=0;
while ($rowMes = $stmtMes->fetch(PDO::FETCH_ASSOC)) {
  $codMes=$rowMes['mes'];
}

//PARTE QUE REALIZA UN COMPROBANTE POR UNIDAD ORG.
$sqlUnidades="SELECT a.cod_unidadorganizacional, 
(select u.abreviatura from unidades_organizacionales u where u.codigo=a.cod_unidadorganizacional)as nombreunidad,m.mes, m.gestion
from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos a
where m.codigo=md.cod_mesdepreciaciones and md.cod_activosfijos=a.codigo and m.codigo='$codDepreciacion' 
group by a.cod_unidadorganizacional, nombreunidad ORDER BY 2";

//echo $sqlUnidades;

$stmtUnidades = $dbh->prepare($sqlUnidades);
$stmtUnidades->execute();
$stmtUnidades->bindColumn('cod_unidadorganizacional', $codUnidadCabecera);
$stmtUnidades->bindColumn('nombreunidad', $nombreUnidadCabecera);
$stmtUnidades->bindColumn('mes', $mesCabecera);
$stmtUnidades->bindColumn('gestion', $gestionCabecera);

while ($rowUnidades = $stmtUnidades->fetch(PDO::FETCH_BOUND)) {
      $sql="SELECT a.cod_depreciaciones,
      (select r.nombre from depreciaciones r where r.codigo=a.cod_depreciaciones)as nombrerubro,
      sum(md.d5_incrementoporcentual)as actualizacionanterior, sum(md.d7_incrementodepreciacionacumulada)as actualizaciondepreciacion, sum(d8_depreciacionperiodo)as depreciacionperiodo 
      from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos a
      where m.codigo=md.cod_mesdepreciaciones and md.cod_activosfijos=a.codigo and m.codigo='$codDepreciacion' and a.cod_unidadorganizacional='$codUnidadCabecera'
      group by m.mes, m.gestion, a.cod_unidadorganizacional, a.cod_depreciaciones, nombrerubro order by 2";  
      
      //echo $sql;

      $stmt = $dbh->prepare($sql);
      $stmt->execute();

      // $stmt->bindColumn('mes', $codMes);
      // $stmt->bindColumn('gestion', $codAnio);
      // $stmt->bindColumn('cod_unidadorganizacional', $codUnidadO);
      // $stmt->bindColumn('nombreunidad', $nombreUnidad);
      $stmt->bindColumn('cod_depreciaciones', $codRubro);
      $stmt->bindColumn('nombrerubro', $nombreRubro);
      $stmt->bindColumn('actualizacionanterior', $valorActualizacionAnterior);
      $stmt->bindColumn('actualizaciondepreciacion', $valorActualizacionDepreciacion);
      $stmt->bindColumn('depreciacionperiodo', $valorDepreciacionPeriodo);


      //aca insertamos la cabecera del comprobante
      // $globalUnidadX=$_SESSION["globalUnidad"];

      $globalUnidadX=829;//se contabilizara en la DN 

      $codAreaSA="502";//area oara todas las oficinas
      $tipoComprobante=3;
      $codEmpresa=1;
      // $codAnio=$_SESSION["globalNombreGestion"];      
      //$fechaActual=date("Y-m-d H:i:s");      
      $horaActual=date("H:i:s");
      $fechaContabilizacion=$gestionCabecera."-".$mesCabecera."-01";
      $fecha_x = date("Y-m-t", strtotime($fechaContabilizacion));//ultimo dia de la fecha
      $fecha_contabilizacion = $fecha_x." ".$horaActual;

      $codMoneda=1;
      $codEstadoComprobante=1;
      //#C3b0ll1t4sG4n4d0r4s#F1#1234 ->f4c3


      // $mesTrabajo=$_SESSION['globalMes'];
      // $gestionTrabajo=$_SESSION['globalNombreGestion'];
      $mesTrabajo=$mesCabecera;
      $gestionTrabajo=$gestionCabecera;

      $numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $codUnidadCabecera, $gestionTrabajo, $mesTrabajo);

      $glosaCabecera="Actualizacion y Depreciacion de Activos Fijos Mes: ".$mesTrabajo." ".$gestionTrabajo." Unidad: ".$nombreUnidadCabecera;
      $codComprobante=obtenerCodigoComprobante();
      //insertamos cabecera
      $sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) values ('$codComprobante','$codEmpresa','$globalUnidadX','$gestionTrabajo','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fecha_contabilizacion','$numeroComprobante','$glosaCabecera')";
      $stmtInsertCab = $dbh->prepare($sqlInsertCab);
      $flagSuccess=$stmtInsertCab->execute();
      //INSERTAMOS LA RELACION ENTRE COMPROBANTES Y DEPRECIACIONES.
      $sqlInsertCabRelacion="INSERT INTO comprobantes_depreciaciones (cod_comprobante, cod_depreciacion) values ('$codComprobante','$codDepreciacion')";
      $stmtInsertCabRelacion = $dbh->prepare($sqlInsertCabRelacion);
      $flagSuccessRelacion=$stmtInsertCabRelacion->execute();
      $ordenComprobanteDetalle=1;
      while ($row = $stmt->fetch(PDO::FETCH_BOUND)){
         //obtenemos la cuenta del rubro
         $sqlPC="SELECT p.codigo, p.nombre, p.numero from depreciaciones r, plan_cuentas p 
            where r.cod_cuentacontable=p.codigo and r.codigo='$codRubro'";
         $stmtPC=$dbh->prepare($sqlPC);
         $stmtPC->execute();
         while ($rowPC = $stmtPC->fetch(PDO::FETCH_ASSOC)) {
            $codigoCuenta=$rowPC['codigo'];
            $nombreCuenta=$rowPC['nombre'];
            $numeroCuenta=$rowPC['numero'];
            $numeroCuenta=trim($numeroCuenta);
            
            $sqlCuentas="SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.cod_padre like '%$numeroCuenta%' order by 1";
            $stmtCuentas=$dbh->prepare($sqlCuentas);
            $stmtCuentas->execute();
            $indice=1;
            $codCuentaReg1=$codigoCuenta;
            $codCuentaReg2=$codigoCuenta;
            while($rowCuentas = $stmtCuentas->fetch(PDO::FETCH_ASSOC)){
               if($indice==1){
                  $codCuentaReg1=$rowCuentas["codigo"];
               }
               if($indice==2){
                  $codCuentaReg2=$rowCuentas["codigo"];
               }
               $indice++;
            }
            
            $glosaDetalle1="Actualización Valor Anterior ".$nombreUnidadCabecera." ".$nombreRubro." ".$mesTrabajo."/".$gestionTrabajo;
            $glosaDetalle2="Actualización Dep. Acumulada ".$nombreUnidadCabecera." ".$nombreRubro." ".$mesTrabajo."/".$gestionTrabajo;
            $glosaDetalle3="Depreciación Periodo ".$nombreUnidadCabecera." ".$nombreRubro." ".$mesTrabajo."/".$gestionTrabajo;

            $ordenDetalle=$ordenComprobanteDetalle;

            //AQUI HACEMOS LA CONTABILIZACION DEL VALOR ANTERIOR ACTUALIZADO(actualizacion)
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaReg1','0','$codUnidadCabecera','$codAreaSA','$valorActualizacionAnterior','0','$glosaDetalle1','$ordenDetalle')";
               $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();

            $ordenDetalle=$ordenComprobanteDetalle+1;
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaDepreciacion','0','$codUnidadCabecera','$codAreaSA','0','$valorActualizacionAnterior','$glosaDetalle1','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            //FIN CONTABILIZACION VALOR ANTERIOR ACTUALIZADO

            //CONTABILIZACION ACTUALIZACION DE LA DEPRECIACION
            $ordenDetalle=$ordenComprobanteDetalle+2;
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaDepreciacion','0','$codUnidadCabecera','$codAreaSA','$valorActualizacionDepreciacion','0','$glosaDetalle2','$ordenDetalle')";
               $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();

               $ordenDetalle=$ordenComprobanteDetalle+3;
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaReg2','0','$codUnidadCabecera','$codAreaSA','0','$valorActualizacionDepreciacion','$glosaDetalle2','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            //FIN CONTABILIZACION ACTUALIZACION DE LA DEPRECIACION

            //CONTABILIZACION DE LA DEPRECIACION DEL PERIODO
            $ordenDetalle=$ordenComprobanteDetalle+4;
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaDepreciacionAF','0','$codUnidadCabecera','$codAreaSA','$valorDepreciacionPeriodo','0','$glosaDetalle3','$ordenDetalle')";
               $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();

               $ordenDetalle=$ordenComprobanteDetalle+5;
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaReg2','0','$codUnidadCabecera','$codAreaSA','0','$valorDepreciacionPeriodo','$glosaDetalle3','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            //FIN CONTABILIZACION DEPRECIACION DEL PERIODO



            //echo $codCuentaReg1." ".$codCuentaReg2."<br>";
               $ordenComprobanteDetalle=$ordenComprobanteDetalle+4;
         }
      }

}


showAlertSuccessError($flagSuccess,$urlList7);

?>
