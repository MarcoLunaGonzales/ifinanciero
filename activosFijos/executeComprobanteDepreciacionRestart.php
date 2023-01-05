<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsDepreciacion.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codDepreciacion=$_GET["codigo"];
$codComprobanteExistente=$_GET["comprobante"];


//SE DEBE PARAMETRIZAR ESTE CODIGO DE CUENTA PARA LA DEPRECIACION
// $codCuentaDepreciacion=298;//actualizacion  valor anterior
$codCuentaDepreciacion=299;//actualizacion  valor anterior
// $codCuentaDepreciacionAF=256;//depreciacion //contra cuenta
$codCuentaDepreciacionAF=257;//depreciacion //contra cuenta

$sqlMes="SELECT m.mes,m.gestion from mesdepreciaciones m where m.codigo='$codDepreciacion'";
$stmtMes = $dbh->prepare($sqlMes);
$stmtMes -> execute();
while ($rowMes = $stmtMes->fetch(PDO::FETCH_ASSOC)) {
  $codMes=$rowMes['mes'];
  $codGestion=$rowMes['gestion'];
}
$abrevMes=abrevMes($codMes);

//******depreciacion mes a mes
$nameFechasComprobante=$abrevMes."/".$codGestion;
//***insertamos cabecera
      $codAreaSA="501";//area para todas las oficinas DN
      $tipoComprobante=3;
      $globalUnidadX=5;//se contabilizara en RLP
      $gestionTrabajo=$codGestion;
      $mesTrabajo=$codMes;

      $codEmpresa=1;
      $codMoneda=1;
      $codEstadoComprobante=1;

      $horaActual=date("H:i:s");
      $fechaContabilizacion=$gestionTrabajo."-".$mesTrabajo."-01";
      $fecha_x = date("Y-m-t", strtotime($fechaContabilizacion));//ultimo dia de la fecha
      $fecha_contabilizacion = $fecha_x." ".$horaActual;
      
      $numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $globalUnidadX, $gestionTrabajo, $mesTrabajo);
      //$glosaCabecera="Actualizacion y Depreciacion de Activos Fijos Mes: ".$mesTrabajo." ".$gestionTrabajo." Unidad: ".$nombreUnidadCabecera;
      $glosaCabecera="Actualización y Depreciación de Bienes de Uso y Activos Fijo corresp ".$nameFechasComprobante;
      


      //REESCRIBIMOS EL COD COMPROBANTE RECUPERADO
      $codComprobante=$codComprobanteExistente;


      //BORRAMOS EL DETALLE
      $sqlDeleteComp="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
      $stmtDeleteComp = $dbh->prepare($sqlDeleteComp);
      $flagSuccess=$stmtDeleteComp->execute();



//PARTE QUE REALIZA el COMPROBANTE detalle POR UNIDAD ORG.
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
// $stmtUnidades->bindColumn('mes', $mesCabecera);
// $stmtUnidades->bindColumn('gestion', $gestionCabecera);

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
      $stmt->bindColumn('cod_depreciaciones', $codRubro);
      $stmt->bindColumn('nombrerubro', $nombreRubro);
      $stmt->bindColumn('actualizacionanterior', $valorActualizacionAnterior);
      $stmt->bindColumn('actualizaciondepreciacion', $valorActualizacionDepreciacion);
      $stmt->bindColumn('depreciacionperiodo', $valorDepreciacionPeriodo);

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

            $codCuentaReg1=$codigoCuenta;
            
            $contraCuenta=obtenerContraCuentaDepreciacion($codRubro);
            $codCuentaReg2=$contraCuenta;
            if($contraCuenta==0){
               $codCuentaReg2=$codigoCuenta;
            }

            $glosaDetalle1="Actualización Valor Anterior ".$nombreUnidadCabecera." ".$nombreRubro." ".$nameFechasComprobante;
            $glosaDetalle2="Actualización Dep. Acumulada ".$nombreUnidadCabecera." ".$nombreRubro." ".$nameFechasComprobante;
            $glosaDetalle3="Depreciación Periodo ".$nombreUnidadCabecera." ".$nombreRubro." ".$nameFechasComprobante;


            $ordenDetalle=$ordenComprobanteDetalle;

            if($valorActualizacionAnterior>0){
               //AQUI HACEMOS LA CONTABILIZACION DEL VALOR ANTERIOR ACTUALIZADO(actualizacion)
               $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaReg1','0','$codUnidadCabecera','$codAreaSA','$valorActualizacionAnterior','0','$glosaDetalle1','$ordenDetalle')";
                  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
               $flagSuccessDet=$stmtInsertDet->execute();

               $ordenDetalle=$ordenComprobanteDetalle+1;
               $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaDepreciacion','0','$codUnidadCabecera','$codAreaSA','0','$valorActualizacionAnterior','$glosaDetalle1','$ordenDetalle')";
               $stmtInsertDet = $dbh->prepare($sqlInsertDet);
               $flagSuccessDet=$stmtInsertDet->execute();
               //FIN CONTABILIZACION VALOR ANTERIOR ACTUALIZADO
               $ordenDetalle=$ordenComprobanteDetalle+2;
            }
            
            if($valorActualizacionDepreciacion>0){
               //CONTABILIZACION ACTUALIZACION DE LA DEPRECIACION
               $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaDepreciacion','0','$codUnidadCabecera','$codAreaSA','$valorActualizacionDepreciacion','0','$glosaDetalle2','$ordenDetalle')";
                  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
               $flagSuccessDet=$stmtInsertDet->execute();

                  $ordenDetalle=$ordenComprobanteDetalle+3;
               $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaReg2','0','$codUnidadCabecera','$codAreaSA','0','$valorActualizacionDepreciacion','$glosaDetalle2','$ordenDetalle')";
               $stmtInsertDet = $dbh->prepare($sqlInsertDet);
               $flagSuccessDet=$stmtInsertDet->execute();
               //FIN CONTABILIZACION ACTUALIZACION DE LA DEPRECIACION
               $ordenDetalle=$ordenComprobanteDetalle+4;
            }

            //CONTABILIZACION DE LA DEPRECIACION DEL PERIODO
            if($valorDepreciacionPeriodo>0){
               $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaDepreciacionAF','0','$codUnidadCabecera','$codAreaSA','$valorDepreciacionPeriodo','0','$glosaDetalle3','$ordenDetalle')";
                  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
               $flagSuccessDet=$stmtInsertDet->execute();
                  $ordenDetalle=$ordenComprobanteDetalle+5;
               $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$codCuentaReg2','0','$codUnidadCabecera','$codAreaSA','0','$valorDepreciacionPeriodo','$glosaDetalle3','$ordenDetalle')";
               $stmtInsertDet = $dbh->prepare($sqlInsertDet);
               $flagSuccessDet=$stmtInsertDet->execute();
               //FIN CONTABILIZACION DEPRECIACION DEL PERIODO
               $ordenComprobanteDetalle=$ordenComprobanteDetalle+4;
            }    
            //echo $codCuentaReg1." ".$codCuentaReg2."<br>";
         }
      }

}

echo "EL COMPROBANTE SE REESCRIBIO CORRECTAMENTE!!!!!";
?>
