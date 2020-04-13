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

$sqlAreas="";
if(isset($_GET['s'])){
  $arraySql=explode("IdArea=",$_GET['s']);
  $codigoArea=trim($arraySql[1]);

  $sqlAreas="and p.cod_area=".$codigoArea;
}

 $query="SELECT s.*,p.cod_area FROM simulaciones_costos s join plantillas_costo p on p.codigo=s.cod_plantillacosto where s.cod_responsable=$usuario and s.cod_estadosimulacion=5 $sqlAreas order by s.codigo";
 $query2="SELECT s.*,p.cod_area FROM simulaciones_servicios s join plantillas_servicios p on p.codigo=s.cod_plantillaservicio where s.cod_responsable=$usuario and s.cod_estadosimulacion=5 $sqlAreas order by s.codigo"; //cod_responsable=$usuario and
  ?>
   <label class="col-sm-3 col-form-label">Propuesta :</label>
   <div class="col-sm-9">
     <div class="form-group">
          <select class="selectpicker form-control" name="simulaciones" id="simulaciones" data-style="<?=$comboColor;?>" required>
           <?php 
           $stmt = $dbh->prepare($query);
           $stmt->execute();
           while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $nomArea=abrevArea_solo($row['cod_area']);
              $codigoSim=$row['codigo'];    
              ?><option value="<?=$codigoSim?>$$$SIM" class="text-right"><?=$row['nombre']?> - <?=$nomArea?></option>
             <?php 
             } 
          $stmt2 = $dbh->prepare($query2);
           $stmt2->execute();
           while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
              $codigoSim=$row2['codigo'];
              $nomArea=abrevArea_solo($row2['cod_area']);    
              ?><option value="<?=$codigoSim?>$$$TCP" class="text-right"><?=$row2['nombre']?> - <?=$nomArea?></option>
             <?php 
             } ?> 
           </select>
      </div>
    </div>        