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
$sql="SELECT codigo,nombre, abreviatura
      from areas
      where cod_estado=1
      ORDER BY 2";
$stmtArea = $dbh->prepare($sql);
// Ejecutamos
$stmtArea->execute(); 
// bindColumn
$stmtArea->bindColumn('codigo', $codigoArea);
$stmtArea->bindColumn('nombre', $nombreArea);
$stmtArea->bindColumn('abreviatura', $abreviatura);

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
                    <form id="formAreas" method="post" action="<?=$urlSaveAreas_organizacion;?>">
                      <input type="hidden" name="codUnidad" value="<?=$codUnidad?>">
                      <table class="table" id="data_cuentas">
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
                              // $nombreX=trim($nombre);$numeroX=trim($numero);
                              // if($bandera>0){
                              // $data[$nc][0]=$index;$data[$nc][1]=$nombreX;$data[$nc][2]=$numeroX;                           
                              // $nc++;
                              // }
                              // $nombre=formateaPlanCuenta($nombre,$nivel);
                          ?>
                          <tr>
                            <td align="center">
                              <div class="form-check">                                
                                  <input  type="checkbox" name="areas[]" value="<?=$codigoArea?>" >
                              </div>
                            </td>
                            <td><?=$codigoArea;?></td>
                            <td><?=$nombreArea;?></td>
                            <td>
                              <select>
                                <option>TECNOLOGIA</option>
                                <option>TIC</option>
                                <option>ADMIN</option>
                                <option>CONTA</option>
                                <option>RRHH</option>
                              </select>
                            </td>
                          </tr>

                          <?php
                							$index++;
                						}
                          ?>

                        </tbody>
                      </table>
                  </div>
                </div>
              </div>
        				<div class="card-footer fixed-bottom">
                    <button class="btn" type="submit">Guardar</button>
                    <a href="<?=$urlList2;?>" class="<?=$buttonCancel;?>">Cancelar</a>
                    <a href="#" onclick="filaTabla($('#tablas_registradas'));" id="boton_registradas" class="btn btn-warning text-dark">Areas Registradas <span class='badge bg-white text-warning'> <?=$nc?></span></a>
                </div>
			     </form>
            </div>
          </div>  
        </div>
    </div>
<?php 
