<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$table="plan_cuentas_cajachica";
$moduleName="Configurar Cuentas por Partida Presupuestaria";

$codPartida=$codigo;
// Preparamos
$sql="SELECT c.codigo, c.numero, c.nombre, c.nivel,c.cod_padre,
(select count(*) from plan_cuentas_cajachica pc where c.codigo=pc.cod_cuenta)as bandera 
FROM plan_cuentas c where c.cod_estadoreferencial=1 and c.nivel=1 order by 2";
$stmt = $dbh->prepare($sql);
$stmt->execute(); 
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_padre', $cod_padre);
$stmt->bindColumn('bandera', $bandera);
?>
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Configurar Cuentas Para Caja Chica</h4>
                  <!-- <h6 class="card-title">Partida: Caja Chica</h6> -->
                  <h6 class="card-title">Por favor active la casilla para registrar la cuenta</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <form id="form_partidaspresupuestariasCC" method="post" action="caja_chica/plandecuentas_save.php">
                    <table class="table" id="data_cuentas_2">
                      <thead>
                        <tr>
                          <th></th>
                          <th class="text-center">-</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                				$index=1;$nc=0;$index_general=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $nombreX=trim($nombre);$numeroX=trim($numero);
                          if($bandera>0){
                          $data[$nc][0]=$index_general;
                          $data[$nc][1]=$nombreX;
                          $data[$nc][2]=$numeroX;                           
                          $nc++;
                          }
                          $nombre=formateaPlanCuenta($nombre,$nivel);
                          ?>
                          <tr onclick="verDetalleCuenta_nivel1('<?=$index?>')">
                            <td></td>
                            <td align="center">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="checkbox" id="cuentas<?=$index_general?>" onclick="sendCheked(<?=$index_general?>,'<?=$nombreX?>','<?=$numeroX?>')" name="cuentas[]" value="<?=$codigo?>" <?=($bandera>0)?"checked":"";?> >
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </td>
                            <td><?=$numero;?></td>
                            <td><?=$nombre;?></td>
                          </tr>
                          <?php
                          $index_general++;
                          $sql2="SELECT c.codigo, c.numero, c.nombre, c.nivel,c.cod_padre,
                          (select count(*) from plan_cuentas_cajachica pc where c.codigo=pc.cod_cuenta)as bandera 
                          FROM plan_cuentas c where c.cod_estadoreferencial=1 and c.nivel=2 and c.cod_padre='$numero' order by 2";
                          $stmt2 = $dbh->prepare($sql2);
                          $stmt2->execute(); 
                          $stmt2->bindColumn('codigo', $codigo_2);
                          $stmt2->bindColumn('numero', $numero2);
                          $stmt2->bindColumn('nombre', $nombre2);
                          $stmt2->bindColumn('nivel', $nivel2);
                          $stmt2->bindColumn('cod_padre', $cod_padre2);
                          $stmt2->bindColumn('bandera', $bandera2);
                          $index_2=1;
                          while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
                            $nombre2X=trim($nombre2);$numero2X=trim($numero2);
                            if($bandera2>0){
                            $data[$nc][0]=$index_general;
                            $data[$nc][1]=$nombre2X;
                            $data[$nc][2]=$numero2X;                           
                            $nc++;
                            }
                            $nombre2=formateaPlanCuenta($nombre2,$nivel2);
                            ?>
                            <tr onclick="verDetalleCuenta_nivel2('<?=$index?>','<?=$index_2?>')" class="det-cuenta-<?=$index?>" style="display:none">
                              <td></td>
                              <td align="center">
                                <div class="form-check">
                                  <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" id="cuentas<?=$index_general?>" onclick="sendCheked(<?=$index_general?>,'<?=$nombre2X?>','<?=$numero2X?>')" name="cuentas[]" value="<?=$codigo_2?>" <?=($bandera2>0)?"checked":"";?> >
                                    <span class="form-check-sign">
                                      <span class="check"></span>
                                    </span>
                                  </label>
                                </div>
                              </td>
                              <td><?=$numero2;?></td>
                              <td style="color: #483D8B"><?=$nombre2;?></td>
                            </tr>
                            <?php
                            $index_general++;
                              $sql3="SELECT c.codigo, c.numero, c.nombre, c.nivel,c.cod_padre,
                              (select count(*) from plan_cuentas_cajachica pc where c.codigo=pc.cod_cuenta)as bandera 
                              FROM plan_cuentas c where c.cod_estadoreferencial=1 and c.nivel=3 and c.cod_padre='$numero2' order by 2";
                              $stmt3 = $dbh->prepare($sql3);
                              $stmt3->execute(); 
                              $stmt3->bindColumn('codigo', $codigo_3);
                              $stmt3->bindColumn('numero', $numero3);
                              $stmt3->bindColumn('nombre', $nombre3);
                              $stmt3->bindColumn('nivel', $nivel3);
                              $stmt3->bindColumn('cod_padre', $cod_padre3);
                              $stmt3->bindColumn('bandera', $bandera3);
                              $index_3=1;
                              while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) {
                                $nombre3X=trim($nombre3);$numero3X=trim($numero3);
                                if($bandera3>0){
                                $data[$nc][0]=$index_general;
                                $data[$nc][1]=$nombre3X;
                                $data[$nc][2]=$numero3X;                           
                                $nc++;
                                }
                                $nombre3=formateaPlanCuenta($nombre3,$nivel3);
                                ?>
                                <tr onclick="verDetalleCuenta_nivel3('<?=$index?>','<?=$index_2?>','<?=$index_3?>')" class="det-cuenta-<?=$index?>-<?=$index_2?>" style="display:none">
                                  <td></td>
                                  <td align="center">
                                    <div class="form-check">
                                      <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" id="cuentas<?=$index_general?>" onclick="sendCheked(<?=$index_general?>,'<?=$nombre3X?>','<?=$numero3X?>')" name="cuentas[]" value="<?=$codigo_3?>" <?=($bandera3>0)?"checked":"";?> >
                                        <span class="form-check-sign">
                                          <span class="check"></span>
                                        </span>
                                      </label>
                                    </div>
                                  </td>
                                  <td ><?=$numero3;?></td>
                                  <td style="color:#006400"><?=$nombre3;?></td>
                                </tr>
                                <?php
                                $index_general++;
                                $sql4="SELECT c.codigo, c.numero, c.nombre, c.nivel,c.cod_padre,
                                (select count(*) from plan_cuentas_cajachica pc where c.codigo=pc.cod_cuenta)as bandera 
                                FROM plan_cuentas c where c.cod_estadoreferencial=1 and c.nivel=4 and c.cod_padre='$numero3' order by 2";
                                $stmt4 = $dbh->prepare($sql4);
                                $stmt4->execute(); 
                                $stmt4->bindColumn('codigo', $codigo_4);
                                $stmt4->bindColumn('numero', $numero4);
                                $stmt4->bindColumn('nombre', $nombre4);
                                $stmt4->bindColumn('nivel', $nivel4);
                                $stmt4->bindColumn('cod_padre', $cod_padre4);
                                $stmt4->bindColumn('bandera', $bandera4);
                                $index_4=1;
                                while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
                                  $nombre4X=trim($nombre4);$numero4X=trim($numero4);
                                  if($bandera4>0){
                                  $data[$nc][0]=$index_general;
                                  $data[$nc][1]=$nombre4X;
                                  $data[$nc][2]=$numero4X;                           
                                  $nc++;
                                  }
                                  $nombre4=formateaPlanCuenta($nombre4,$nivel4);
                                  ?>
                                  <tr onclick="verDetalleCuenta_nivel4('<?=$index?>','<?=$index_2?>','<?=$index_3?>','<?=$index_4?>')" class="det-cuenta-<?=$index?>-<?=$index_2?>-<?=$index_3?>" style="display:none">
                                    <td></td>
                                    <td align="center">
                                      <div class="form-check">
                                        <label class="form-check-label">
                                          <input class="form-check-input" type="checkbox" id="cuentas<?=$index_general?>" onclick="sendCheked(<?=$index_general?>,'<?=$nombre4X?>','<?=$numero4X?>')" name="cuentas[]" value="<?=$codigo_4?>" <?=($bandera4>0)?"checked":"";?> >
                                          <span class="form-check-sign">
                                            <span class="check"></span>
                                          </span>
                                        </label>
                                      </div>
                                    </td>
                                    <td><?=$numero4;?></td>
                                    <td style="color:#FF4500"><?=$nombre4;?></td>
                                  </tr>
                                  <?php
                                  $index_general++;
                                  $sql5="SELECT c.codigo, c.numero, c.nombre, c.nivel,c.cod_padre,
                                  (select count(*) from plan_cuentas_cajachica pc where c.codigo=pc.cod_cuenta)as bandera 
                                  FROM plan_cuentas c where c.cod_estadoreferencial=1 and c.nivel=5 and c.cod_padre='$numero4' order by 2";
                                  $stmt5 = $dbh->prepare($sql5);
                                  $stmt5->execute(); 
                                  $stmt5->bindColumn('codigo', $codigo_5);
                                  $stmt5->bindColumn('numero', $numero5);
                                  $stmt5->bindColumn('nombre', $nombre5);
                                  $stmt5->bindColumn('nivel', $nivel5);
                                  $stmt5->bindColumn('cod_padre', $cod_padre5);
                                  $stmt5->bindColumn('bandera', $bandera5);
                                  $index_5=1;
                                  while ($row = $stmt5->fetch(PDO::FETCH_BOUND)) {
                                    $nombre5X=trim($nombre5);$numero5X=trim($numero5);
                                    if($bandera5>0){
                                    $data[$nc][0]=$index_general;
                                    $data[$nc][1]=$nombre5X;
                                    $data[$nc][2]=$numero5X;                           
                                    $nc++;
                                    }
                                    $nombre5=formateaPlanCuenta($nombre5,$nivel5);
                                    ?>
                                    <tr class="det-cuenta-<?=$index?>-<?=$index_2?>-<?=$index_3?>-<?=$index_4?>" style="display:none">
                                      <td></td>
                                      <td align="center">
                                        <div class="form-check">
                                          <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" id="cuentas<?=$index_general?>" onclick="sendCheked(<?=$index_general?>,'<?=$nombre5X?>','<?=$numero5X?>')" name="cuentas[]" value="<?=$codigo_5?>" <?=($bandera5>0)?"checked":"";?> >
                                            <span class="form-check-sign">
                                              <span class="check"></span>
                                            </span>
                                          </label>
                                        </div>
                                      </td>
                                      <td><?=$numero5;?></td>
                                      <td><?=$nombre5;?></td>
                                    </tr>

                          <?php
                                $index_general++;
                                $index_5++;
                                }
                                $index_4++;
                                }
                              $index_3++;
                              }
                              
                              $index_2++;
                            }
                            
              							$index++;
              						}
                          ?>
                          <tr onclick="verDetalleCuenta_nivel_otros()">
                            <th></th>
                            <th align="center">-</th>
                            <th>-</th>
                            <th>OTRAS CUENTAS</th>
                          </tr>
                          <!-- cuentas que no tiene cod padre -->
                          <?php
                            $sqlotros="SELECT c.codigo, c.numero, c.nombre, c.nivel,c.cod_padre,
                            (select count(*) from plan_cuentas_cajachica pc where c.codigo=pc.cod_cuenta)as bandera 
                            FROM plan_cuentas c where c.cod_estadoreferencial=1 and c.nivel<>1 and c.cod_padre is null order by 2";
                            $stmtOtros = $dbh->prepare($sqlotros);
                            $stmtOtros->execute(); 
                            $stmtOtros->bindColumn('codigo', $codigo_otros);
                            $stmtOtros->bindColumn('numero', $numeroOtros);
                            $stmtOtros->bindColumn('nombre', $nombreOtros);
                            $stmtOtros->bindColumn('nivel', $nivelOtros);
                            $stmtOtros->bindColumn('cod_padre', $cod_padreOtros);
                            $stmtOtros->bindColumn('bandera', $banderaOtros);
                            $index_5=1;
                            while ($row = $stmtOtros->fetch(PDO::FETCH_BOUND)) {
                              $nombreOtrosX=trim($nombreOtros);$numeroOtrosX=trim($numeroOtros);
                              if($banderaOtros>0){
                              $data[$nc][0]=$index_general;
                              $data[$nc][1]=$nombreOtrosX;
                              $data[$nc][2]=$numeroOtrosX;                           
                              $nc++;
                              }
                              $nombreOtros=formateaPlanCuenta($nombreOtros,$nivelOtros);
                              if($nivelOtros==2){
                                $labelOtros='color:#483D8B';
                              }elseif($nivelOtros==3){
                                $labelOtros='color:#006400';
                              }elseif ($nivelOtros==4) {
                                $labelOtros='color:#FF4500';
                              }elseif($nivelOtros==5){
                                $labelOtros='color:#8B008B';
                              }else{
                                $labelOtros='color:#000000';
                              }
                              ?>
                              <tr class="det-cuenta-otros" style="display:none">
                                <td></td>
                                <td align="center">
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="cuentas<?=$index_general?>" onclick="sendCheked(<?=$index_general?>,'<?=$nombreOtrosX?>','<?=$numeroOtrosX?>')" name="cuentas[]" value="<?=$codigo_otros?>" <?=($banderaOtros>0)?"checked":"";?> >
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </td>
                                <td><?=$numeroOtros;?></td>
                                <td><?=$nombreOtros;?></td>
                              </tr>
                          <?php $index_general++;} ?>

                      </tbody>
                    </table>


                  </div>
                </div>
              </div>
        				<div class="card-footer fixed-bottom">
                    <button class="btn" type="submit">Guardar</button>
                    <a href="<?=$urlListCC2;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
                    <a href="#" onclick="filaTabla($('#tablas_registradas'));" id="boton_registradas" class="btn btn-warning text-dark">Cuentas Registradas <span class='badge bg-white text-warning'> <?=$nc?></span></a>
                </div>
			     </form>
            </div>
          </div>  
        </div>
    </div>
