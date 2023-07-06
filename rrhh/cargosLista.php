<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT c.codigo,UPPER(c.nombre) as nombre,c.objetivo,c.abreviatura,c.cod_tipo_cargo,
                  (SELECT tc.nombre FROM tipos_cargos_personal tc WHERE tc.codigo=c.cod_tipo_cargo) AS nombre_tipo_cargo,
                  UPPER(cpadre.nombre) nombre_dependencia
                FROM cargos c
                LEFT JOIN cargos cpadre ON cpadre.codigo = c.cod_padre
                WHERE c.cod_estadoreferencial=1 ORDER BY c.nombre");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('objetivo', $objetivo);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('cod_tipo_cargo', $cod_tipo_cargo);
$stmt->bindColumn('nombre_tipo_cargo', $nombre_tipo_cargo);
$stmt->bindColumn('nombre_dependencia', $nombre_dependencia);

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
                  <h4 class="card-title"><?=$nombrePluralCargos?></h4>                  
                  <h4 align="right" >
                <a  style="height:10px;width: 10px; color: #ffffff;background-color: #1883ba;border-radius: 3px;border: 2px solid #1883ba;" href='<?=$urlCargoEscalaSalarialGeneral;?>' >
                  <i class="material-icons" title="Lista Escala Salarial General">trending_up</i>
                </a>  
                <!-- Lista de Cargos Inactivos -->
                <a  style="height:10px;width: 10px; color: #ffffff;background-color: #f44336;border-radius: 3px;border: 2px solid #f44336;" href='?opcion=cargosListaInactivo' title="Cargos Inactivos">
                  <i class="material-icons">list</i>
                </a>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th width="10">#</th>                        
                          <th width="150">Nombre</th>
                          <th width="230">Objetivo</th>
                          <th width="10">Abreviatura</th>
                          <th width="10">Nivel del Cargo</th>
                          <th width="10">Dependencia</th>
                          <th width="80"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>
                            <td><?=$nombre;?></td>
                            <td><?=strlen($objetivo) > 100 ? (substr($objetivo, 0, 100) . "...") : $objetivo;?></td>
                              <td><?=$abreviatura;?></td>
                              <td><?=$nombre_tipo_cargo;?></td>
                              <td><?=$nombre_dependencia;?></td>
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <!-- Responsabilidades -->
                                <a href='<?=$urlCargosFunciones;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-warning" title="Responsabilidades del Cargo">
                                  <i class="material-icons">assignment</i>
                                </a>
                                <!-- Autoridades -->
                                <a href='index.php?opcion=cargosAutoridades&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-info" title="Autoridades del Cargo">
                                  <i class="material-icons">list</i>
                                </a>

                                <a href='<?=$urlCargosEscalaSalarial;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-primary">
                                    <i class="material-icons" title="Escala Salarial">trending_up</i>
                                </a>
                                <!-- Reporte PDF -->
                                <a href='rrhh/pdfGeneracion.php?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-default" title="Manual de Cargo">
                                  <i class="material-icons">archive</i>
                                </a>

                                <a href='<?=$urlFormCargos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteCargos;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                                </button>
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
