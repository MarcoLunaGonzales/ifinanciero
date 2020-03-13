<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$codigo=$_GET['banco'];

?> 
                           <label class="col-sm-2 col-form-label">Cheques</label>
                           <div class="col-sm-3">                     
                             <div class="form-group">
                               <select class="selectpicker form-control form-control-sm" onchange="ponerNumeroChequePago()" data-live-search="true" name="emitidos_pago" id="emitidos_pago" data-style="btn btn-primary">
                                    <option selected="selected" value="####">--CHEQUES--</option>
                                    <?php 
                                     $stmt4 = $dbh->prepare("SELECT * FROM cheques where cod_banco=$codigo and cod_estadoreferencial=1");
                                     $stmt4->execute();
                                     while ($row2 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel2=$row2['codigo'];
                                      $serie=$row2['nro_serie'];
                                      $cheque=(int)$row2['nro_cheque']+1;
                                      ?><option value="<?=$codigoSel2;?>####<?=$cheque?>"># SERIE : <?=$serie?></option><?php 
                                     }
                                    ?>
                                  </select>
                             </div>
                           </div>                           
                           <label class="col-sm-1 col-form-label">Nro. Cheque</label>
                           <div class="col-sm-2">                     
                             <div class="form-group">
                             	<input type="number" class="form-control" name="numero_cheque" id="numero_cheque" value="0">
                             </div>
                           </div>
                           <label class="col-sm-1 col-form-label">Nombre Ben.</label>
                           <div class="col-sm-3">                     
                             <div class="form-group">
                             	<input type="text" class="form-control" name="nombre_ben" id="nombre_ben" value="">
                             </div>
                           </div>                            
