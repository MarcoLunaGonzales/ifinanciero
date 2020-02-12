<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$cod_cargo=$codigo;
//nombre del cargo
$stmtPersonal = $dbh->prepare("SELECT nombre from cargos where codigo=$cod_cargo");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$nombre_cargo=$result['nombre'];
//listamos los niveles que existen
$stmt = $dbh->prepare("SELECT codigo,nombre from niveles_escala_salarial where cod_estadoreferencial=1");    
$stmt->execute();
$stmt->bindColumn('codigo', $codigo_nivel_escala); 
$stmt->bindColumn('nombre', $nombre_nivel_escala);
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlCargosEscalaSalarialSave;?>" method="post">
            <input type="hidden" name="cod_cargo" id="cod_cargo" value="<?=$cod_cargo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar  Escala Salarial</h4>                
				</div>
                <h4 class="card-title" align="center"><?=$nombre_cargo;?></h4>
			  </div>
			  <div class="card-body ">                    
                <?php 
                    $idFila=0;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                        $idFila++;
                        $montoX="";
                        $sqlMontoNivel = "SELECT monto from cargos_escala_salarial
                        where cod_cargo=$cod_cargo and cod_nivel_escala_salarial=$codigo_nivel_escala and cod_estadoreferencial=1";
                        $stmtMontoNivel = $dbh->prepare($sqlMontoNivel);
                        $stmtMontoNivel->execute();
                        $resultMontoNivel=$stmtMontoNivel->fetch();                        
                        $montoX=$resultMontoNivel['monto'];
                

                    ?>
                    <div class="row">
                        <!-- <label class="col-sm-2 col-form-label">Nombre</label> -->
                        <div class="col-sm-2">
                        <div class="form-group">
                            <input class="form-control"  value="<?=$nombre_nivel_escala?>" readonly="true" style=" text-align: center">
                            <input class="form-control" type="hidden" name="cod_nivel_escala<?=$idFila;?>" id="cod_nivel_escala<?=$idFila;?>" value="<?=$codigo_nivel_escala?>"/>
                        </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Monto</label>
                        <div class="col-sm-2">
                        <div class="form-group">                        
                            <input class="form-control" type="text" name="monto<?=$idFila;?>" id="monto<?=$idFila;?>" value="<?=$montoX;?>" required="true"/>
                        </div>
                        </div>
                    </div><!--fin campo nombre --> 
                <?php  } ?>
                <input class="form-control" type="hidden" name="nroFila" id="nroFila" value="<?=$idFila?>"/>
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlCargosEscalaSalarial;?>&codigo=<?=$cod_cargo?>" class="<?=$buttonCancel;?>">
                <i class="material-icons" title="Volver">keyboard_return</i>Volver
            </a>

			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>