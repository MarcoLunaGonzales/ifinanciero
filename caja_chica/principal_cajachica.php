<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT * from tipos_caja_chica where cod_estadoreferencial=1 and cod_personal=$globalUser");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_uo', $cod_uo);
$stmt->bindColumn('cod_area', $cod_area);



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
                  <h4 class="text-center">Seleccione un Caja Chica</h4>

                  <?php
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
                    <div class="row div-center text-center">
            
                       <div class="card bg-dark text-white mx-auto" style="width: 18rem;">
                         <a href="?opcion=ListaCajaChica&codigo=<?=$codigo?>" >
                            <div class="card-body ">
                               <h5 class="card-title"><?=$nombre;?></h5>
                               <p class="card-text text-small">Oficina : <?=$cod_uo?><br>Area : <?=$cod_area?></p>
                               <i class="material-icons">home_work</i>
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
