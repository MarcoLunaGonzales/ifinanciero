<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$check_var=$_GET['check_var'];
$dbh = new Conexion();

if($check_var==1){?>	
	<!-- <input type="text" name="razon_social" id="razon_social" class="form-control" style="background-color: #cec6d6;" required=""> -->
	<select name="razon_social" id="razon_social" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
        <option value=""></option>
        <?php 
        $query = "SELECT razon_social from facturas_venta GROUP BY razon_social order by razon_social asc";
        $stmt = $dbh->query($query);
        while ($row = $stmt->fetch()){ ?>
            <option value="<?=$row["razon_social"];?>" ><?=$row["razon_social"];?></option>
        <?php } ?>
    </select>
<?php }
?>



