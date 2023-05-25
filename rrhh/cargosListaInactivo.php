<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_tipo_cargo,
  (select tc.nombre from tipos_cargos_personal tc where tc.codigo=cod_tipo_cargo)as nombre_tipo_cargo
 from cargos where cod_estadoreferencial=2 order by nombre");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('cod_tipo_cargo', $cod_tipo_cargo);
$stmt->bindColumn('nombre_tipo_cargo', $nombre_tipo_cargo);

?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$nombrePluralCargos?> - Inactivos</h4>                  
                  <h4 align="right" >
                <a  style="height:10px;width: 10px; color: #ffffff;background-color: #4caf50;border-radius: 3px;border: 2px solid #4caf50;" href='?opcion=cargosLista'  title="Cargos Activos">
                <i class="material-icons">list</i>
              </a>  
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
                                <a href='<?=$urlCargosFunciones;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-warning">
                                  <i class="material-icons" title="Responsabilidades del Cargo">assignment</i>
                                </a>

                                <a href='<?=$urlCargosEscalaSalarial;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-primary">
                                    <i class="material-icons" title="Escala Salarial">trending_up</i>
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
              <?php

              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormCargos;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
