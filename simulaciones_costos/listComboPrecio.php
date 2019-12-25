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
 $ibnorca=$_GET["ibnorca"];
 $query="SELECT * FROM precios_plantillacosto where cod_plantillacosto=$codigo order by codigo";
  ?>
     <div class="form-group">
          <select class="selectpicker form-control" name="precio_venta" id="precio_venta" data-style="<?=$comboColor;?>" required>
           <?php 
           $stmt = $dbh->prepare($query);
           $stmt->execute();
           while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $codigoPrecio=$row['codigo'];
              $precioLocal=number_format($row['venta_local'], 2, '.', ',');
              $precioExterno=number_format($row['venta_externo'], 2, '.', ',');
              if($ibnorca==1){
                $labelText="Ibnorca: ".$precioLocal;
              }else{
                $labelText="Fuera: ".$precioExterno;
              }    
              ?><option value="<?=$codigoPrecio?>" class="text-right"><?=$labelText?></option>
             <?php 
             } ?> 
           </select>
      </div> 
<?php   
}     