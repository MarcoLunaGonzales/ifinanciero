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
$usuario=$_SESSION['globalUser'];
 $query="SELECT * FROM af_proveedores order by codigo";
  ?>
   <label class="col-sm-3 col-form-label">Proveedores :</label>
   <div class="col-sm-9">
     <div class="form-group">
          <select class="selectpicker form-control" name="proveedores" id="proveedores" data-style="<?=$comboColor;?>" required>
           <?php 
           $stmt = $dbh->prepare($query);
           $stmt->execute();
           while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $codigoProv=$row['codigo'];    
              ?><option value="<?=$codigoProv?>" class="text-right"><?=$row['nombre']?></option>
             <?php 
             } ?> 
           </select>
      </div>
    </div>        