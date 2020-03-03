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
 $query="SELECT * FROM plantillas_servicios_tiposervicio where cod_plantillaservicio=$codigo order by codigo";
 $stmt = $dbh->prepare($query);
 $stmt->execute();
 $sum=0;
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $monto=$row['monto'];
   $cantidad=$row['cantidad'];
   $sum+=$monto*$cantidad;
 }
 $query="SELECT * FROM plantillas_servicios where codigo=$codigo";
 $stmt = $dbh->prepare($query);
 $stmt->execute();
 $dias=1;$utilidad=1;
 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
   $dias=$row['dias_auditoria'];
   $utilidad=$row['utilidad_minima'];
   $anios=$row['anios'];
 }
  ?>                      <label class="col-sm-2 col-form-label">Dias Auditoria:</label>
                           <div class="col-sm-1"> 
                             <div class="form-group">
                                <div class="form-check">
                                  <div class="form-group">
                                  <input type="number" min="1" class="form-control" value="<?=$dias?>" id="dias_auditoria" name="dias_auditoria">
                                   </div>
                                  </div>
                               </div>
                             </div>
                          <label class="col-sm-2 col-form-label">Utilidad Minima:</label>
                           <div class="col-sm-1"> 
                             <div class="form-group">
                                  <input type="text" class="form-control" value="<?=$utilidad?>" id="utilidad_minima" name="utilidad_minima">
                             </div>    
                          </div>
                          <label class="col-sm-1 col-form-label">Años:</label>
                           <div class="col-sm-2"> 
                             <div class="form-group">
                                  <input type="text" class="form-control" value="<?=$anios?>" id="anios" name="anios">
                             </div>    
                          </div> 
<?php   
}     