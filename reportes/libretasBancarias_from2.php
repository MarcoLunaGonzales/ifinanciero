<?php

// require_once 'layouts/bodylogin2.php';
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();

$gestionGlobal=$_SESSION['globalGestion'];

$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=date("Y-m-d");
?>

<div class="content">
	<div class="container-fluid">
    <!-- <div style="overflow-y: scroll;"> -->
      <div class="col-md-12">
        <form id="form1" class="form-horizontal" action="reportes/libretasBancarias_print2.php" method="post" target="_blank">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Reporte de Saldos Libreta Bancaria</h4>
          </div>
          </div>
          <div class="card-body ">
            <div class="row">
              <label class="col-sm-2 col-form-label">Libretas</label>
              <div class="col-sm-7">
                <div class="form-group">                                                      
                  <select class="selectpicker form-control form-control-sm" name="libretas" id="libretas" required    data-actions-box="true">
                          <?php
                          $stmt = $dbh->prepare("SELECT l.*,b.abreviatura as banco from libretas_bancarias l join bancos b on b.codigo=l.cod_banco where l.cod_estadoreferencial=1 order by l.nombre");
                         $stmt->execute();
                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $codigoX=$row['codigo'];
                            $nombreX=$row['nombre'];
                            $bancoX=$row['banco'];
                          ?>
                    <option value="<?=$codigoX;?>"><?=$nombreX?> - <?=$bancoX?></option>  
                         <?php
                           }
                           ?>
                  </select>
                </div>
              </div> 
            </div>

            <center><h4 class="text-muted">Fecha Libreta Bancaria</h4></center>
            <div class="row">
                <label class="col-sm-2 col-form-label">Del:</label>
                <div class="col-sm-3">
                  <div class="form-group">
                    <div id="div_contenedor_fechaD"> 
                      <input type="date" style="background-color:#E3CEF6;text-align: left" class="form-control" name="fecha_desde" id="fecha_desde" min="2021-01-01" max="<?=$fechaHasta?>" value="<?=$fechaDesde?>" required="true">  
                    </div>     
                  </div>
                </div>
                <label class="col-sm-1 col-form-label">Al:</label>
                <div class="col-sm-3">
                  <div class="form-group">
                    <div id="div_contenedor_fechaH"> 
                    <input type="date" style="background-color:#E3CEF6;text-align: left" class="form-control" name="fecha_hasta" id="fecha_hasta" min="2021-01-01" max="<?=$fechaHasta?>" value="<?=$fechaHasta?>" required="true">                    
                    </div>
                  </div>
                </div>
            </div><!--fin campo RUBRO -->
          </div>
          <div class="card-footer ml-auto mr-auto">
            <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
          </div>
        </div>
        </form>
      </div>
    <!-- </div> -->
		
	
	</div>
</div>