<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

$dbh = new Conexion();

?>

<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="form1" class="form-horizontal" action="personal/rptIngresosDescuentosPrint.php" method="post" target="_blank">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Ingresos & Descuentos</h4>
          </div>
        </div>
        <div class="card-body ">
          <div class="row">
            <label class="col-sm-2 col-form-label">Oficina</label>
            <div class="col-sm-8">
              <div class="form-group">
                  <select name="cod_uo" id="cod_uo" class="selectpicker form-control" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" onChange="ajaxOficinaPersonal(this);">
                      <option value=""></option>
                      <?php 
                      $queryUO = "SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 order by nombre";
                      $statementUO = $dbh->query($queryUO);
                      while ($row = $statementUO->fetch()){ ?>
                          <option  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                      <?php } ?>
                  </select>
              </div>
            </div>                  
          </div>
          <!-- <div class="row">
            <label class="col-sm-2 col-form-label">Area</label>
            <div class="col-sm-8">
              <div class="form-group" >
                  <div id="div_contenedor_area_tcc">                      
                  </div>                    
              </div>
            </div>
          </div> -->

          <div class="row">
              <label class="col-sm-2 col-form-label">Personal</label>
              <div class="col-sm-8">
              <div class="form-group">
                  <div id="div_contenedor_personal">
                      <select id="cod_personal" name="cod_personal" class="selectpicker form-control" data-style="btn btn-info" data-size="5">
                          
                      </select>

                  </div>
              </div>
              </div><!--fin campo cod_responsables_responsable -->

          </div><!--fin campo cod_responsables_autorizadopor --> 

        </div>
        <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>