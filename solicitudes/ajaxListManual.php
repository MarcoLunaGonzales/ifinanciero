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

//echo  $usuario;

$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

/*SACAREMOS EL AREA Y LA OFICINA DESDE EL FINANCIERO RRHH*/
$sqlUnidadArea="SELECT p.cod_area, p.cod_unidadorganizacional from personal p where p.codigo='$usuario'";
$stmtUnidadArea = $dbh -> prepare($sqlUnidadArea);
$stmtUnidadArea->execute();
$codAreaUsuario=0;
$codUnidadUsuario=0;
while ($rowUnidadArea = $stmtUnidadArea->fetch(PDO::FETCH_ASSOC)){
  $codAreaUsuario=$rowUnidadArea['cod_area'];
  $codUnidadUsuario=$rowUnidadArea['cod_unidadorganizacional'];
}

?>
  <div class="row col-sm-12 float-right">
    <div class="col-sm-12">
      <label class="bmd-label-static">ORIGEN DE LA SOLICITUD Of/Área</label>
    </div>
  </div>


  <div class="row col-sm-12">
    <div class="col-sm-6">
      <div class="form-group">
        <select class="selectpicker form-control form-control-sm" name="unidad_solicitud" onchange="cargarArrayAreaDistribucion(-1)" id="unidad_solicitud" data-style="btn btn-primary">
        <?php
        $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          $codigoX=$row['codigo'];
          $nombreX=$row['nombre'];
          $abrevX=$row['abreviatura'];
          if($codigoX==$codUnidadUsuario){
           ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
          }else{
          //if($v==0){
          ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
          //}      
          } 
        }
        ?>
        </select>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        <select class="selectpicker form-control form-control-sm" name="area_solicitud" id="area_solicitud" data-style="btn btn-rose">
        <?php                                                     
        $sqlAreas="SELECT a.codigo, a.nombre, a.abreviatura FROM areas a join areas_activas aa on aa.cod_area=a.codigo $sqlAreas where a.cod_estado=1 order by 2";
        $stmt = $dbh->prepare($sqlAreas);
        $stmt->execute();
        $cont=0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $codigoX=$row['codigo'];
          $nombreX=$row['nombre'];
          $abrevX=$row['abreviatura'];
          if($codigoX==$codAreaUsuario || ($globalAdmin==1 && $codigoX==501)){
            ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
          }else{
            ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
          }
        } 
        ?>
        </select>
      </div>
    </div>      
  </div>