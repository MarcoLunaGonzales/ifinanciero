<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();

$codigo=$codigo;
if($codigo==0){
  $stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and distribucion_gastos=1 order by nombre");

  $stmt->execute();

  // $stmt->bindColumn('codDistribucion', $cod_distribucionDetalle);
  $stmt->bindColumn('codigo', $cod_area);
  $stmt->bindColumn('nombre', $nombre_area);
  $stmt->bindColumn('abreviatura', $abreviatura_area);

  $porcentaje=0;
  $nombre_d='';  
  ?>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
        <form name="form1" id="form1" action="<?=$urlSaveRegisterDistribucionGastos;?>" method="POST">
        <input class="form-control" type="hidden" name="codigo" id="codigo" value="<?=$codigo?>"/>        
          <div class="card">
            <div class="card-header <?= $colorCard; ?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?= $iconCard; ?></i>
              </div>
              <h4 class="card-title">Registrar Nueva Distribución</h4>
              <!-- <h6 class="card-title">Por favor active la casilla para registrar el Area</h6> -->            
            </div>
            <div class="card-body">
              <div class="row">
                <label class="col-sm-2 col-form-label" style="color:#0B2161;font-size: 15px">Nombre Distribución:</label>
                <div class="col-sm-3">
                <div class="form-group">
                  <input class="form-control" type="text" name="nombre_d" id="nombre_d" value="<?=$nombre_d?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
                </div>

                <label class="col-sm-2 col-form-label" style="color:#0B2161;font-size: 15px">Oficina:</label>
                <div class="col-sm-3">
                  <div class="form-group">
                    <select class="selectpicker form-control form-control-sm" name="unidad" id="unidad" data-style="<?=$comboColor;?>" >
                      <option disabled selected="selected" value="">Oficina</option>
                      <?php
                        $stmtOf = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 order by 2");
                        $stmtOf->execute();
                        while ($rowOf = $stmtOf->fetch(PDO::FETCH_ASSOC)) {
                          $codigoX=$rowOf['codigo'];
                          $nombreX=$rowOf['nombre'];
                          $abrevX=$rowOf['abreviatura'];
                      ?>
                      <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                      <?php
                        }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <h6 class="card-title">Por favor ingrese los porcentajes</h6>

              <div class="table-responsive">

                
                <table class="table table-condensed">
                  <thead>
                    <tr>
                      <th class="text-left">#</th>
                      <th class="text-center">Área</th>
                      <th class="text-center">Abreviatura</th>
                      <th class="text-center">Porcentaje</th>
                      <!--th class="text-right">Actions</th-->
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                       $index = 1;
                       $sum=0;

                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          
                    ?>
                      <tr>
                        <td class="text-center"><?= $index; ?></td>
                        <td class="text-left"><?= $nombre_area; ?></td>
                        <td class="text-center"><?= $abreviatura_area; ?></td>
                        <td class="text-center">
                        <input type="hidden" name="cod_area[]"  value="<?=$cod_area;?>"/>
                        <input class="form-control sm" type="number" onchange="sumarPorcentaje()" id="porcentaje<?= $index; ?>" name="porcentaje[]"  required="true" value="<?= $porcentaje; ?>" onkeyup="sumarPorcentaje(); javascript:this.value=this.value.toUpperCase();"/>  
                        </td>
                      </tr>
                    <?php
                            $index++;
                           }
                    ?>
                  </tbody>
                  <tfoot>
                    <tr></tr>
                    <tr>
                    <td colspan="2"></td>
                    <th >Porcentaje Total (100 %) : </th>
                    <th><input type="text" required="true" id="total" name="total" value="<?= $sum; ?>" readonly="readonly" ></th>
                    </tr>
                  </tfoot>
                       </table>
                
              </div>
            </div>
          </div>
          <?php
          if($globalAdmin == 1) {
          ?>
            <div class="card-footer fixed-bottom">
            <button type="submit" id="botonGuardar" class="<?=$buttonCeleste;?>" disabled="true">Guardar</button>
            <a href='<?= $urlList; ?>'  rel="tooltip" class="<?=$buttonCancel;?>">Volver      
            </a>
            </div>
          <?php
          }
          ?>
  </form>

        </div>
      </div>
    </div>
  </div>

  
<?php 
}else{
  $stmtDist = $dbh->prepare("SELECT nombre, cod_uo from distribucion_gastosarea where codigo=$codigo");
  $stmtDist->execute();
  $resultDist=$stmtDist->fetch();
  $nombre_d=$resultDist['nombre'];
  $cod_oficina=$resultDist['cod_uo'];

  ?>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
          <form name="form1" id="form1" action="<?=$urlSaveRegisterDistribucionGastos;?>" method="POST">
          <input class="form-control" type="hidden" name="codigo" id="codigo" value="<?=$codigo?>"/>       
            <div class="card">
              <div class="card-header <?= $colorCard; ?> card-header-icon">
                <div class="card-icon">
                  <i class="material-icons"><?= $iconCard; ?></i>
                </div>
                <h4 class="card-title">Editar Distribución x Á
                rea</h4>
                <!-- <h6 class="card-title">Por favor active la casilla para registrar el Area</h6> -->            
              </div>
              <div class="card-body">
                <div class="row">
                  <label class="col-sm-2 col-form-label" >Nombre Distribución:</label>
                  <div class="col-sm-3">
                  <div class="form-group">
                    <input class="form-control" type="text" name="nombre_d" id="nombre_d" value="<?=$nombre_d?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                  </div>
                  </div>
                  <label class="col-sm-2 col-form-label" style="color:#0B2161;font-size: 15px">Oficina:</label>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <select class="selectpicker form-control form-control-sm" name="unidad" id="unidad" data-style="<?=$comboColor;?>" >
                        <option disabled selected="selected" value="">Oficina</option>
                      <?php
                        $stmtOf = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 order by 2");
                        $stmtOf->execute();
                        while ($rowOf = $stmtOf->fetch(PDO::FETCH_ASSOC)) {
                          $codigoX=$rowOf['codigo'];
                          $nombreX=$rowOf['nombre'];
                          $abrevX=$rowOf['abreviatura'];
                      ?>
                        <option value="<?=$codigoX;?>" <?=($cod_oficina==$codigoX)?"selected":"";?>><?=$nombreX;?></option> 
                      <?php
                        }
                      ?>
                      </select>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <?php
                      if ($globalAdmin == 1) {
            ?>
              <div class="card-footer fixed-bottom">
              <button type="submit" id="botonGuardar" class="<?=$buttonCeleste;?>">Guardar</button>
              <a href='<?= $urlList; ?>'  rel="tooltip" class="<?=$buttonCancel;?>">Volver
                  
              </a>
              </div>
            <?php
            }
            ?>
            </form>

          </div>
        </div>
      </div>
    </div>

  
<?php }


?>



