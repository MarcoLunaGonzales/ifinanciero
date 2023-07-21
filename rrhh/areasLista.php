<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sql="SELECT a.codigo, UPPER(a.nombre) as nombre, a.abreviatura, a.observaciones, UPPER(IFNULL(apadre.nombre, '-')) AS nombre_padre
      FROM areas a
      LEFT JOIN areas apadre ON apadre.codigo = a.cod_padre
      WHERE a.cod_estado = 1
      ORDER BY a.nombre ASC";
$stmt = $dbh->prepare($sql);

//echo $sql;
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('nombre_padre', $nombre_padre);
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
                  <h4 class="card-title"><?=$nombrePluralAreas?></h4>
                  <h4 align="right">
                    <!-- Lista de Inactivos -->
                    <a style="height:10px;width: 10px; color: #ffffff;background-color: #f44336;border-radius: 3px;border: 2px solid #f44336;" href="?opcion=areasListaInactivo" title="Areas Inactivos">
                      <i class="material-icons">list</i>
                    </a>
                  </h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                      <tr>
                          
                          <th>#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Abreviatura</th>
                          <th>Dependencia</th>
                          <th>Observaciones</th>
                          
                          <th></th>
                      </tr>
                  </thead>
                  <tbody>
                  <?php $index=1;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                      <tr>
                        <td><?=$index;?></td>
                        <td><?=$codigo;?></td>
                        <td><?=$nombre;?></td>
                        <td><?=$abreviatura;?></td>
                        <td><?=$nombre_padre;?></td>
                        <td><?=$observaciones;?></td>
                          
                          <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>
                            <!-- Asignación de Cargos -->
                            <a href="index.php?opcion=registerAreasCargos&codigo=<?=$codigo;?>" rel="tooltip" class="btn btn-primary" data-original-title="" title="">
                              <i class="material-icons" title="Asignación de Cargos">playlist_add</i>
                            </a>
                            <!-- Mapa Cargos -->
                            <button rel="tooltip" class="btn btn-info mapa_cargos" data-cod_area="<?=$codigo;?>" data-nombre_area="<?=$nombre;?>" title="Ver mapa de cargos por área">
                              <i class="material-icons">map</i>
                            </button>

                            <a href='<?=$urlFormAreas;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteAreas;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormAreas;?>&codigo=0'">Registrar</button>
                    <!-- Mapa de Areas -->
                    <a class="btn btn-info" href="index.php?opcion=areasMapa"><i class="material-icons">map</i> Mapa áreas</a>
                    <!-- Mapa de Areas Personal -->
                    <a class="btn btn-warning" href="index.php?opcion=areasMapaPersonal"><i class="material-icons">map</i> Mapa personal</a>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
</div>


<!-- MAPA -->
<div class="modal fade modal-primary" id="modalMapa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card">
            <div class="card-header card-header-danger card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">list</i>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                </button>
                <h4 class="card-title">Mapa de Cargos por Area</h4>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h5 class="text-primary">
                        <i class="material-icons">business</i> <b id="title_ar">ÁREA</b>
                    </h5>
                </div>
                <div id="treeContainer"></div>
            </div>
        </div>  
    </div>
</div>
<!-- LIBRERIA PARA ESTRUCTURA ARBOL -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

<script>
    /**
     * Funcionalidad para generar el arbol de los CARGOS por AREA
     */
    $('.mapa_cargos').on('click', function() {
        let cod_area    = $(this).data('cod_area');
        let nombre_area = $(this).data('nombre_area');
        $('#title_ar').html(nombre_area);
        $.ajax({
            url: 'rrhh/ajaxMapaCargosAreas.php',
            method: 'POST',
            dataType: 'json',
            data:{
                cod_area: cod_area
            },
            success: function(response) {
                console.log(response)
                // Modificar la estructura de datos para asignar iconos diferentes
                response.data.forEach(function(node) {
                    if (node.children.length > 0) {
                        node.icon = 'jstree-folder';
                    } else {
                        node.icon = 'jstree-file';
                    }
                });
                
                // Construir el árbol
                $('#treeContainer').jstree('destroy').jstree({
                    core: {
                    data: response.data
                    },
                    plugins: ['themes', 'types'],
                    types: {
                        folder: {
                            icon: 'jstree-folder' // Icono para los nodos de carpeta
                        },
                        file: {
                            icon: 'jstree-tree' // Icono para los nodos de archivo
                        }
                    }
                });
                $('#modalMapa').modal('show');
                // Abrir todas las pestañas del árbol
                setTimeout(function() {
                    $('#treeContainer').jstree('open_all');
                }, 2000);
            },
            error: function() {
                console.log('Error al obtener los datos');
            }
        });
    });

</script>

