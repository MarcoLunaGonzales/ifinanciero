<?php


require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
   require_once '../layouts/bodylogin2.php';
require_once '../styles.php';
$dbh = new Conexion();



if(isset($_GET["codigo_planilla"])){
    $cod_planilla = $_GET["codigo_planilla"];//
    $cod_gestion = $_GET["cod_gestion"];//
    // $cod_mes = $_GET["cod_mes"];//
    $codigo_nuevo=$cod_planilla.",".$cod_gestion;
    // $cod_personal="-1000";
    //$mes=strtoupper(nombreMes($cod_mes));
    $gestion=nameGestion($cod_gestion);
    ?>
    <div class="content">
        <div class="container-fluid">
       <center>
                <div class="col-md-8">
                    <form id="form1" class="form-horizontal" action="boletas_aguinaldos_print.php" method="GET" >
                    <div class="card">
                        <div class="card-header  card-header-text">
                            <div class="card-text">
                              <h4 class="card-title">Impresion de Boleta de Aguinaldo</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Planilla:</label>
                                <div class="col-sm-3"><div class="form-group"><input class="form-control" type="text" readonly="true" style="background:white;color: blue;" value="<?=$gestion?>" ></div></div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Oficina</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-default"  data-show-subtext="true" data-live-search="true" required="true">
                                            <option value="-1000">TODO</option>
                                            <?php 
                                            $query = "SELECT uo.codigo,uo.nombre 
                                                from personal p join unidades_organizacionales uo on p.cod_unidadorganizacional=uo.codigo 
                                                where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 GROUP BY uo.codigo";
                                            //echo $query;
                                            $stmt = $dbh->query($query);
                                            while ($rowges = $stmt->fetch()){ 
                                                ?>
                                                <option value="<?=$rowges["codigo"]?>"><?=$rowges["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>   
                            
                            <input type="hidden" name="cod_planilla" id="cod_planilla" value="<?=$codigo_nuevo?>">
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-rose">Generar Boleta</button>
                        </div>
                    </div>
                    </form>
                </div>
          </center>
        </div>
    </div>

    <?php 

   // $url="location:boletas_html.php?cod_planilla=".$codigo_nuevo."&cod_personal=".$cod_personal;
   //  header($url); 
}
?>