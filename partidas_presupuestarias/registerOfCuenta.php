<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$table="partidaspresupuestarias_cuentas";
$moduleName="Configurar Cuentas por Partida Presupuestaria";

$codPartida=$codigo;
$nombrePartida=namePartidaPres($codPartida);

// Preparamos
$sql="SELECT c.codigo, c.numero, c.nombre, c.nivel,
(select count(*) from partidaspresupuestarias_cuentas pc where c.codigo=pc.cod_cuenta and pc.cod_partidapresupuestaria in ($codPartida))as bandera 
FROM plan_cuentas c where c.cod_estadoreferencial=1 order by 2";

//echo $sql;

$stmt = $dbh->prepare($sql);

// Ejecutamos
$stmt->execute(); 
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('nivel', $nivel);
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
                  <h4 class="card-title"><?=$moduleName?></h4>
                  <h6 class="card-title">Partida: <?=$nombrePartida;?></h6>
                  <h6 class="card-title">Por favor active la casilla para registrar la cuenta</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <form id="form_partidaspresupuestarias" method="post" action="partidas_presupuestarias/saveOfCuenta.php">
                      <input type="hidden" name="cod_partida" value="<?=$codPartida?>">
                    <table class="table" id="data_cuentas">
                      <thead>
                        <tr>
                          <th class="text-center">-</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                				$index=1;$nc=0;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $nombreX=trim($nombre);$numeroX=trim($numero);
                          if($bandera>0){
                          $data[$nc][0]=$index;
                          $data[$nc][1]=$nombreX;
                          $data[$nc][2]=$numeroX;                           
                          $nc++;
                          }
                          $nombre=formateaPlanCuenta($nombre,$nivel);
                          ?>
                        <tr>
                          <td align="center">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" type="checkbox" id="cuentas<?=$index?>" onclick="sendCheked(<?=$index?>,'<?=$nombreX?>','<?=$numeroX?>')" name="cuentas[]" value="<?=$codigo?>" <?=($bandera>0)?"checked":"";?> >
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
              							$index++;
              						}
                          ?>

                      </tbody>
                    </table>


                  </div>
                </div>
              </div>
        				<div class="card-footer fixed-bottom">
                    <button class="btn" type="submit">Guardar</button>
                    <a href="<?=$urlList2;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
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
require_once 'partidas_presupuestarias/modal.php';
?>