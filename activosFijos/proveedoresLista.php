<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();


$stmt = $dbh->prepare("select * from af_proveedores where cod_estado=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_empresa', $cod_empresa);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('created_by', $created_by);
$stmt->bindColumn('modified_at', $modified_at);
$stmt->bindColumn('modified_by', $modified_by);
$stmt->bindColumn('direccion', $direccion);
$stmt->bindColumn('telefono', $telefono);
$stmt->bindColumn('email', $email);
$stmt->bindColumn('personacontacto', $personacontacto);



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
                  <h4 class="card-title"><?=$moduleNamePluralProveedores?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table"  id="tablePaginator">

<thead>
    <tr>
        <th>Código</th>
        <th>Nombre</th>
        <th>Dirección</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Persona Contacto</th>

        <th></th>
    </tr>
</thead>
<tbody>
<?php $index=1;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <tr>
        <td><?=$codigo;?></td>

        <td><?=$nombre;?></td>
        <td><?=$direccion;?></td>
        <td><?=$telefono;?></td>
        <td><?=$email;?></td>
        <td><?=$personacontacto;?></td>

        <td  class="td-actions text-right">
        <?php
          if($globalAdmin==1){
                        ?>
                        <a href='<?=$urlEditProv;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                        <i class="material-icons"><?=$iconEdit;?></i>
                        </a>
                        <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteProv;?>&codigo=<?=$codigo;?>')">
                        <i class="material-icons"><?=$iconDelete;?></i>
                        </button>
                        <?php
                    }
                    ?>
        </td>
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
      				<div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrarProv;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
