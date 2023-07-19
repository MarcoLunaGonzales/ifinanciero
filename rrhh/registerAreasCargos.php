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
$moduleName="Configurar Areas por Oficina";

$codArea=$codigo;


// Preparamos
$stmtArea = $dbh->prepare("SELECT nombre FROM areas a where codigo=:codigo");
$stmtArea->bindParam(':codigo',$codArea);
// Ejecutamos
$stmtArea->execute(); 
// bindColumn
$result = $stmtArea->fetch();
$nombreCargo = $result['nombre'];


// Preparamos
$sql="SELECT c.codigo, UPPER(c.nombre) as nombre,
    (SELECT count(*) FROM cargos_areasorganizacion ca WHERE c.codigo=ca.cod_cargo AND ca.cod_areaorganizacion='$codArea') AS bandera,
    UPPER(IFNULL(cpadre.nombre, '-')) as dependencia
    FROM cargos c
    LEFT JOIN cargos cpadre ON cpadre.codigo = c.cod_padre
    WHERE c.cod_estadoreferencial = 1 ORDER BY 2";

$stmtCategoria = $dbh->prepare($sql);
$stmtCategoria->bindParam(':codigo',$codArea);
$stmtCategoria->execute();

$stmtCategoria->bindColumn('codigo', $codigoCargo);
$stmtCategoria->bindColumn('nombre', $nombreCargo);
$stmtCategoria->bindColumn('bandera', $bandera);
$stmtCategoria->bindColumn('dependencia', $dependencia);

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
                </div>
                <div class="card-header card-header-primary card-header-icon text-center">
                  <h4 class="card-title">Configurar <b>Cargos</b> por Areas</h4>
                  <h6 class="card-title">Area: <b class="text-success"><?=$nombreCargo;?></b></h6>
                  <h6 class="card-title">Por favor active la casilla para registrar la categoria</h6>
                </div>
                <div class="card-body">
                  
                  <div class="table-responsive">
                    <form id="lñp" method="post" action="?opcion=areasCargosSave">
                      <input type="hidden" name="codArea" value="<?=$codArea?>">
                      <table class="table" id="data_cuentas" >
                        <thead>
                          <tr>
                            <th class="text-center">-</th>
                            <th>Codigo Cargos</th>
                            <th>Nombre Cargos</th>
                            <th>Dependencia</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $index=1;$nc=0;
                        	while ($row = $stmtCategoria->fetch(PDO::FETCH_BOUND)) {
                            if($bandera>0){              
                              $nc++;
                            }
                          ?>
                          <tr>
                            <td align="center">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input  class="form-check-input" type="checkbox" id="cargos<?=$index?>" name="cargos<?=$index;?>" onclick="sendChekedA(<?=$index?>,<?=$nc?>)"  value="<?=$codigoCargo?>" <?=($bandera>0)?"checked":"";?> >
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td><?=$codigoCargo;?></td>
                            <td><?=$nombreCargo;?></td>
                            <td><?=$dependencia;?></td>
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
                    <a href="<?=$urlListAreas;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>

                    <a href="#" id="boton_registradasA" class="btn btn-warning text-white">Cargos Registrados <span class='badge bg-white text-warning'> <?=$nc?></span></a>
                </div>
			     </form>
            </div>
          </div>  
        </div>
    </div>
<script>numFilasA=<?=$nc?>;</script> 



