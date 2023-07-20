<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sql="SELECT a.codigo, UPPER(a.nombre) as nombre, a.abreviatura, a.observaciones, UPPER(IFNULL(apadre.nombre, '-')) AS nombre_padre
      FROM areas a
      LEFT JOIN areas apadre ON apadre.codigo = a.cod_padre
      WHERE a.cod_estado != 1
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
                  <h4 class="card-title"><?=$nombrePluralAreas?> - Inactivo</h4>
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
                    <a class="btn btn-danger" href="index.php?opcion=areasLista"><i class="material-icons">arrow_back</i> Volver</a>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
</div>