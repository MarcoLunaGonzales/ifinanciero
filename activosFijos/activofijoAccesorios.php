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

$sql="SELECT codigo,activo,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable)as cod_responsables_responsable,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional,(select a.abreviatura from areas a where a.codigo=cod_area)as cod_area,(select d.nombre from depreciaciones d where d.codigo = cod_depreciaciones)as cod_depreciaciones
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


$sql2="SELECT codigo as codigoacc,cod_activofijo,nombre,
(select a.nombre from estados_accesoriosaf a where a.codigo=cod_estadoaccesorioaf)as nombre_estadoaccesorioaf,cod_estadoaccesorioaf
from accesorios_af
where cod_activofijo = :codigo and cod_estadoreferencialAcc=1";
$stmt2 = $dbh->prepare($sql2);
//ejecutamos
$stmt2->bindParam(':codigo',$codigo);
$stmt2->execute();

$stmt2->bindColumn('codigoacc', $codigoacc);
$stmt2->bindColumn('cod_activofijo', $cod_activofijo);
$stmt2->bindColumn('nombre', $nombreAcc);
$stmt2->bindColumn('nombre_estadoaccesorioaf', $cod_estadoaccesorioaf);
$stmt2->bindColumn('cod_estadoaccesorioaf', $cod_estadoAcceAF);
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
                  <h3 class="card-title"><b>Accesorios De Activos Fijos</b></h3>
                  <h5 class="card-title"><?=$activo;?></h5>
                  <h6 class="card-title"><?=$cod_unidadorganizacional;?> - <?=$cod_area;?></h6>
                  <h6 class="card-title"><?=$cod_depreciaciones;?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                            <th>Código Del Accesorio</th>
                            <th>Nombre</th>
                            <th>Estado Del Accesorio</th>
                            <th></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $index=1;
                      while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) { 
                          $datos=$codigoacc."||".$nombreAcc."||".$cod_estadoAcceAF;
                        ?>
                          <tr>
                              <td><?=$codigoacc;?></td>
                              <td><?=$nombreAcc;?></td>
                              <td><?=$cod_estadoaccesorioaf;?></td>
                              <td class="td-actions text-right">
                                
                                 <button class="btn btn-success" type="button" data-toggle="modal" data-target="#modalEditarAcc" onclick="agregaformAcc('<?=$datos;?>')">
                                    <i class="material-icons" title="Editar Accesorio AF"><?=$iconEdit;?></i>
                                 </button>

                                <button class="<?=$buttonDelete;?>" type="button" data-toggle="modal" data-target="#modalBorrarAcc" onclick="agregaformAccB('<?=$datos;?>')">
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
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->

                <button class="<?=$buttonNormal;?>" type="button" data-toggle="modal" data-target="#modalRegistrarAcc">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>

<div class="modal fade" id="modalRegistrarAcc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Registrar Accesorios Del AF</h4>
      </div>
      <div class="modal-body">
        
        <label> Nombre</label><br>
        <input type="text" name="nombreAcc" id="nombreAcc" class="form-control input-sm"><br>
        <label> Estado Del Accesorio</label><br>
        <select name="estadoAcc" id="estadoAcc" class="selectpicker" data-style="btn btn-primary" >
            <option value="1">BUENO</option>
            <option value="2">REGULAR</option>
            <option value="3">MALO</option>
            <option value="4">EXTRAVIADO</option>
            <option value="5">DADO DE BAJA</option>
        </select>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarAccAF"  data-dismiss="modal">Registrar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditarAcc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Accesorios Del AF</h4>
      </div>
      <div class="modal-body">
        <label> codigo Accesorio: </label><br>
        <input type="text" name="idAccE" id="idAccE" class="form-control input-sm" readonly="readonly"><br>
        <label> Nombre</label><br>
        <input type="text" name="nombreAccE" id="nombreAccE" class="form-control input-sm"><br>
        <label> Estado Del Accesorio</label><br>
        <select name="estadoAccE" id="estadoAccE" class="selectpicker" data-style="btn btn-primary" >
            <option value="1">BUENO</option>
            <option value="2">REGULAR</option>
            <option value="3">MALO</option>
            <option value="4">EXTRAVIADO</option>
            <option value="5">DADO DE BAJA</option>
        </select>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="saveEditAccAF"  data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalBorrarAcc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">¿Estás seguro?</h4>
      </div>
      <div class="modal-body">      
        <input type="hidden" name="idAccE" id="idAccE" value="0">
        Esta acción Borrará el Accesorio del AF. ¿Deseas continuar?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="saveDeleteAccAF"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarAccAF').click(function(){
      codigoAF=<?=$codigo?>;
      nombreAcc=$('#nombreAcc').val();
      estadoAcc=$('#estadoAcc').val();

      RegistrarAccAF(codigoAF,nombreAcc,estadoAcc);
    });

    $('#saveEditAccAF').click(function(){
      codigoAF=<?=$codigo?>;
      idAccE=$('#idAccE').val();
      nombreAcc=$('#nombreAccE').val();
      estadoAcc=$('#estadoAccE').val();
      SaveEditAccAF(idAccE,codigoAF,nombreAcc,estadoAcc);
    });
    $('#saveDeleteAccAF').click(function(){
      codigoAF=<?=$codigo?>;
      idAccE=$('#idAccE').val();
      SaveDeleteAccAF(idAccE,codigoAF);
    });

    
  });
</script>