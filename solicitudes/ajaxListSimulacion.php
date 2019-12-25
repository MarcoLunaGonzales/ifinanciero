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
 $query="SELECT * FROM simulaciones_costos where cod_responsable=$usuario and cod_estadosimulacion=3 order by codigo";
  ?>
   <label class="col-sm-3 col-form-label">Simulacion :</label>
   <div class="col-sm-9">
     <div class="form-group">
          <select class="selectpicker form-control" name="simulaciones" id="simulaciones" data-style="<?=$comboColor;?>" required>
           <?php 
           $stmt = $dbh->prepare($query);
           $stmt->execute();
           while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $codigoSim=$row['codigo'];    
              ?><option value="<?=$codigoSim?>" class="text-right"><?=$row['nombre']?></option>
             <?php 
             } ?> 
           </select>
      </div>
    </div>        