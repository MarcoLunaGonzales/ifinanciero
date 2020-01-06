<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$table="partidaspresupuestarias_cuentas";
$moduleName="Configurar Areas por Unidad Organizacional";

$codUnidad=$codigo;


// Preparamos
$stmtUnidad = $dbh->prepare("SELECT nombre
      from unidades_organizacionales
      where codigo=:codigo");
$stmtUnidad->bindParam(':codigo',$codUnidad);
// Ejecutamos
$stmtUnidad->execute(); 
// bindColumn
$result = $stmtUnidad->fetch();
$nombreUnidad = $result['nombre'];


// Preparamos
$sql="SELECT a.codigo,a.nombre,
(select count(*) from areas_organizacion ao where a.codigo=ao.cod_area and ao.cod_unidad in ($codUnidad)) as bandera,
(select ao.cod_areapadre from areas_organizacion ao where a.codigo=ao.cod_area and ao.cod_unidad in ($codUnidad)) as cod_area_padre
from areas a
where cod_estado=1 order by 2";

//echo $sql;

$stmtArea = $dbh->prepare($sql);
$stmtArea->bindParam(':codigo',$codUnidad);
// Ejecutamos
$stmtArea->execute(); 
// bindColumn
$stmtArea->bindColumn('codigo', $codigoArea);
$stmtArea->bindColumn('nombre', $nombreArea);
$stmtArea->bindColumn('bandera', $bandera);
$stmtArea->bindColumn('cod_area_padre', $codAreaPadre);

?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title"><?=$moduleName?></h4>
                  <h6 class="card-title">Unidad Organizacional: <?=$nombreUnidad;?></h6>
                  <h6 class="card-title">Por favor active la casilla para registrar el Area</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <form id="lñp" method="post" action="<?=$urlSaveAreas_organizacion;?>">
                      <input type="hidden" name="codUnidad" value="<?=$codUnidad?>">
                      <table class="table" id="data_cuentas" >
                        <thead>
                          <tr>
                            <th class="text-center">-</th>
                            <th>Codigo Area</th>
                            <th>Nombre Area</th>
                            <th>Nombre Area Padre</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $index=1;$nc=0;
                        	while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) {
                            if($bandera>0){              
                              $nc++;
                            }
                              //$nombre_areaX=formateaPlanCuenta($nombre_areaX,$nivel);
                          ?>
                          <tr>
                            <td align="center">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input  class="form-check-input" type="checkbox" id="areas<?=$index?>" name="areas<?=$index;?>" onclick="sendChekedA(<?=$index?>,<?=$nc?>)"  value="<?=$codigoArea?>" <?=($bandera>0)?"checked":"";?> >
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td><?=$codigoArea;?></td>
                            <td><?=$nombreArea;?></td>
                            <td >
                              
                                <?php
                                //listados todas las area para padre

                                $sql2="SELECT codigo,nombre, abreviatura
                                from areas
                                where cod_estado=1
                                ORDER BY 2";
                                $stmtArea2 = $dbh->prepare($sql2);
                                $stmtArea2->execute(); 
                                $stmtArea2->bindColumn('codigo', $codigoArea2);
                                $stmtArea2->bindColumn('nombre', $nombreArea2);
                                $stmtArea2->bindColumn('abreviatura', $abreviatura2);
                              ?>                              
                              <select name="cod_areaorganizacion_padre<?=$index;?>" id="cod_areaorganizacion_padre" class="selectpicker" data-style="btn btn-primary" data-live-search="true">
                                    <option value="">-</option>
                                    <?php while ($row = $stmtArea2->fetch()){ ?>
                                        <option <?=($codAreaPadre==$codigoArea2)?"selected":"";?>  value="<?=$codigoArea2;?>"><?=$nombreArea2;?></option>
                                    <?php } ?>
                              </select>
                              

                              
                            </td>
                          </tr>

                          <?php
                							$index++;
                						}
                          ?>
                        </tbody>
                      </table>
                      <input type="hidden" name="numero_filas" value="<?=$index;?>">
                  </div>
                </div>
              </div>
        				<div class="card-footer fixed-bottom">
                    <button class="btn" type="submit">Guardar</button>
                    <a href="<?=$urlListUO;?>" class="<?=$buttonCancel;?>">Cancelar</a>

                    <a href="#" id="boton_registradasA" class="btn btn-warning text-dark">Areas Registradas <span class='badge bg-white text-warning'> <?=$nc?></span></a>
                </div>
			     </form>
            </div>
          </div>  
        </div>
    </div>
<script>numFilasA=<?=$nc?>;</script> 



