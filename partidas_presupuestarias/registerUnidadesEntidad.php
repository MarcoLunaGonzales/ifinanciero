<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$table="entidades_uo";
$moduleName="Configurar Oficinas por Entidad";

$codEntidad=$codigo;


// Preparamos
$stmtEntidad = $dbh->prepare("SELECT nombre from entidades where codigo=$codEntidad");
$stmtEntidad->execute(); 
// bindColumn
$result = $stmtEntidad->fetch();
$nombreEntidad = $result['nombre'];


// Preparamos
$sql="SELECT u.codigo,u.nombre,
(select count(*) from entidades_uo ao where u.codigo=ao.cod_uo and ao.cod_entidad in ($codEntidad)) as bandera
from unidades_organizacionales u
where cod_estado=1 order by 2";

//echo $sql;

$stmtArea = $dbh->prepare($sql);
// Ejecutamos
$stmtArea->execute(); 
// bindColumn
$stmtArea->bindColumn('codigo', $codigoUO);
$stmtArea->bindColumn('nombre', $nombreUO);
$stmtArea->bindColumn('bandera', $bandera);

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
                  <h6 class="card-title">Oficina: <?=$nombreEntidad;?></h6>
                  <h6 class="card-title">Por favor active la casilla para registrar La Oficina</h6>
                </div>
                <div class="card-body">
                  
                  <div class="table-responsive">
                    <form id="lñp" method="post" action="<?=$urlSaveundiades_entidad;?>">
                      <input type="hidden" name="codEntidad" value="<?=$codEntidad?>">
                      <table class="table" id="data_cuentas" >
                        <thead>
                          <tr>
                            <th class="text-center">-</th>
                            <th>Codigo Oficina</th>
                            <th>Nombre Oficina</th>                            
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $index=1;$nc=0;
                        	while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) {
                            $nombreX=trim($nombreUO);  
                            if($bandera>0){                                            
                              $data[$nc][0]=$index;
                              $data[$nc][1]=$nombreX;
                              $nc++;                              
                            }
                              //$nombre_areaX=formateaPlanCuenta($nombre_areaX,$nivel);
                          ?>
                          <tr>
                            <td align="center">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input  class="form-check-input" type="checkbox" id="unidades<?=$index?>" name="unidades<?=$index;?>" onclick="sendChekedUnidadesEntidad(<?=$index?>,'<?=$nombreX?>')"  value="<?=$codigoUO?>" <?=($bandera>0)?"checked":"";?> >
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td><?=$codigoUO;?></td>
                            <td><?=$nombreUO;?></td>
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
                    <a href="<?=$urlListEntidades;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>

                    <a href="#" id="boton_registradasA" onclick="filaTablaUnidadEntidad($('#tablasU_registradas'));" class="btn btn-warning text-dark">Unidades Registradas <span class='badge bg-white text-warning'> <?=$nc?></span></a>
                </div>
			     </form>
            </div>
          </div>  
        </div>
    </div>

<?php 
for ($i=0; $i < $nc; $i++) { 
  ?><script>unidades_tabla.push({codigo:<?=$data[$i][0]?>,nombre:'<?=$data[$i][1]?>'});</script><?php
}

?>
<script>numFilasUE=<?=$nc?>;</script> 

<?php 
require_once 'partidas_presupuestarias/modal.php';
?>