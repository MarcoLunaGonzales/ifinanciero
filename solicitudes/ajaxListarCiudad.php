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
$codigo=$_GET['codigo'];
       
$lista= obtenerCiudadServicioIbrnorca($codigo);

?> 
                            <option disabled selected value="">--Seleccione--</option>
                             <?php
                                  foreach ($lista->lista as $listas) {
                                      echo "<option value=".$listas->idCiudad.">".$listas->nomCiudad."</opction>";
                                  }?>
                             <!--<option value="NN">OTRO</option>-->     
