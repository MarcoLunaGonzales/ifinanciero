<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalAdmin        =$_SESSION["globalAdmin"];
$globalCodUnidad    =$_SESSION["globalUnidad"];
$globalNombreUnidad =$_SESSION["globalNombreUnidad"];


$cod_mes_global     =$_SESSION['globalMes'];
$nombre_mes         =nombreMes($cod_mes_global);
$codGestionActiva   =$_SESSION['globalGestion'];
$globalNombreGestion=$_SESSION['globalNombreGestion'];

$codigo_planilla = $_GET['codigo_planilla'];

$dbh = new Conexion();

$sql = "SELECT ppm.codigo, UPPER(CONCAT(p.primer_nombre, ' ', p.paterno)) as nombre_personal, p.email,
      (SELECT COUNT(*) FROM planillas_email WHERE cod_planilla_mes = ppm.codigo) as nro_visitas,
      (SELECT DATE_FORMAT(fecha,'%d-%m-%Y %H:%i:%s') FROM planillas_email WHERE cod_planilla_mes = ppm.codigo ORDER BY id DESC LIMIT 1) as ultima_visita
      FROM planillas_personal_mes ppm
      LEFT JOIN personal p ON p.codigo = ppm.cod_personalcargo
      WHERE ppm.cod_planilla = '$codigo_planilla'";
  $stmtAdmnin = $dbh->prepare($sql);
  $stmtAdmnin->execute();

  $stmtAdmnin->bindColumn('codigo', $codigo);
  $stmtAdmnin->bindColumn('nombre_personal', $nombre_personal);
  $stmtAdmnin->bindColumn('nro_visitas', $nro_visitas);
  $stmtAdmnin->bindColumn('ultima_visita', $ultima_visita);
  
  $ruta_vista = obtenerValorConfiguracion(104);
  ?>

  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Reporte de Visitas</h4>       
          </div>
          <div class="card-body ">
              <table class="table" id="tablePaginator">
                <thead>
                    <tr>                    
                      <th>Personal</th>
                      <th class="td-actions text-center">Nro Visitas</th>   
                      <th>Última Visita</th>
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;                  
                  $datosX="";
                  while ($row = $stmtAdmnin->fetch(PDO::FETCH_BOUND)) {              
                    ?>
                    <tr>                    
                      <td><?=$nombre_personal?></td>
                      <td class="td-actions text-center">
                          <a href="<?=$ruta_vista;?>ver_boleta.php?key=<?=$codigo;?>" 
                              target="_blank" 
                              rel="tooltip" 
                              class="btn btn-<?=$nro_visitas > 0 ? 'success' : 'danger' ?>" data-original-title="" title="Ver boleta">
                            <i class="material-icons" title="Visitas">remove_red_eye</i> <?=$nro_visitas?>                       
                            <div class="ripple-container"></div>
                          </a>                         
                      </td>
                      <td><?=empty($ultima_visita) ? 'Sin visita' : $ultima_visita ;?></td>
                    </tr>
                  <?php $index++; } ?>
                </tbody>                                      
              </table>
          </div> 
        </div>
      </div>
    </div>
  </div>
