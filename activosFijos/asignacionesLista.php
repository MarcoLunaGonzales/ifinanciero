<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$codigoX = '';
$codigo = 0;
try{

    if (isset($_POST["codigoactivo"]))
        $codigoX = $_POST["codigoactivo"];    

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $dbh->prepare("select * from v_activosfijos where codigoactivo = :codigoactivo");
    //$stmt = $dbh->prepare("select * from v_activosfijos where codigo = :codigoactivo");
    //echo "...".$codigoX;

    //bindparam
    $stmt->bindParam(':codigoactivo', $codigoX);
    //ejecutamos
    $stmt->execute();
    //$stmt->debugDumpParams();

    //bindColumn
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $codigoactivo = $result['codigoactivo'];
    $tipoalta = $result['tipoalta'];
    $fechalta = $result['fechalta'];
    $indiceufv = $result['indiceufv'];
    $tipocambio = $result['tipocambio'];
    $moneda = $result['moneda'];
    $valorinicial = $result['valorinicial'];
    $depreciacionacumulada = $result['depreciacionacumulada'];
    $valorresidual = $result['valorresidual'];
    $cod_depreciaciones = $result['cod_depreciaciones'];
    $cod_tiposbienes = $result['cod_tiposbienes'];
    $vidautilmeses = $result['vidautilmeses'];
    $estadobien = $result['estadobien'];
    $otrodato = $result['otrodato'];
    $cod_ubicaciones = $result['cod_ubicaciones'];
    $cod_empresa = $result['cod_empresa'];
    $activo = $result['activo'];
    $cod_responsables_responsable = $result['cod_responsables_responsable'];
    $cod_responsables_autorizadopor = $result['cod_responsables_autorizadopor'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
    $vidautilmeses_restante = $result['vidautilmeses_restante'];
    $nombre_personal = $result['nombre_personal'];
    $nombre_depreciaciones = $result['nombre_depreciaciones'];
    $tipo_bien = $result['tipo_bien'];
    $edificio = $result['edificio'];
    $oficina = $result['oficina'];
    $nombre_uo = $result['nombre_uo'];

    if (isset($_POST["codigoactivo"])){
        //OBTENER LAS ASIGNACIONES, NUEVA ASIGNACION ETC
        $query = "SELECT * FROM v_activosfijos_asignaciones where codigo = ".$codigo;
        $statement = $dbh->query($query);
    }
    //$statement->debugDumpParams();
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlList8;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"> Asignacion <?=$moduleNameSingular6;?></h4>
				</div>
			  </div>
			  <div class="card-body ">


<div class="row">
    <label class="col-sm-2 col-form-label">Codigo Activo</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" name="codigoactivo" id="codigoactivo" required="true" value="<?=$codigoactivo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
    <button type="submit" class="<?=$buttonNormal;?>">Buscar</button>
</div><!--fin campo codigoactivo -->

<div class="row">
    <label class="col-sm-2 col-form-label">Estado bien</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" readonly name="estadobien" id="estadobien" value="<?=$estadobien;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

    <!--fin campo estadobien -->
    <label class="col-sm-2 col-form-label">Asingado a</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" readonly  name="nombre_personal" id="nombre_personal" value="<?=$nombre_personal;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

</div>

<div class="row">
    <label class="col-sm-2 col-form-label">Rubro</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" readonly name="tipo_bien" id="tipo_bien" value="<?=$tipo_bien;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>

    <label class="col-sm-2 col-form-label">Edificio</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" readonly name="edificio" id="edificio" value="<?=$edificio;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo edificio -->
<div class="row">
    <label class="col-sm-2 col-form-label">Oficina</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" readonly name="oficina" id="oficina" value="<?=$oficina;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>



    <label class="col-sm-2 col-form-label">UO</label>
    <div class="col-sm-4">
    <div class="form-group">
        <input class="form-control" type="text" readonly name="nombre_uo" id="nombre_uo" value="<?=$nombre_uo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo oficina -->
<div class="row">
    <label class="col-sm-2 col-form-label">Activo</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="activo" readonly id="activo" value="<?=$activo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo activo -->
</div>
			  
			</div>
		  </form>
		</div>
	
	</div>
</div>

<!-- tabla -->
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNamePlural8?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table">
                    <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                     
                      
                        <th>Personal</th>
                     
                      
                        <th>Edificio</th>
                        <th>Oficina</th>
                        <th>UO</th>
                        <th></th>
                    </tr>
                        </thead>
                        <tbody>
                        <?php $index=1;
                        while ($row = $statement->fetch()) { ?>
                           <tr>
                                <td><?=$row["fechaasignacion"];?></td>
                                <td><?=$row["estadobien_asig"];?></td>
                     
                                <td><?=$row["nombre_personal"];?></td>
                           
                                <td><?=$row["tipo_bien"];?></td>
                                
                                <td><?=$row["edificio"];?></td>
                                <td><?=$row["oficina"];?></td>
                                <td><?=$row["nombre_uo"];?></td>
                                <td></td>
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
      				<div class="card-footer ml-auto mr-auto">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlEdit8;?>'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>



<?php
} catch(PDOException $ex){
	echo "Un error ocurrio".$ex->getMessage();
}
?>





