<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET["codigo"])){
 $codigo=$_GET["codigo"];
 $query="SELECT * FROM precios_plantillacosto where cod_plantillacosto=$codigo order by codigo";
  ?>
  <label class="col-sm-3 col-form-label">Precio Venta:</label>
  <div class="col-sm-9">
     <div class="form-group">
          <select class="selectpicker form-control" name="precio_venta" id="precio_venta" data-style="<?=$comboColor;?>" required>
           <?php 
           $stmt = $dbh->prepare($query);
           $stmt->execute();
           while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $codigoPrecio=$row['codigo'];
              $precioLocal=number_format($row['venta_local'], 2, '.', ',');
              $precioExterno=number_format($row['venta_externo'], 2, '.', ',');
              ?><option value="<?=$codigoPrecio?>" class="text-right">Ibnorca: <?=$precioLocal?>, Fuera: <?=$precioExterno?></option>
             <?php 
             } ?> 
           </select>
      </div>
   </div>
  <!--<label class="col-sm-3 col-form-label">Precio Venta Fuera Ibnorca :</label>
  <div class="col-sm-3">
     <div class="form-group">
          <select class="selectpicker form-control" name="precio_externo" id="precio_externo" data-style="<?=$comboColor;?>" required>
           <?php 
           $stmtFuera = $dbh->prepare($query);
           $stmtFuera->execute();
           while ($rowFuera = $stmtFuera->fetch(PDO::FETCH_ASSOC)) {
              $codigoPrecio=$rowFuera['codigo'];
              $precioExterno=number_format($rowFuera['venta_externo'], 2, '.', ',');
              ?><option value="<?=$codigoPrecio?>" class="text-right"><?=$precioExterno?></option>
             <?php 
             } ?> 
           </select>
      </div>
   </div>-->  
<?php   
}     