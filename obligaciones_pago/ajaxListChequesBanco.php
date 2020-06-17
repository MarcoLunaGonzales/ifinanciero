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
$fila=$_GET['fila'];
?>                    
                             <div class="form-group">
                               <select class="selectpicker form-control form-control-sm" onchange="ponerNumeroChequePagoDetalle('<?=$fila?>')" data-live-search="true" name="emitidos_pago<?=$fila?>" id="emitidos_pago<?=$fila?>" data-style="btn btn-primary">
                                    <option selected="selected" value="####">--CHEQUES--</option>
                                    <?php 
                                     $stmt4 = $dbh->prepare("SELECT * FROM cheques where cod_banco=$codigo and cod_estadoreferencial=1");
                                     $stmt4->execute();
                                     while ($row2 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel2=$row2['codigo'];
                                      $serie=$row2['nro_serie'];
                                      $cheque=(int)$row2['nro_cheque']+1;
                                      ?><option value="<?=$codigoSel2;?>####<?=$cheque?>"># CUENTA : <?=$serie?></option><?php 
                                     }
                                    ?>
                                  </select>
                             </div>                    
