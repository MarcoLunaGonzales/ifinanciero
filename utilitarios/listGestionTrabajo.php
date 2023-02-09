<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="utilitarios/saveGestionTrabajo.php" method="post">
              <div class="card">
                <div class="card-header card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Cambiar Gestion de Trabajo</h4>
                </div>
                
                <div class="card-body">
                  <div class="">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Gestion</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td align="center">
                            <select class="selectpicker" data-style="<?=$comboColor;?>" data-show-subtext="true" data-live-search="true"  title="Seleccione una opcion" name="gestion" id="gestion" required>
                              <option disabled selected value=""></option>
                              <?php
                              $stmt = $dbh->prepare("SELECT g.codigo, g.nombre FROM gestiones g where g.cod_estado=1 order by 2 desc");
                              $stmt->execute();
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $codigoX=$row['codigo'];
                                $nombreX=$row['nombre'];
                              ?>
                              <option value="<?=$codigoX;?>"><?=$nombreX;?></option>
                              <?php 
                              }
                                ?>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="card-body">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
              </div>
               </form>
            </div>
          </div>  
        </div>
    </div>
