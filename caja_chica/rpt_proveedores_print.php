<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();
set_time_limit(300);


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$sqlActivos="SELECT * from af_proveedores where cod_estado=1 order by nombre";  

//echo $sqlActivos;

$stmtProveedor = $dbh->prepare($sqlActivos);
$stmtProveedor->execute();

// bindColumn
$stmtProveedor->bindColumn('codigo', $codigo);
$stmtProveedor->bindColumn('cod_empresa', $cod_empresa);
$stmtProveedor->bindColumn('nombre', $nombre);
$stmtProveedor->bindColumn('nit', $nit);
$stmtProveedor->bindColumn('direccion', $direccion);
$stmtProveedor->bindColumn('telefono', $telefono);
$stmtProveedor->bindColumn('email', $email);
$stmtProveedor->bindColumn('personacontacto', $personal_contacto);
$stmtProveedor->bindColumn('email_personacontacto', $email_personalcontacto);
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              

              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <!-- <h6 class="card-title">Exportar como:</h6> -->
                  </div>
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">  Reporte De Proveedores</h4>
                  
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">

                    <table class="table table-condensed" >
                      <thead class="bg-secondary text-white">
                        <tr >
                          <th class="text-center">#</th>
                          <th class="font-weight-bold">Cod. Proveedor</th>
                          <th class="font-weight-bold">Nombre</th>
                          <th class="font-weight-bold">Nit</th>
                          <th class="font-weight-bold">Dirección</th>
                          <th class="font-weight-bold">Teléfono</th>
                          <th class="font-weight-bold">Email</th>
                          <th class="font-weight-bold">Personal Contacto</th>
                          <th class="font-weight-bold">Email Personal Contacto.</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                          $index = 0;
                          while ($row = $stmtProveedor->fetch(PDO::FETCH_ASSOC)) {
                            $index++;   
                        ?>
                        <tr>
                          <td class="text-center small"><?=$index;?></td>
                          <td class="text-center small"><?=$codigo?></td>
                          <td class="text-center small"><?=$nombre; ?></td>
                          <td class="text-center small"><?=$nit; ?></td>
                          <td class="text-left small"><?=$direccion; ?></td>
                          <td class="text-left small"><?= $telefono; ?></td>
                          <td class="text-left small"><?= $email; ?></td>
                          <td class="text-center small"><?= $personal_contacto; ?></td>
                          <td class="text-left small"><?= $email_personalcontacto; ?></td>                          
                        </tr>
                        <?php 
                            } 
                        ?>
                      </tbody>
                    </table>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>