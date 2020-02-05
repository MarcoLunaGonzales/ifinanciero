<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,
  (select t.nombre from tipos_doc_cajachica t where t.codigo=cod_tipodoc) as tipo_documento,
  (select e.nombre from estados_rendiciones e where e.codigo=cod_estado) as nombre_estado
from rendiciones 
where cod_estadoreferencial=1 and cod_personal=$globalUser");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo); 
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('tipo_documento', $tipo_documento);
$stmt->bindColumn('monto_a_rendir', $monto_a_rendir);
$stmt->bindColumn('monto_rendicion', $monto_rendicion);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('nombre_estado', $nombre_estado);
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
                  <h4 class="card-title">Mis Rendiciones</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th>#</th>                        
                          <th>F de rendición</th>
                          <th>Tipo</th>
                          <th>Monto a Rendir</th>
                          <th>Monto Rendición</th>
                          <th>Monto devuelto</th>
                          <th>Descripción</th>
                          <th>Estado</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                          if($monto_rendicion == 0 || $monto_rendicion ==null) $monto_devuelto=0;
                          else $monto_devuelto=$monto_a_rendir-$monto_rendicion;
                          
                          if($fecha==null){
                            $fecha="Sin Definir";
                          }
                          if($cod_estado==1)
                            $label='<span class="badge badge-danger">';
                          else
                            $label='<span class="badge badge-success">';
                          ?>

                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$fecha;?></td>
                              <td><?=$tipo_documento;?></td>
                              <td><?=$monto_a_rendir;?></td>        
                              <td><?=$monto_rendicion;?></td>
                              <td><?=$monto_devuelto;?></td>        
                              <td><?=$observaciones;?></td>        
                              <td><?=$label.$nombre_estado."</span>";?></td>
                              
                              <td class="td-actions text-right">
                              <?php
                                if($cod_estado==1){
                              ?>
                                <a href='<?=$urlListaRendicionesDetalle;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Rendición Detalle">add</i>
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
            </div>
          </div>  
        </div>
    </div>
