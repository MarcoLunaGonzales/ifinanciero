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

  if(isset($arraySql[1])){
    $codigoArea=trim($arraySql[1]);
    $sqlAreas="and p.cod_area=".$codigoArea;
  }
}

if(isset($_GET["sim"])){
  if($_GET["sim"]=="sec"){
    $query="SELECT s.*,p.cod_area FROM simulaciones_costos s join plantillas_costo p on p.codigo=s.cod_plantillacosto  where s.cod_estadosimulacion=3 $sqlAreas order by s.codigo";
    $query2="";
  }else{
    $query="";
    $query2="SELECT s.*,p.cod_area,c.nombre as cliente FROM simulaciones_servicios s join plantillas_servicios p on p.codigo=s.cod_plantillaservicio join clientes c on c.codigo=s.cod_cliente where s.cod_responsable=$usuario and s.cod_estadosimulacion=5 $sqlAreas order by s.codigo"; //cod_responsable=$usuario and
  }
}else{
  $query="SELECT s.*,p.cod_area FROM simulaciones_costos s join plantillas_costo p on p.codigo=s.cod_plantillacosto  where s.cod_estadosimulacion=3 $sqlAreas order by s.codigo";
 $query2="SELECT s.*,p.cod_area,c.nombre as cliente FROM simulaciones_servicios s join plantillas_servicios p on p.codigo=s.cod_plantillaservicio join clientes c on c.codigo=s.cod_cliente where s.cod_responsable=$usuario and s.cod_estadosimulacion=5 $sqlAreas order by s.codigo"; //cod_responsable=$usuario and
}
 
  ?>
   <!--<label class="col-sm-3 col-form-label">Propuesta :</label>-->
   <div class="col-sm-12">
     <div class="form-group">
          <select class="selectpicker form-control form-control-sm" data-size="10" data-live-search="true" name="simulaciones" id="simulaciones" data-style="btn-warning" required>
           <?php 
           $stmt = $dbh->prepare($query);
           $stmt->execute();
           while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              $nomArea=abrevArea_solo($row['cod_area']);
              $codigoSim=$row['codigo'];    
              ?><option value="<?=$codigoSim?>$$$SIM" class="text-right">(<?=$codigoSim?> - <?=$nomArea?>) <?=$row['nombre']?></option>
             <?php 
             } 
          $stmt2 = $dbh->prepare($query2);
           $stmt2->execute();
           while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
              $codigoSim=$row2['codigo'];
              $cliente=$row2['cliente'];
              $nomArea=abrevArea_solo($row2['cod_area']);    
              ?><option value="<?=$codigoSim?>$$$TCP" class="text-right"><?=$row2['nombre']?> - <?=$nomArea?> <?=$cliente?></option>
             <?php 
             } ?> 
           </select>
      </div>
    </div>        