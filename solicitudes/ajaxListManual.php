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
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$v=0;
if(isset($_GET['v'])){
 $v=$_GET['v'];
 if($v!=0){
  //$globalUnidad=obtenerIdUnidadServicioIbnorca($v);
  //$globalArea=obtenerIdAreaServicioIbnorca($v); 
 }
}

  $sqlAreas="";
  $sqlOficina="";
  if(isset($_GET['s'])){
    $s=$_GET['s'];
    $arraySql=explode("IdArea",$s);
    $codigoArea='0';  
    if(isset($arraySql[1])){
      $codigoArea=trim($arraySql[1]);
    }
    
    if($codigoArea=='0'){
      $sqlAreas="and (aa.cod_area=0)";
    }else{
      $sqlAreas="and (aa.cod_area ".$codigoArea.")";               
    }


    //oficina   
    $codigoOficina='0';  
    if(isset($arraySql[0])){
      $arraySql[0]=str_replace("and","",$arraySql[0]); //para quitar el and
      $arraySqlOficina=explode("IdOficina",$arraySql[0]);
      if(isset($arraySqlOficina[1])){
         $codigoOficina=trim($arraySqlOficina[1]);
      }
    }
    
    if($codigoOficina=='0'){
      $sqlOficina="and (codigo=0)";
    }else{
      $sqlOficina="and (codigo ".$codigoOficina.")";               
    }

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
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 $sqlOficina order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX==$globalUnidad){
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
                                                             
                                           $stmt = $dbh->prepare("SELECT a.codigo, a.nombre, a.abreviatura FROM areas a join areas_activas aa on aa.cod_area=a.codigo $sqlAreas where a.cod_estado=1 order by 2");
                                         $stmt->execute();
                                         $cont=0;
                                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$row['codigo'];
                                           $nombreX=$row['nombre'];
                                           $abrevX=$row['abreviatura'];
                                           if($codigoX==$globalArea){
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
  </div>