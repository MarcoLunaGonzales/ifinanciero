<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_tipo_cargo,
  (select tc.nombre from tipos_cargos_personal tc where tc.codigo=cod_tipo_cargo)as nombre_tipo_cargo
 from cargos where cod_estadoreferencial=1 order by nombre");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('cod_tipo_cargo', $cod_tipo_cargo);
$stmt->bindColumn('nombre_tipo_cargo', $nombre_tipo_cargo);


/********************************/
/*    DATOS DE TABLA EXTERNA    */
/********************************/
// CODIGO GET: Config Aprobación
$cod_aprobacion = $codigo;
$acc_sql = "SELECT acc.codigo,
              acc.nombre
              FROM  aprobacion_configuraciones_cargos acc 
              WHERE acc.codigo = :codigo";
$acc_stmt = $dbh->prepare($acc_sql);
$acc_stmt->bindParam(':codigo', $cod_aprobacion);
$acc_stmt->execute();
while ($row = $acc_stmt->fetch(PDO::FETCH_ASSOC)) {
	$edit_codigo = $row['codigo'];
	$edit_nombre = $row['nombre'];
}
/************************************/
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> - card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"> <b><?=$edit_nombre;?> - </b>Lista de Cargos</h4>                  
                  <h4 align="right" >
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th>#</th>                        
                          <th>Nombre</th>
                          <th>Abreviatura</th>
                          <th>Tipo Cargo</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$nombre;?></td>
                              <td><?=$abreviatura;?></td>        
                              <td><?=$nombre_tipo_cargo;?></td>
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <!-- REPSONSABILIADES -->
                                <a href='index.php?opcion=configuracionCargosFunciones&codigo=<?=$codigo;?>&cod_config_aprobacion=<?=$edit_codigo;?>' rel="tooltip" class="btn btn-warning" title="Responsabilidades del Cargo">
                                  <i class="material-icons">assignment</i>
                                </a>
                                <!-- AUTORIDADES -->
                                <a href='index.php?opcion=configuracionCargosAutoridades&codigo=<?=$codigo;?>&cod_config_aprobacion=<?=$edit_codigo;?>' rel="tooltip" class="btn btn-primary" title="Autoridades del Cargo">
                                  <i class="material-icons">list</i>
                                </a>
                                <?php
                                  }
                                ?>
                              
                              </td>
                          </tr>
                        <?php $index++; } ?>
                      </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
						    <a href="<?=$urlList;?>" class="btn btn-default">Volver</a>
              </div>
            </div>
          </div>  
        </div>
    </div>
