<?php

// require_once 'layouts/bodylogin2.php';
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
$query = "select * from depreciaciones";
$statement = $dbh->query($query);

$gestionGlobal=$_SESSION['globalGestion'];

$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-12-31";

if(isset($bkLink)){
  $urlReporteBalance="reportes/reporteBalanceBK.php";
}
?>

<div class="content">
	<div class="container-fluid">
    <div style="overflow-y: scroll;">
      <div class="col-md-12">
        <form id="form1" class="form-horizontal" action="<?=$urlReporteBalance;?>" method="post" target="_blank">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Reporte Balance General</h4>
          </div>
          </div>
          <div class="card-body ">
            <div class="row">
              <label class="col-sm-2 col-form-label">Entidad</label>
              <div class="col-sm-7">
                <div class="form-group">                            
                          <!-- <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)">  -->
                          <select class="selectpicker form-control form-control-sm" name="entidad[]" id="entidad" required onChange="ajax_entidad_Oficina()" multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true">                           
                          <?php
                          $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM entidades where cod_estadoreferencial=1 order by 2");
                         $stmt->execute();
                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $codigoX=$row['codigo'];
                            $nombreX=$row['nombre'];
                            $abrevX=$row['abreviatura'];
                          ?>
                       <option value="<?=$codigoX;?>"><?=$nombreX?></option>  
                         <?php
                           }
                           ?>
                      </select>
                  </div>
              </div>  

            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Gestion</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <select name="gestion" id="gestion" class="selectpicker form-control form-control-sm " data-style="btn btn-info"
                      required onChange="AjaxGestionFechaDesdeBG(this)">
                      <?php
                        $sql="SELECT * FROM gestiones order by 2 desc";
                        $stmtg = $dbh->prepare($sql);
                        $stmtg->execute();
                        while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                          $codigog=$rowg['codigo'];
                          $nombreg=$rowg['nombre'];
                          // if($codigog!=$gestionGlobal){
                            ?>
                        <option value="<?=$codigog;?>"><?=$nombreg;?></option>
                        <?php 
                          // }     
                        }
                      ?>
                  </select>
                </div>
              </div>
            </div><!--fin campo gestion -->
            <div class="row">
              <label class="col-sm-2 col-form-label">Oficina</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <div id="div_contenedor_oficina1">                              
                  </div>

                </div>
              </div>
            </div><!--fin campo gestion -->


            <div class="row">
                <label class="col-sm-2 col-form-label">A Fecha:</label>
                <div class="col-sm-7">
                  <div class="form-group">
                    <div id="div_contenedor_fechaH">                    
                      <!-- <input type="text" class="form-control datepicker " autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>" value="<?=$fechaHasta?>">   -->
                      <input type="date" name="fecha" id="fecha" class="form-control" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>">
                    </div>
                      
                  </div>
                </div>
            </div><!--fin campo RUBRO -->
            <div class="row">
              <label class="col-sm-2 col-form-label">Nivel</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <select name="nivel" id="nivel" class="selectpicker form-control form-control-sm " data-style="btn btn-danger"
                      required>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5" selected>5</option>
                  </select>
                </div>
              </div>
            </div><!--fin campo gestion -->
          </div>
          <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
            </div>
        </div>
        </form>
      </div>
    </div>
		
	
	</div>
</div>