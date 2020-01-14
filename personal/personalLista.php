<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();


$stmt = $dbh->prepare(" SELECT p.codigo,p.identificacion,p.cod_lugar_emision,p.paterno,p.materno,p.primer_nombre,p.bandera,
  p.ing_contr,
  (select c.nombre from cargos c where c.codigo=cod_cargo)as xcargo,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as xuonombre,
 (select a.abreviatura from areas a where a.codigo=cod_area)as xarea,
 (select ep.nombre from estados_personal ep where ep.codigo=cod_estadopersonal)as xestado,
 (select tp.nombre from tipos_personal tp where tp.codigo=cod_tipopersonal)as xcod_tipopersonal
 
 from personal p
 where p.cod_estadoreferencial=1 order by p.paterno, p.materno, p.primer_nombre
 ");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('identificacion', $ci);
$stmt->bindColumn('cod_lugar_emision', $ci_lugar_emision);
$stmt->bindColumn('paterno', $paterno);
$stmt->bindColumn('materno', $materno);
$stmt->bindColumn('primer_nombre', $primer_nombre);
$stmt->bindColumn('bandera', $bandera);

$stmt->bindColumn('ing_contr', $fecha_ingreso);
$stmt->bindColumn('xcargo', $xcargo);
$stmt->bindColumn('xuonombre', $xuonombre);
$stmt->bindColumn('xarea', $xarea);
$stmt->bindColumn('xestado', $xestado);
$stmt->bindColumn('xcod_tipopersonal', $xcod_tipopersonal);

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
              <h4 class="card-title"><?=$nombrePluralPersonal?></h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table" id="tablePaginator">
                  <thead>
                      <tr>
                        <th></th>
                        <th>Código</th>
                        <th>Nombre</th>      
                        <th>Ci</cIte></th>
                        <th>Cargo</th>
                        <th>Oficina-Area</th>                        
                        <th>Tipo Personal</th>                                                
                        <th>F.Ingreso</th>
                        <th>Estado</th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                      <tr>
                        <td  class="td-actions text-right">    
                          <a href='<?=$urlprintPersonal;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                            <i class="material-icons">print</i>
                          </a>
                        </td>
                        <td><?=$codigo?></td>
                        <td><?=$paterno;?> <?=$materno;?> <?=$primer_nombre;?></td>      
                        <td><?=$ci;?>-<?=$ci_lugar_emision;?></td>
                        <td><?=$xcargo;?></td>
                        <td><?=$xuonombre;?>-<?=$xarea;?></td>                        
                        <td><?=$xcod_tipopersonal;?></td>                                              
                        <td><?=$fecha_ingreso;?></td>
                        <td><?=$xestado;?></td>
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>
                            <a href='<?=$urlFormPersonalContratos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-info">
                              <i class="material-icons" title="Contratos">assignment</i>
                            </a>
                            <a href='<?=$urlFormPersonalAreaDistribucion;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-warning">            
                              <i class="material-icons" title="Area-Distribución" style="color:black;">call_split</i>
                            </a>
                            <?php
                              }
                            ?>        
                        </td>
                          
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>
                            <a href='<?=$urlFormPersonal;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeletePersonal;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                            <?php
                              }
                            ?>                      
                        </td>
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1 and $bandera==1){
                          ?>
                            <div class="dropdown">
                              <button class="btn btn-primary dropdown-toggle" type="button" id="editar_otros" data-toggle="dropdown" aria-extended="true">
                                <i class="material-icons" title="Editar"><?=$iconEdit;?></i>                        
                                <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu" role="menu" aria-labelledby="editar_otros">
                                <!-- <li role="presentation" class="dropdown-header"><small>U.O.</small></li> -->
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=1&codigo_p=<?=$codigo;?>'><small>Oficina/Area</small></a></li>
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=2&codigo_p=<?=$codigo;?>'><small>Cargo</small></a></li>
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=3&codigo_p=<?=$codigo;?>'><small>Grado Acad</small></a></li>
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=4&codigo_p=<?=$codigo;?>'><small>Haber Básico</small></a></li>
                              </ul>
                            </div>                            
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
                <!--<button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormPersonal;?>&codigo=0'">Registrar</button>-->
                <!--<button class="btn btn-success"  id="service">Web Service</button>-->
                <button class="btn btn-success"  onClick="location.href='<?=$urlsaveWSPersonal;?>'">Actualizar Datos</button>
          </div>
          <div id="resultados">
            <ul></ul>
          </div>
          <?php
          }
          ?>
  
        </div>
      </div>  
    </div>
</div>