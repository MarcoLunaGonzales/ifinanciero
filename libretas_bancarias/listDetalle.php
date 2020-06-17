<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codigoLibreta=$_GET['codigo'];
//echo "test cod bono: ".$codigoLibreta;

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT ce.*
FROM libretas_bancariasdetalle ce where ce.cod_libretabancaria=$codigoLibreta and  ce.cod_estadoreferencial=1 order by ce.codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('descripcion', $descripcion);
$stmt->bindColumn('informacion_complementaria', $informacion_complementaria);
$stmt->bindColumn('agencia', $agencia);
$stmt->bindColumn('nro_cheque', $nro_cheque);
$stmt->bindColumn('nro_documento', $nro_documento);
$stmt->bindColumn('fecha_hora', $fecha);
$stmt->bindColumn('monto', $monto);


//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT p.nombre as banco,c.* FROM $table c join bancos p on c.cod_banco=p.codigo WHERE c.codigo=$codigoLibreta");
// Ejecutamos
$stmtb->execute();
// bindColumn
$stmtb->bindColumn('banco', $nombreBanco);
$stmtb->bindColumn('nro_cuenta', $cuenta);
$stmtb->bindColumn('nombre', $nombre);

?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Subiendo Archivo Excel</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNamePluralDetalle?></h4>
                  
                  <?php
                  while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
                    ?>
                  <h4 class="card-title" align="center"><?=$nombreBanco?> <b>NRO. CUENTA: <?=$cuenta?></b> / <?=$nombre?></b></h4>
                  <?php
                  }
                  ?>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">
                          <td class="text-center">#</td>
                          <td>Fecha</td>
                          <td>Hora</td>
                          <td>Descripcion</td>
                          <td>Informacion C.</td>
                          <td>Sucursal</td>
                          <td>Monto</td>
                          <td>Nro Cheque</td>
                          <td>Nro Documento</td>
                          <td class="text-right">Acciones</td>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                           
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-center"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
                          <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
                          <td class="text-left"><?=$descripcion?></td>
                          <td class="text-left"><?=$informacion_complementaria?></td>      
                          <td class="text-left"><?=$agencia?></td>
                          <td class="text-right"><?=number_format($monto,2,".","")?></td>
                          <td class="text-right"><?=$nro_cheque?></td>
                          <td class="text-left"><?=$nro_documento?></td>
                          <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                            ?>
                            <a href="#" class="btn btn-fab btn-danger btn-sm"><i class="material-icons"><?=$iconDelete;?></i></a>
                            <?php
                            }
                            ?>
                            
                          </td>
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

              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                <button class="<?=$buttonCancel;?>" onClick="location.href='<?=$urlList;?>'">Volver</button>
                <div class="btn-group dropdown">
                              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Cargar Libreta de Exel
                              </button>
                              <div class="dropdown-menu">
                                <a href="#" onclick="subirArchivoExcelLibretaBancaria(1,'Formato A');return false;"  class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i>Formato A
                                </a>
                                <a href="#" onclick="subirArchivoExcelLibretaBancaria(2,'Formato B');return false;"  class="dropdown-item">
                                           <i class="material-icons">keyboard_arrow_right</i>Formato B
                                </a>
                              </div>
                  </div>
                
              </div>
              
              <?php
              }
              ?>
		  
            </div>
          </div>  
              


        </div>
    </div>


    <!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalSubirArchivoExcel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
               <div class="card-header card-header-warning card-header-text">
                  <div class="card-text">
                    <h4 id="formato_texto"></h4>
                  </div>
                  <button type="button" class="btn btn-success btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                  <form action="<?=$urlSaveImport?>" method="post" name="formLibretaBancaria" id="formLibretaBancaria" enctype="multipart/form-data">
                    <input type="hidden" name="tipo_formato" id="tipo_formato">
                  <div class="row">
                       <label class="col-sm-3 col-form-label" style="color:#000000; ">Archivo Excel:</label>
                       <div class="col-sm-6">
                         <div class="form-group">
                          <input type="hidden" class="form-control" name="codigo" id="codigo" value="<?=$codigoLibreta?>">
                          <small id="label_txt_documentos_excel"></small> 
                          <span class="input-archivo">
                            <input type="file" class="archivo" accept=".xls,.xlsx" name="documentos_excel" id="documentos_excel"/>
                          </span>
                          <label title="Ningún archivo" for="documentos_excel" id="label_documentos_excel" class="label-archivo btn btn-info btn-sm"><i class="material-icons">publish</i> Subir Archivo
                          </label>
                      
                         </div>
                       </div>
                   </div>
                   <div class="row">     
                       <label class="col-sm-3 col-form-label" style="color:#000000; ">Observaciones:</label>
                       <div class="col-sm-6">
                         <div class="form-group">
                           <textarea type="text" class="form-control" name="observaciones" id="observaciones" value="" style="background-color:#E3CEF6;text-align: left" ></textarea>
                         </div>
                       </div> 
                   </div>
                   <div class="row">     
                       <label class="col-sm-3 col-form-label" style="color:#000000; ">Tipo de Cargado:</label>
                       <div class="col-sm-6">
                         <div class="form-group">
                            <select class="selectpicker form-control" name="tipo_cargado" id="tipo_cargado" data-style="btn btn-info">
                          <?php
                             $stmt = $dbh->prepare("SELECT p.codigo,p.nombre FROM tipos_libretabancariacargado p where p.cod_estadoreferencial=1 order by p.codigo");
                             $stmt->execute();
                             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                               $codigoX=$row['codigo'];
                               $nombreX=$row['nombre'];
                             ?>
                             <option value="<?=$codigoX;?>"><?=$nombreX;?></option>  
                             <?php
                               }
                               ?> 
                       </select>
                         </div>
                       </div> 
                   </div>
                   <br><br>
                   <center><h4 id="tipo_formato_titulo2" class="font-weight-bold"></h4></center>
                   <div id="tabla_muestra_formato_a">
                     <table class="table table-bordered table-condensed">
                       <thead>
                         <tr style="background:#21618C; color:#fff;">
                          <th>Fecha</th>
                          <th>Hora</th>
                          <th>Descripcion</th>
                          <th>Informacion Complementaria</th>
                          <th>Nro Documento</th>
                          <th>Monto</th>
                          <th>Sucursal</th>
                          <th>Nro Cheque</th>
                         </tr>
                       </thead>
                       <tbody>
                         <tr>
                           <td>dd-mm-aaaa</td>
                          <td>HH:mm:ss</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                         </tr>
                       </tbody>
                     </table>  
                   </div>
                   <div id="tabla_muestra_formato_b" class="d-none">
                     <table class="table table-bordered table-condensed">
                       <thead>
                         <tr style="background:#21618C; color:#fff;">
                          <th>Fecha</th>
                          <th>Descripcion</th>
                          <th>Nro Documento</th>
                          <th>Monto</th>
                          <th>Sucursal</th>
                         </tr>
                       </thead>
                       <tbody>
                         <tr>
                           <td>dd-mm-aaaa</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                         </tr>
                       </tbody>
                     </table>  
                   </div>
                <hr>
                <div class="float-right">
                  <button type="submit" id="submit" name="import" class="btn btn-success" onclick="iniciarCargaAjax();">Importar Registros</button>
                </div>
               </div>  
    </div>
  </div>
<!--    end small modal -->