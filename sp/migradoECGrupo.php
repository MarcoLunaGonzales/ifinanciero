<?php

set_time_limit(0);
error_reporting(-1);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();

?>


<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title">Cargado de Estados de Cuenta</h4>
          </div>
          <div class="card-body">
                  
<?php

echo "<h6>Hora Inicio Proceso ESTADO CUENTAS: " . date("Y-m-d H:i:s")."</h6>";

$codCuentaOrigen=162;
$sqlTipo="select c.tipo from configuracion_estadocuentas c where c.cod_plancuenta=$codCuentaOrigen";
$stmtTipo=$dbh->prepare($sqlTipo);
$stmtTipo->execute();
$tipoDebeHaber=0;
while ($rowTipo = $stmtTipo->fetch(PDO::FETCH_ASSOC)) {
  $tipoDebeHaber=$rowTipo['tipo'];
}

$sql="SELECT c.codigo, c.cod_comprobante, c.cod_cuenta, c.cod_cuentaauxiliar, c.debe, c.haber, c.glosa, cc.fecha from comprobantes cc, comprobantes_detalle c where cc.codigo=c.cod_comprobante and  c.cod_cuenta='$codCuentaOrigen' and cc.cod_tipocomprobante='4'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nombreX=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoX=$row['codigo'];
  $codComprobanteX=$row['cod_comprobante'];
  $codCuentaX=$row['cod_cuenta'];
  $codCuentaAuxiliarX=$row['cod_cuentaauxiliar'];
  $debeX=$row['debe'];
  $haberX=$row['haber'];
  $glosaX=$row['glosa'];
  $fechaX=$row['fecha'];

  $montoEstadoCuenta=0;
  if($tipoDebeHaber==1){
    $montoEstadoCuenta=$debeX;
  }else{
    $montoEstadoCuenta=$haberX;
  }

  echo "$codigoX $codComprobanteX $codCuentaX $codCuentaAuxiliarX $montoEstadoCuenta $glosaX $fechaX <br>";

  //$sqlInsert="INSERT into estados_cuenta(cod_comprobantedetalle, cod_plancuenta, monto,  cod_proveedor, fecha, cod_comprobantedetalleorigen, cod_cuentaaux, cod_cajachicadetalle, cod_tipoestadocuenta, glosa_auxiliar) values ('$codigoX','$codCuentaX','$debeX','0','$fechaX','0','$codCuentaAuxiliarX','0','$tipoEstadoCuenta','$glosaX')";
  //$stmtInsert = $dbh->prepare($sqlInsert);
  //$stmtInsert->execute();

  
}

echo "<h6>HORA FIN PROCESO CARGADO ESTADO CUENTAS GRUPO: " . date("Y-m-d H:i:s")."</h6>";

?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>