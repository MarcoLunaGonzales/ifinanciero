<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,
  (select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal)as personal,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo)as uo,( select a.abreviatura from areas a where a.codigo=cod_area) as area from tipos_caja_chica where cod_estadoreferencial=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_uo', $cod_uo);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('uo', $uo);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('personal', $personal);

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
                  <h4 class="card-title"><?=$nombrePluralTiposCajaChica?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th>#</th>                        
                          <th>Nombre</th>
                          <th>Oficina</th>
                          <th>Area</th>
                          <th>Responsable</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$nombre;?></td>
                              <td><?=$uo;?></td>
                              <td><?=$area;?></td>        
                              <td><?=$personal;?></td>        
                              
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <a href='<?=$urlFormTiposCajaChica;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteTiposCajaChica;?>&codigo=<?=$codigo;?>')">
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormTiposCajaChica;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
