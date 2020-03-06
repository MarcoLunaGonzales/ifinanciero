<?php
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$usd=6.96;
$fechaActual=date("Y-m-d");
$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT * from tipos_clientenacionalidad");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('descripcion', $descripcion);
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
           <form action="<?=$urlSave?>" method="post">   
              <div class="card">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <input type="hidden" name="cambio_moneda" readonly value="<?=$usd?>" id="cambio_moneda"/>
                  <?php 
                  $an=0;
                 while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                  $an++; 
                  ?>
                <div class="row">
                  <div class="div-center"><h4><b><?=$descripcion?></b></h4></div>
                    <table class="table table-condensed table-bordered">
                      <thead>
                        <tr class="bg-primary text-white">
                          <th></th>
                          <th colspan="4">BOLIVIANOS</th>
                          <th colspan="4">DOLARES</th>
                        </tr>
                        <tr class="bg-primary text-white">
                          <th class="text-left" width="25%">Otorgaci&oacute;n del Sello</th>
                          <th>Micro</th>
                          <th>Pequeña</th>
                          <th>Mediana</th>
                          <th>Grande</th>
                          <th>Micro</th>
                          <th>Pequeña</th>
                          <th>Mediana</th>
                          <th>Grande</th>
                        </tr>
                      </thead>
                      <tbody>
                  <?php
                  $query1="SELECT * FROM configuraciones_servicios_sello order by codigo";
                  $stmt1 = $dbh->prepare($query1);
                  $stmt1->execute();
                  $iii=0;
                  while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                    $iii++;
                    $descripcionX=$row1['descripcion'];
                    $codigoX=$row1['codigo'];

                    $respuesta1=obtenerMontoTarifarioSelloEmpresa($codigoX,1,$codigo);
                    $respuesta2=obtenerMontoTarifarioSelloEmpresa($codigoX,2,$codigo);
                    $respuesta3=obtenerMontoTarifarioSelloEmpresa($codigoX,3,$codigo);
                    $respuesta4=obtenerMontoTarifarioSelloEmpresa($codigoX,4,$codigo);

                    $monto1=$respuesta1[0];
                    $monto2=$respuesta2[0];
                    $monto3=$respuesta3[0];
                    $monto4=$respuesta4[0];

                    //codigos
                    $codigos1=$respuesta1[1];
                    $codigos2=$respuesta2[1];
                    $codigos3=$respuesta3[1];
                    $codigos4=$respuesta4[1];

                    $monto1USD=number_format($monto1/$usd,2,'.','');
                    $monto2USD=number_format($monto2/$usd,2,'.','');
                    $monto3USD=number_format($monto3/$usd,2,'.','');
                    $monto4USD=number_format($monto4/$usd,2,'.','');

                    $monto1=number_format($monto1,2,'.','');
                    $monto2=number_format($monto2,2,'.','');
                    $monto3=number_format($monto3,2,'.','');
                    $monto4=number_format($monto4,2,'.','');
                     ?>
                       <tr>
                         <td class="text-left"><?=$descripcionX?></td>
                         <td><input type="hidden" name="codigos1FFF<?=$an?>FFF<?=$iii?>" value="<?=$codigos1?>"><input readonly type="number" id="monto_tarifario1FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifario1FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,1)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,1)" value="<?=$monto1?>" step="0.01"></td>
                         <td><input type="hidden" name="codigos2FFF<?=$an?>FFF<?=$iii?>" value="<?=$codigos2?>"><input readonly type="number" id="monto_tarifario2FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifario2FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,2)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,2)" value="<?=$monto2?>" step="0.01"></td>
                         <td><input type="hidden" name="codigos3FFF<?=$an?>FFF<?=$iii?>" value="<?=$codigos3?>"><input readonly type="number" id="monto_tarifario3FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifario3FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,3)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,3)" value="<?=$monto3?>" step="0.01"></td>
                         <td><input type="hidden" name="codigos4FFF<?=$an?>FFF<?=$iii?>" value="<?=$codigos4?>"><input readonly type="number" id="monto_tarifario4FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifario4FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,4)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',1,4)" value="<?=$monto4?>" step="0.01"></td>
                         <td><input readonly type="number" id="monto_tarifarioUSD1FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifarioUSD1FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,1)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,1)" value="<?=$monto1USD?>" step="0.01"></td>
                         <td><input readonly type="number" id="monto_tarifarioUSD2FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifarioUSD2FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,2)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,2)" value="<?=$monto2USD?>" step="0.01"></td>
                         <td><input readonly type="number" id="monto_tarifarioUSD3FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifarioUSD3FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,3)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,3)" value="<?=$monto3USD?>" step="0.01"></td>
                         <td><input readonly type="number" id="monto_tarifarioUSD4FFF<?=$an?>FFF<?=$iii?>" name="monto_tarifarioUSD4FFF<?=$an?>FFF<?=$iii?>"  class="form-control text-primary text-right input-tarifario" onchange="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,4)" onkeyUp="calcularMontoTarifarioType('<?=$an?>','<?=$iii?>',2,4)" value="<?=$monto4USD?>" step="0.01"></td>
                       </tr>
                     <?php
                   }
                   ?>
                     </tbody>
                    </table>
                    <input type="hidden" value="<?=$iii?>" id="numero_filas<?=$an?>" name="numero_filas<?=$an?>">
                  </div>
                   <?php
                 } 
                  ?>
                      
                </div>
              </div>
              <?php 

      				?><div class="card-footer fixed-bottom">
                <input type="hidden" value="<?=$an?>" id="numero_regiones" name="numero_regiones">
                <button type="submit" id="boton_guardar" class="btn btn-default d-none"><i class="material-icons">save</i> Guardar Cambios</button>
                <a href="#" id="boton_editar" class="btn btn-danger" onclick="editarTarifarioServicios(1)"><i class="material-icons">edit</i> Editar Registros</a>
                <a href="#" id="boton_editar_cancelar" class="btn btn-success d-none" onclick="editarTarifarioServicios(2)"><i class="material-icons">cancel</i> Cancelar Edici&oacute;n</a>
              </div>
             </form> 
            </div>
          </div>  
        </div>
    </div>
