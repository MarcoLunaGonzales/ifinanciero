<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT af.*, d.nombre dep_nombre, tb.tipo_bien tb_tipo, u.edificio u_edificio, u.oficina u_oficina, 
(select afi.imagen from activosfijosimagen afi where af.codigo = afi.codigo) as imagen, (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area
from activosfijos af, depreciaciones d, tiposbienes tb, ubicaciones u 
where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo and af.cod_ubicaciones  = u.codigo 
and af.cod_estadoactivofijo = 1";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('codigoactivo', $codigoactivo);
$stmt->bindColumn('tipoalta', $tipoalta);
$stmt->bindColumn('fechalta', $fechalta);
$stmt->bindColumn('indiceufv', $indiceufv);
$stmt->bindColumn('tipocambio', $tipocambio);
$stmt->bindColumn('moneda', $moneda);
$stmt->bindColumn('valorinicial', $valorinicial);
$stmt->bindColumn('depreciacionacumulada', $depreciacionacumulada);
$stmt->bindColumn('valorresidual', $valorresidual);
$stmt->bindColumn('cod_depreciaciones', $cod_depreciaciones);//rubro
$stmt->bindColumn('cod_tiposbienes', $cod_tiposbienes);//tipo bien
$stmt->bindColumn('vidautilmeses', $vidautilmeses);
$stmt->bindColumn('estadobien', $estadobien);
$stmt->bindColumn('otrodato', $otrodato);
$stmt->bindColumn('cod_ubicaciones', $cod_ubicaciones);//ubicacion
$stmt->bindColumn('cod_empresa', $cod_empresa);//empresa
$stmt->bindColumn('activo', $activo);
$stmt->bindColumn('cod_responsables_responsable', $cod_responsables_responsable);
$stmt->bindColumn('cod_responsables_autorizadopor', $cod_responsables_autorizadopor);

$stmt->bindColumn('dep_nombre', $dep_nombre);
$stmt->bindColumn('tb_tipo', $tb_tipo);
$stmt->bindColumn('u_edificio', $u_edificio);
$stmt->bindColumn('u_oficina', $u_oficina);
$stmt->bindColumn('imagen', $imagen);

$stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('cod_estadoactivofijo', $cod_estadoactivofijo);
$stmt->bindColumn('nombre_unidad', $nombreUnidad);
$stmt->bindColumn('nombre_area', $nombreArea);

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
                  <h4 class="card-title"><?=$moduleNamePlural6?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                            <th></th>
                            <th>Codigo</th>
                            <th>Unidad/Area</th>
                            <th>Activo</th>
                            <th>F. Alta</th>
                          
                            <th>Rubro/Depr.</th>
                            <th>Tipo Bien</th>
                            <th>Estado bien</th>
                            <th>Ubicacion</th>
                            
                            
                            <th></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $index=1;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                              <td  class="td-actions text-right">
                              
                                
                                <a href='<?=$printDepreciacion1;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons">print</i>
                                </a>
                              </td>
                              <td><?=$codigoactivo;?></td>
                              <td><?=$nombreUnidad;?>-<?=$nombreArea;?></td>
                              <td><?=$activo;?></td>
                              <td><?=$fechalta;?></td>
                              <td><?=$dep_nombre;?></td>
                              <td><?=$tb_tipo;?></td>
                              <td><?=$estadobien;?></td>
                              <td><?=$u_oficina;?> <?=$u_edificio;?></td>
                              
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <a href='<?=$urlEdit6;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete2;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons"><?=$iconDelete;?></i>
                                </button>
                                <a href='<?=$urlEditTransfer;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonMorado;?>">
                                  <i class="material-icons">transfer_within_a_station</i>
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
                <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar_activosfijos;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
