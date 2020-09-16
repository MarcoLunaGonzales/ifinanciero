<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
require_once '../styles.php';
//header('Content-Type: application/json');
//ini_set("display_errors", "1");
$dbh = new Conexion();


$cod_comprobante=$_GET['cod_comprobante'];

$query="SELECT numero,MONTH(fecha) as mes,cod_tipocomprobante,cod_unidadorganizacional,cod_gestion from comprobantes where codigo=$cod_comprobante";
// echo $query;
$stmt = $dbh->prepare($query);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {  
  $numero=$row['numero'];
  $mes=$row['mes'];
  $cod_tipocomprobante=$row['cod_tipocomprobante'];
  $cod_unidadorganizacional=$row['cod_unidadorganizacional'];
  $cod_gestion=$row['cod_gestion'];
}
?>

<div class="row">
  <label class="col-sm-4 text-right col-form-label" style="color:#424242">Gestión</label>
  <div class="col-sm-6">
    <div class="form-group">            
      <select class="selectpicker form-control form-control-sm" name="gestion" id="gestion" data-style="<?=$comboColor;?>">
            <option disabled selected value="">Gestión</option>
          <?php
          $stmt = $dbh->prepare("SELECT nombre from gestiones where cod_estado=1 order by nombre desc");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $codigoX=$row['nombre'];
          $nombreX=$row['nombre'];                  
        ?>
        <option <?=($cod_gestion==$codigoX)?"selected":"disabled";?> value="<?=$codigoX;?>"><?=$nombreX;?></option>  
        <?php
          }
          ?>
      </select>
    </div>
  </div>
</div>  
<div class="row">
  <label class="col-sm-4 text-right col-form-label" style="color:#424242">Mes del comprobante</label>
  <div class="col-sm-6">
    <div class="form-group">              
      <select class="selectpicker form-control form-control-sm" name="mes_comprobante" id="mes_comprobante" data-style="<?=$comboColor;?>">
          <option disabled selected value=""></option>                
          <option <?=($mes==1)?"selected":"disabled";?> value="1">ENERO</option>
          <option <?=($mes==2)?"selected":"disabled";?> value="2">FEBRERO</option>
          <option <?=($mes==3)?"selected":"disabled";?> value="3">MARZO</option>
          <option <?=($mes==4)?"selected":"disabled";?> value="4">ABRIL</option>
          <option <?=($mes==5)?"selected":"disabled";?> value="5">MAYO</option>
          <option <?=($mes==6)?"selected":"disabled";?> value="6">JUNIO</option>
          <option <?=($mes==7)?"selected":"disabled";?> value="7">JULIO</option>
          <option <?=($mes==8)?"selected":"disabled";?> value="8">AGOSTO</option>
          <option <?=($mes==9)?"selected":"disabled";?> value="9">SEPTIEMBRE</option>
          <option <?=($mes==10)?"selected":"disabled";?> value="10">OCTUBRE</option>
          <option <?=($mes==11)?"selected":"disabled";?> value="11">NOVIEMBRE</option>
          <option <?=($mes==12)?"selected":"disabled";?> value="12">DICIEMBRE</option>                  
      </select>
    </div>
  </div>
</div>      
<div class="row">
  <label class="col-sm-4 text-right col-form-label" style="color:#424242">Tipo de comprobante</label>
  <div class="col-sm-6">
    <div class="form-group">            
      <select class="selectpicker form-control form-control-sm" name="tipo_comprobante" id="tipo_comprobante" data-style="<?=$comboColor;?>">
            <option disabled selected value="">Tipo</option>
          <?php
          $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_comprobante where cod_estadoreferencial=1 order by 1");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $codigoX=$row['codigo'];
          $nombreX=$row['nombre'];
          $abrevX=$row['abreviatura'];
        ?>
        <option <?=($cod_tipocomprobante==$codigoX)?"selected":"disabled";?>  value="<?=$codigoX;?>"><?=$nombreX;?> - <?=$abrevX;?></option>  
        <?php
          }
          ?>
      </select>
    </div>
  </div>
</div>
<div class="row">
  <label class="col-sm-4 text-right col-form-label" style="color:#424242">Unidad</label>
  <div class="col-sm-6">
    <div class="form-group">            
      <select class="selectpicker form-control form-control-sm" name="unidad" id="unidad" data-style="<?=$comboColor;?>">
            <option disabled selected value="">Tipo</option>
          <?php
          $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 order by 1");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $codigoX=$row['codigo'];
          $nombreX=$row['nombre'];
          $abrevX=$row['abreviatura'];
        ?>
        <option <?=($cod_unidadorganizacional==$codigoX)?"selected":"disabled";?>  value="<?=$codigoX;?>"><?=$nombreX;?> - <?=$abrevX;?></option>  
        <?php
          }
          ?>
      </select>
    </div>
  </div>
</div>  
<div class="row">
  <label class="col-sm-4 text-right col-form-label" style="color:#424242">Número de Comprobante</label>
  <div class="col-sm-6">
    <div class="form-group">
      <input type="number"name="nro_comprobante" id="nro_comprobante" value="<?=$numero?>" class="form-control" readonly>
    </div>
  </div>        
</div>   
<div class="row" id="contenedor_detalle_comprobante">

</div>