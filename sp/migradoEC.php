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

//$codComprobanteOrigen=18692; //COMP SCZ
//$codComprobanteOrigen=18693; //COMP CBBA
//$codComprobanteOrigen=18696; //COMP ORURO
//$codComprobanteOrigen=18697; //COMP POTOSI
//$codComprobanteOrigen=18694; //COMP SUCRE

$codComprobanteOrigen=18695; //COMP TARIJA
$codCuentaOrigen=67;
$tipoEstadoCuenta=2;//CLIENTE

$sql="SELECT c.codigo, c.cod_comprobante, c.cod_cuenta, c.cod_cuentaauxiliar, c.debe, c.glosa, cc.fecha from comprobantes cc, comprobantes_detalle c where cc.codigo=c.cod_comprobante and  c.cod_cuenta='$codCuentaOrigen' and c.cod_comprobante='$codComprobanteOrigen'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nombreX=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoX=$row['codigo'];
  $codComprobanteX=$row['cod_comprobante'];
  $codCuentaX=$row['cod_cuenta'];
  $codCuentaAuxiliarX=$row['cod_cuentaauxiliar'];
  $debeX=$row['debe'];
  $glosaX=$row['glosa'];
  $fechaX=$row['fecha'];

  echo "$codigoX $codComprobanteX $codCuentaX $codCuentaAuxiliarX $debeX $glosaX $fechaX <br>";

  $sqlInsert="INSERT into estados_cuenta(cod_comprobantedetalle, cod_plancuenta, monto,  cod_proveedor, fecha, cod_comprobantedetalleorigen, cod_cuentaaux, cod_cajachicadetalle, cod_tipoestadocuenta, glosa_auxiliar) values ('$codigoX','$codCuentaX','$debeX','0','$fechaX','0','$codCuentaAuxiliarX','0','$tipoEstadoCuenta','$glosaX')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  //$stmtInsert->execute();

  
}

echo "<h6>HORA FIN PROCESO CARGADO ESTADO CUENTAS: " . date("Y-m-d H:i:s")."</h6>";

?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>