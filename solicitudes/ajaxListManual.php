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
  ?>
  <div class="row col-sm-12">
                           <div class="col-sm-6">
                                 <div class="form-group">
                                    <select class="selectpicker form-control form-control-sm" name="unidad_solicitud" id="unidad_solicitud" data-style="btn btn-primary">
                                      <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                       ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                      }
                                    ?>
                                   </select>
                                   </div>
                                 </div>
                                 <div class="col-sm-6">
                                       <div class="form-group">
                                       <select class="selectpicker form-control form-control-sm" name="area_solicitud" id="area_solicitud" data-style="btn btn-primary">
                                     <?php
                                                             
                                           $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                         $stmt->execute();
                                         $cont=0;
                                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$row['codigo'];
                                           $nombreX=$row['nombre'];
                                           $abrevX=$row['abreviatura'];
                                            ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                         } 
                                         ?>
                                        </select>
                                      </div>
                                 </div>      
  </div>