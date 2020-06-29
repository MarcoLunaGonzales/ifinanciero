<?php

// require_once 'layouts/bodylogin2.php';
require_once 'conexion.php';
require_once 'libretas_bancarias/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
$query = "select * from depreciaciones";
$statement = $dbh->query($query);

$gestionGlobal=$_SESSION['globalGestion'];
$global_mes=$_SESSION["globalMes"];

$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m,1,$y)-1));
$ma=date("m",(mktime(0,0,0,$m,1,$y)-1));
$fechaDesde=$y."-".($ma)."-01";
$fechaHasta=$y."-".($ma)."-".$d."";

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";
?>

<div class="content">
	<div class="container-fluid">
    <div style="overflow-y: scroll;">
      <div class="col-md-12">
        <form id="form1" class="form-horizontal" action="<?=$urlContaLibretasBancarias;?>" method="post" target="_blank">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">DEPOSITOS NO FACTURADOS</h4>
          </div>
          </div>
          <div class="card-body ">
            <div class="row">
              <label class="col-sm-2 col-form-label">Libretas</label>
              <div class="col-sm-7">
                <div class="form-group">                            
                          <!-- <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)">  -->
                          <select class="selectpicker form-control form-control-sm" name="libretas" id="libretas" required>                           
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
            <div class="row">
                    <label class="col-sm-2 col-form-label">Gestión</label>
                    <div class="col-sm-7">
                      <div class="form-group">
                        <select name="gestion" id="gestion" onChange="ajax_mes_de_gestion(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                    <option value=""></option>
                                    <?php 
                                    $query = "SELECT codigo,nombre from gestiones where cod_estado=1 ORDER BY nombre desc";
                                    $stmt = $dbh->query($query);
                                    while ($row = $stmt->fetch()){ ?>
                                        <option value="<?=$row["codigo"];?>" <?=($row["codigo"]==$gestionGlobal)?"selected":""?> ><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
                         </div>
                    </div>                     
                    </div><!--div row-->
                  <div class="row">
                     <label class="col-sm-2 col-form-label">Mes</label>
                     <div class="col-sm-7">
                      <div class="form-group">
                        <div id="div_contenedor_mes">   
                          <?php $sql="SELECT c.cod_mes,(select m.nombre from meses m where m.codigo=c.cod_mes) as nombre_mes from meses_trabajo c where c.cod_gestion=$gestionGlobal";
                  $stmtg = $dbh->prepare($sql);
                  $stmtg->execute();
                  ?>
                  <select name="cod_mes_x" id="cod_mes_x" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  required data-live-search="true">
                  <?php
                    
                    while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {    
                      $cod_mes=$rowg['cod_mes'];    
                      $nombre_mes=$rowg['nombre_mes'];    
                    ?>
                    <option value="<?=$cod_mes;?>" <?=($cod_mes==$global_mes)?"selected":""?> ><?=$nombre_mes;?></option>
                    <?php 
                    }
                  ?>
                  </select>
                          
                        </div>                                    
                         </div>
                      </div>
                </div>

          </div>
          <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="btn btn-primary">Listar</button>
            </div>
        </div>
        </form>
      </div>
    </div>
		
	
	</div>
</div>