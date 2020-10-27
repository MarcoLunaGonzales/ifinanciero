<?php
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$mes=$_SESSION["globalMes"];
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$fechaActual=date("Y-m-d");
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

// Preparamos
$lista=listaLibretasBancarias();
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">today</i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Libreta</th>
                          <th>Banco</th>
                          <th>Cuenta</th>
                          <th>Contra Cuenta</th>
                          <th class="text-right" width="24%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;$cont=0;

                      	while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $codigo=$row['codigo'];
                          $nombre=$row['nombre'];
                          $banco=$row['banco'];
                          $cuenta=$row['nro_cuenta'];
                          $cod_cuenta=$row['cod_cuenta'];
                          $cod_contraCuenta=$row['cod_contracuenta'];
                          $nombreCuenta=obtieneNumeroCuenta($cod_cuenta)." ".nameCuenta($cod_cuenta);
                          $ContraCuenta=obtieneNumeroCuenta($cod_contraCuenta)." ".nameCuenta($cod_contraCuenta);
?>
                        <tr>
                          <td align="center text-left"><?=$index;?></td>                          
                          <td class="text-left font-weight-bold"><?=$nombre;?></td>
                          <td class="text-left"><?=$banco;?></td>
                          <td class="small text-left"><small><?=$nombreCuenta;?></small></td>
                          <td class="small text-left"><small><?=$ContraCuenta;?></small></td>
                      
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">addchart</i> DATOS EXCEL
                              </button>
                              <div class="dropdown-menu menu-fixed-sm-table">
                                <a href="#" onclick="ponerHtmlElemento('<?=$nombre?>','titulo_excel');ponerVariableInput(<?=$codigo?>,'codigo');subirArchivoExcelLibretaBancaria(1,'Formato BISA'); return false;"  class="dropdown-item">
                                   <i class="material-icons">keyboard_arrow_right</i>Formato BISA
                                </a>
                                <a href="#" onclick="ponerHtmlElemento('<?=$nombre?>','titulo_excel');ponerVariableInput(<?=$codigo?>,'codigo');subirArchivoExcelLibretaBancaria(2,'Formato UNION'); return false;"  class="dropdown-item">
                                   <i class="material-icons">keyboard_arrow_right</i>Formato UNION
                                </a>
                              </div>
                             </div> 
                            <a href='<?=$urlList2;?>&codigo=<?=$codigo;?>' class="<?=$buttonDetailMin;?>">
                              <i class="material-icons" title="Detalle">playlist_add</i>
                            </a>
                           <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
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
                    <a href="#" onclick="location.href='<?=$urlRegister2;?>'" class="<?=$buttonNormal;?>">Registrar</a>
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
      <div class="card-header card-header-default card-header-text">
        <div class="card-text">
          <h4 id="formato_texto"></h4>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <center><h2 id="titulo_excel" class="font-weight-bold text-info"></h2></center>
        <form action="<?=$urlSaveImport?>" method="post" name="formLibretaBancaria" id="formLibretaBancaria" enctype="multipart/form-data">
          <input type="hidden" name="tipo_formato" id="tipo_formato">
          <div class="row">
            <label class="col-sm-3 col-form-label" style="color:#000000; ">Archivo Excel:</label>
            <div class="col-sm-6">
              <div class="form-group">
                <input type="hidden" class="form-control" name="lista_padre" id="lista_padre" value="-1">
                <input type="hidden" class="form-control" name="codigo" id="codigo" value="-1">
                <small id="label_txt_documentos_excel"></small> 
                <span class="input-archivo">
                  <input type="file" class="archivo" accept=".xls,.xlsx" name="documentos_excel" id="documentos_excel"/>
                </span>
                <label title="Ningún archivo" for="documentos_excel" id="label_documentos_excel" class="label-archivo btn btn-default btn-sm"><i class="material-icons">publish</i> Subir Archivo
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
                <select class="selectpicker form-control" name="tipo_cargado" id="tipo_cargado" data-style="btn btn-default">
                <?php
                   $stmt = $dbh->prepare("SELECT p.codigo,p.nombre FROM tipos_libretabancariacargado p where p.cod_estadoreferencial=1 order by p.codigo desc");
                   $stmt->execute();
                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                     $codigoX=$row['codigo'];
                     $nombreX=$row['nombre'];
                     if($codigoX==1){
                      ?>
                     <option value="<?=$codigoX;?>" selected><?=$nombreX;?></option>  
                      <?php
                     }else{
                      ?>
                     <option value="<?=$codigoX;?>"><?=$nombreX;?></option>  
                      <?php
                     }
                   
                     }
                     ?> 
                </select>
              </div>
            </div> 
          </div>
          <br><br>
          <center><h4 id="tipo_formato_titulo2" class="font-weight-bold"></h4></center>
          <div id="tabla_muestra_formato_a">
            <table class="table table-bordered small table-condensed">
              <thead>
               <tr style="background:#F9D820; color:#262C7B;">
                <th>Fecha</th>
                <th>Hora</th>
                <th>Nro Cheque</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Saldo</th>
                <th>Información C.</th>
                <th>Sucursal</th>
                <th>Canal</th>
                <th>Nro Referencia</th> 
                <th>Codigo</th>
               </tr>
              </thead>
              <tbody>
               <tr style="background:#262C7B; color:#fff;">
                <td>dd-mm-aaaa</td>
                <td>HH:mm:ss</td>
                <td></td>
                <td></td>
                <td></td>
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
                 <tr style="background:#223BC8; color:#F3F300;">
                  <th>Fecha</th>
                  <th>Agencia</th>
                  <th>Descripción</th>
                  <th>Nro Documento</th>
                  <th>Monto</th>
                  <th>Saldo</th>
                 </tr>
               </thead>
               <tbody>
                 <tr style="background:#F37200; color:#fff;">
                   <td>dd-mm-aaaa</td>
                  <td></td>
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
        </form>
      </div>  
    </div>
  </div>
</div>
<!--    end small modal -->