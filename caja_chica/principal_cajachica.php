<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo,
  (select a.abreviatura from areas a where a.codigo=cod_area)as nombre_area
  from tipos_caja_chica where cod_estadoreferencial=1 and cod_personal=$globalUser");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_uo', $cod_uo);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('nombre_uo', $nombre_uo);
$stmt->bindColumn('nombre_area', $nombre_area);




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
                  <!-- <h4 class="card-title">Cajas chicas</h4>
                  <h4>Seleccione un Caja Chica</h4> -->
                </div>
                <div class="card-body">
                  <h4 class="text-center">Seleccionar una Caja Chica</h4>
                  <div class="row div-center text-center">
                  <?php
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
                    
            
                       <div class="card text-white mx-auto" style="background-color:#d32f2f; width: 18rem;">
                         <a href="?opcion=ListaCajaChica&codigo=<?=$codigo?>" >
                            <div class="card-body ">
                               <h5 class="card-title" style="color:#ffffff;"><?=$nombre;?></h5>
                               <p class="card-text text-small" style="color:#ffffff">Oficina : <?=$nombre_uo?><br>Area : <?=$nombre_area?></p>
                               <i class="material-icons" style="color:#37474f">home_work</i>
                            </div>
                         </a>
                       </div>

                  <?php }
                  ?>
                   </div>
                </div>
              </div>
              <?php

              if($globalAdmin==1){
              ?>
              
              <?php
              }
              ?>
      
            </div>
          </div>  
        </div>
    </div>
