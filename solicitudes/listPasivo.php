<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
//RECIBIMOS LAS VARIABLES
$codigo=$_GET['cod'];
$stmtPasivo = $dbh->prepare("SELECT * from solicitud_recursoscuentas where cod_cuenta=:codigo");
// Ejecutamos
$stmtPasivo->bindParam(':codigo',$codigo);
$stmtPasivo->execute();
$tituloCuentaPasivo="";
$codigoPasivoX=0;
while ($rowPasivo = $stmtPasivo->fetch(PDO::FETCH_ASSOC)) {
  $codigoXX=$rowPasivo['cod_cuenta'];
  $codigoPasivoXX=$rowPasivo['cod_cuentapasivo'];
  $tituloCuentaPasivo="[".obtieneNumeroCuenta($codigoXX)."] ".nameCuenta($codigoXX);
}
?>

<div class="content">
  <div class="container-fluid">

    <div class="col-md-12">
      <form id="formChequePago" class="form-horizontal" action="<?=$urlSavePasivo;?>" method="post">
        <input type="hidden" name="codigo" value="<?=$codigo?>">
      <div class="card">
        <div class="card-header card-header-info card-header-text">
        <div class="card-text">
          <h4 class="card-title">Registrar Cuenta de Pasivo</h4>
        </div>
        </div>
        <div class="card-body ">
          <div class="row">
          <label class="col-sm-2 col-form-label">Cuenta Gasto</label>
          <div class="col-sm-7">
          <div class="form-group">
             <input type="text" class="form-control" readonly value="<?=$tituloCuentaPasivo?>">
          </div>
          </div>
        </div>

          <div class="row">
          <label class="col-sm-2 col-form-label">Cuenta Pasivo</label>
          <div class="col-sm-7">
          <div class="form-group">
            <select class="selectpicker form-control" name="cuenta" id="cuenta" data-size="6" data-style="<?=$comboColor;?>" data-live-search="true" required>
                          <?php
                  $stmt = $dbh->prepare("SELECT p.codigo,p.nombre,p.numero FROM plan_cuentas p where p.nivel=5 and p.numero like '2%' order by p.codigo"); //where NOT EXISTS (SELECT 1 FROM cheques d WHERE d.cod_banco=p.codigo)
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
                  $numeroX=$row['numero'];
                  if($codigoPasivoXX==$codigoX){
                    ?>
                   <option value="<?=$codigoX;?>" selected><?=$numeroX;?> - <?=$nombreX;?></option>  
                   <?php
                  }else{
                    ?>
                   <option value="<?=$codigoX;?>"><?=$numeroX;?> - <?=$nombreX;?></option>  
                   <?php
                  }
                }
                  ?> 
                       </select>
          </div>
          </div>
        </div>
        
        </div>
        <div  class="card-footer fixed-bottom">
        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
        <a href="<?=$urlListCC2;?>" class="<?=$buttonCancel;?>">Volver </a>
        </div>
      </div>
      </form>
    </div>
  
  </div>
</div>