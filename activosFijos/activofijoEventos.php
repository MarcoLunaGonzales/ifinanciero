<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

// $codigo = $_GET["codigo"];//codigoactivofijo

$sql="SELECT codigo,activo,(select p.nombre from personal2 p where p.codigo=cod_responsables_responsable)as cod_responsables_responsable,
(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional,
(select a.abreviatura from areas a where a.codigo=cod_area)as cod_area,
(select d.nombre from depreciaciones d where d.codigo = cod_depreciaciones)as cod_depreciaciones
from activosfijos
where codigo = :codigo";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->bindParam(':codigo',$codigo);
$stmt->execute();
//bindColumn
$result = $stmt->fetch();
$codigo = $result['codigo'];
$activo = $result['activo'];
$cod_responsables_responsable = $result['cod_responsables_responsable'];
$cod_unidadorganizacional = $result['cod_unidadorganizacional'];
$cod_area = $result['cod_area'];
$cod_depreciaciones = $result['cod_depreciaciones'];


$sql2="SELECT codigo as codigoEve,cod_activofijo,nombre,fecha,cod_estadoreferencial,cod_personalresponsable,
(select p.nombre from personal2 p where p.codigo=cod_personalresponsable)as nombre_personal
from eventos_af
where cod_activofijo = :codigo and cod_estadoreferencial=1";
$stmt2 = $dbh->prepare($sql2);
//ejecutamos
$stmt2->bindParam(':codigo',$codigo);
$stmt2->execute();

$stmt2->bindColumn('codigoEve', $codigoEve);
$stmt2->bindColumn('cod_activofijo', $cod_activofijo);
$stmt2->bindColumn('nombre', $nombreEve);
$stmt2->bindColumn('fecha', $fechaCreacion);
$stmt2->bindColumn('cod_estadoreferencial', $cod_estadoreferencial);
$stmt2->bindColumn('cod_personalresponsable', $cod_personalresponsable);
$stmt2->bindColumn('nombre_personal', $nombre_personal);


$query = "select * from personal2 order by 2";
$statementPersonal = $dbh->query($query);
$statementPersonal2 = $dbh->query($query);
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
                  <h3 class="card-title"><b>Eventos De Activos Fijos</b></h3>
                  <h5 class="card-title"><?=$activo;?></h5>
                  <h6 class="card-title"><?=$cod_unidadorganizacional;?> - <?=$cod_area;?></h6>
                  <h6 class="card-title"><?=$cod_depreciaciones;?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                            <th>Código Evento</th>
                            <th>Nombre</th>
                            <th>Fecha Creación</th>
                            <th>Personal Responsable</th>
                            <th></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $index=1;
                      while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) { 
                          $datos=$codigoEve."||".$nombreEve."||".$cod_personalresponsable;
                        ?>
                          <tr>
                              <td><?=$codigoEve;?></td>
                              <td><?=$nombreEve;?></td>
                              <td><?=$fechaCreacion;?></td>
                              <td><?=$nombre_personal;?></td>
                              <td class="td-actions text-right">
                                 <button class="btn btn-success" type="button" data-toggle="modal" data-target="#modalEditarEve" onclick="agregaformEve('<?=$datos;?>')">
                                    <i class="material-icons" title="Editar Accesorio AF"><?=$iconEdit;?></i>
                                 </button>
                                <button class="<?=$buttonDelete;?>" type="button" data-toggle="modal" data-target="#modalBorrarEve" onclick="agregaformEveB('<?=$datos;?>')">
                                  <i class="material-icons" title="Borrar Accesorio AF"><?=$iconDelete;?></i>
                                </button>                              
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
                <button class="<?=$buttonNormal;?>" type="button" data-toggle="modal" data-target="#modalRegistrarEve">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>

<div class="modal fade" id="modalRegistrarEve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Registrar Evento Para AF</h4>
      </div>
      <div class="modal-body">
        
        <label> Nombre Del Evento</label><br>
        <input type="text" name="nombreEve" id="nombreEve" class="form-control input-sm"><br>
        <label> Nombre Del Responsable</label><br>
        <select name="personalEve" id="personalEve" class="selectpicker" data-style="btn btn-primary" >
            <?php while ($row = $statementPersonal->fetch()){ ?>
                <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
            <?php } ?>
        </select>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarEveAF"  data-dismiss="modal">Registrar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditarEve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Evento Para AF</h4>
      </div>
      <div class="modal-body">
        <label> codigo Evento: </label><br>
        <input type="text" name="idEveE" id="idEveE" class="form-control input-sm" readonly="readonly"><br>
        <label> Nombre Del Evento: </label><br>
        <input type="text" name="nombreEveE" id="nombreEveE" class="form-control input-sm"><br>
        <label> Nombre Del Responsable</label><br>
        <select name="personalEveE" id="personalEveE" class="selectpicker" data-style="btn btn-primary" >
            <?php while ($row = $statementPersonal2->fetch()){ ?>
                <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
            <?php } ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="saveEditEveAF"  data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalBorrarEve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Estás seguro?</h4>
      </div>
      <div class="modal-body">      
        <input type="hidden" name="idEveE" id="idEveE" value="0">
        Esta acción Borrará el Evento Del Activo Fijo. Deseas continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="saveDeleteEveAF"  data-dismiss="modal">Continuar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarEveAF').click(function(){
      codigoAF=<?=$codigo?>;
      nombreEve=$('#nombreEve').val();
      personalEve=$('#personalEve').val();
      
      RegistrarEveAF(codigoAF,nombreEve,personalEve);
    });

    $('#saveEditEveAF').click(function(){
      codigoAF=<?=$codigo?>;
      idEveE=$('#idEveE').val();
      nombreEve=$('#nombreEveE').val();
      cod_personalE=$('#personalEveE').val();
      SaveEditEveAF(idEveE,codigoAF,nombreEve,cod_personalE);
    });
    $('#saveDeleteEveAF').click(function(){
      codigoAF=<?=$codigo?>;
      idEveE=$('#idEveE').val();
      SaveDeleteEveAF(idEveE,codigoAF);
    });

    
  });
</script>