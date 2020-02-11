<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$cod_cargo=$codigo;
$dbh = new Conexion();


$stmtPersonal = $dbh->prepare("SELECT nombre from cargos where codigo=$cod_cargo");
//ejecutamos
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$nombre_cargo=$result['nombre'];
//SELECT
$stmt = $dbh->prepare("SELECT codigo,cod_nivel_escala_salarial,monto,
(select e.nombre from niveles_escala_salarial e where e.codigo=cod_nivel_escala_salarial) as nombre_nivel_escala
from cargos_escala_salarial
where cod_estadoreferencial=1 and cod_cargo=$cod_cargo
ORDER BY nombre_nivel_escala" );
$stmt->bindParam(':codigo',$cod_personal);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $cod_cargo_escala_salarial);
$stmt->bindColumn('cod_nivel_escala_salarial', $cod_nivel_escala_salarial);
$stmt->bindColumn('monto', $monto);
$stmt->bindColumn('nombre_nivel_escala', $nombre_nivel_escala);


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
                  <h4 class="card-title">Escala Salarial</h4>
                  <h4 class="card-title" align="center"><?=$nombre_cargo;?></h4>                  
                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr >
                        	<th>#</th>                          
              						<th>Nombre </th>
                          <th>Monto</th>
              						<th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                       	
                        	$datos=$cod_cargo."/".$cod_cargo_escala_salarial."/".$nombre_nivel_escala."/".$monto;
                        	?>
                            <tr>
                                <td><?=$index;?></td>
                                <td><?=$nombre_nivel_escala;?></td>
                                <td><?=formatNumberDec($monto);?></td>
                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($globalAdmin==1){
                                  ?>                                	
                                  <!-- <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaCargoEscalaSalarialE('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                  </button>   -->                                
                                <?php } ?>
                                </td>
                            </tr>
                        <?php $index++; } ?>            				
                      </tbody>
                      
                    </table>
                  </div>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
                <?php
                if($globalAdmin==1){
                ?>                              
                   <a href="<?=$urlCargosEscalaSalarialForm;?>&codigo=<?=$cod_cargo?>" class="btn btn-warning ">
                    Editar en Grupo
                  </a>                                             
                <?php
                }
                ?>
                <a href="<?=$urlListCargos;?>" class="btn btn-danger">
                  <i class="material-icons" title="Volver">keyboard_return</i>Volver
                </a> 
              </div>
            </div>
          </div>  
        </div>
    </div>



<script type="text/javascript">
  $(document).ready(function(){
    $('#EditarCEC').click(function(){
      cod_cargoE=document.getElementById("cod_cargoE").value;
      cod_cargo_escala_salarialE=document.getElementById("cod_cargo_escala_salarialE").value;
      montoE=$('#montoE').val();
      EditarCargoEscalaSalarial(cod_cargoE,cod_cargo_escala_salarialE,montoE);
    });
    
  });
</script>