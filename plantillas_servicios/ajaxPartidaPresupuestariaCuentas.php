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


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$codigo=$_GET['cod_partida'];
$idFila=$_GET['fila'];

                                $cuentasPartida=obtenerCuentaPlantillaCostos($codigo);
                                ?>
                                  <select class="selectpicker form-control form-control-sm" name="cuenta_plantilladetalle<?=$idFila?>" id="cuenta_plantilladetalle<?=$idFila?>" data-style="fondo-boton-active" required>
                                   <option disabled selected value="">Cuentas</option>
                                    <?php 
                                     while ($rowCuenta = $cuentasPartida->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoCuentaX=$rowCuenta['cod_cuenta'];
                                      $nombreCuentaX=trim($rowCuenta['nombre']);
                                      ?><option value="<?=$codigoCuentaX?>"><?=$nombreCuentaX?></option><?php
                                     }
                                    ?>
                                  </select>
                             