<?php 
for ($i=0; $i < $nc; $i++) { 
  ?><script>cuentas_tabla.push({codigo:<?=$data[$i][0]?>,nombre:'<?=$data[$i][1]?>',numero:'<?=$data[$i][2]?>'});</script><?php
}

?>
  <script>numFilas=<?=$nc?>;</script> 
<?php 
require_once 'caja_chica/modal.php';
?>

<script >
  function verDetalleCuenta_nivel1(index){ 
    var label=index;
    if($(".det-cuenta-"+label).is(":visible")){
      $(".det-cuenta-"+label).hide();
    }else{
      $(".det-cuenta-"+label).show();
    }
  }
  function verDetalleCuenta_nivel2(index,index_2){ 
    var label=index+"-"+index_2;
    if($(".det-cuenta-"+label).is(":visible")){
      $(".det-cuenta-"+label).hide();
    }else{
      $(".det-cuenta-"+label).show();
    }
  }
  function verDetalleCuenta_nivel3(index,index_2,index_3){ 
    var label=index+"-"+index_2+"-"+index_3;
    if($(".det-cuenta-"+label).is(":visible")){
      $(".det-cuenta-"+label).hide();
    }else{
      $(".det-cuenta-"+label).show();
    }
  }
  function verDetalleCuenta_nivel4(index,index_2,index_3,index_4){
    
    var label=index+"-"+index_2+"-"+index_3+"-"+index_4;
    if($(".det-cuenta-"+label).is(":visible")){
      $(".det-cuenta-"+label).hide();
    }else{
      $(".det-cuenta-"+label).show();
    }
  }
  function verDetalleCuenta_nivel_otros(){ 
    var label='otros';
    if($(".det-cuenta-"+label).is(":visible")){
      $(".det-cuenta-"+label).hide();
    }else{
      $(".det-cuenta-"+label).show();
    }
  }
</script>