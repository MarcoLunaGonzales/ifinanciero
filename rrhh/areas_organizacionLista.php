<?php

    require_once 'conexion.php';
    require_once 'configModule.php'; //configuraciones
    require_once 'styles.php';

    $globalAdmin=$_SESSION["globalAdmin"];

    $dbh = new Conexion();

    //SELECT
    $stmt = $dbh->prepare("select a.*, uo.nombre as xunidad_org, ar.nombre as xarea, arpadre.nombre as xareapadre 
    from areas_organizacion a, unidades_organizacionales uo, areas ar, areas arpadre 
    where a.cod_unidad = uo.codigo and a.cod_area = ar.codigo and a.cod_areaorganizacion_padre = arpadre.codigo");
    //ejecutamos
    $stmt->execute();
    //bindColumn
    $stmt->bindColumn('codigo', $codigo);
    $stmt->bindColumn('cod_unidad', $cod_unidad);
    $stmt->bindColumn('cod_area', $cod_area);
    $stmt->bindColumn('cod_areaorganizacion_padre', $cod_areaorganizacion_padre);
    $stmt->bindColumn('cod_estadoreferencial', $cod_estadoreferencial);
    $stmt->bindColumn('created_at', $created_at);
    $stmt->bindColumn('created_by', $created_by);
    $stmt->bindColumn('modified_at', $modified_at);
    $stmt->bindColumn('modified_by', $modified_by);
    $stmt->bindColumn('xunidad_org', $xunidad_org);
    $stmt->bindColumn('xarea', $xarea);
    $stmt->bindColumn('xareapadre', $xareapadre);
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
                  <h4 class="card-title"><?=$nombrePluralAreas_organizacion?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                    <thead>
    <tr>
        <th>Codigo</th>
        <th>Unidad</th>
        <th>Area</th>
        <th>Area Padre</th>
       
        
        
        <th></th>
    </tr>
</thead>
<tbody>
<?php $index=1;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <tr>
    <td><?=$codigo;?></td>
        <td><?=$xunidad_org;?></td>
        <td><?=$xarea;?></td>
        <td><?=$xareapadre;?></td>
     
        
        <td class="td-actions text-right">
        <?php
          if($globalAdmin==1){
        ?>
          <a href='<?=$urlFormAreas_organizacion;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormAreas_organizacion;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
