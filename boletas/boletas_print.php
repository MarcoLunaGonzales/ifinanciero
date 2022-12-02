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
    $cod_mes = $_GET["cod_mes"];//

    $codigo_nuevo=$cod_planilla.",".$cod_mes.",".$cod_gestion;
    $cod_personal="-1000";
    
    $mes=strtoupper(nombreMes($cod_mes));
    $gestion=nameGestion($cod_gestion);
    ?>
    <div class="content">
        <div class="container-fluid">
       <center>
                <div class="col-md-8">
                    <form id="form1" class="form-horizontal" action="boletas_html.php" method="GET" >
                    <div class="card">
                        <div class="card-header  card-header-text">
                            <div class="card-text">
                              <h4 class="card-title">Impresion de Boletas</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Planilla:</label>
                                <div class="col-sm-3"><div class="form-group"><input class="form-control" type="text" readonly="true" style="background:white;color: blue;" value="<?=$mes?> de <?=$gestion?>" ></div></div>
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
                            <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal?>">
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
}else{
 
    $cod_personal=$_GET["cod_personal"];

    $sql="SELECT p.paterno,p.materno,p.primer_nombre,(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo from personal p where p.codigo=$cod_personal";
    $stmtpersonal = $dbh->query($sql);
    $nombre="NO ENCONTRADO.";
    $cargo="";
    while ($rowpersonal = $stmtpersonal->fetch()){ 
        $nombre= $rowpersonal["paterno"]." ".$rowpersonal["materno"]." ".$rowpersonal["primer_nombre"];
        $cargo = $rowpersonal["cargo"];
    }
    ?>
    <div class="content">
        <div class="container-fluid">
       <center>
                <div class="col-md-8">
                    <form id="form1" class="form-horizontal" action="boletas_html.php" method="GET" >
                    <div class="card">
                        <div class="card-header  card-header-text">
                            <div class="card-text">
                              <h4 class="card-title">Impresion de Boletas</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Peronsal:</label>
                                <div class="col-sm-3"><div class="form-group"><input class="form-control" type="text" readonly="true" style="background:white;color: blue;" value="<?=$nombre?>" ></div></div>
                                <label class="col-sm-2 col-form-label"> Cargo:</label>
                                <div class="col-sm-3"><div class="form-group"><input class="form-control" type="text" readonly="true" style="background:white;color: blue;" value="<?=$cargo?>"></div></div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Mes</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_planilla" id="cod_planilla" class="selectpicker form-control form-control-sm" data-style="btn btn-default"  data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $query = "SELECT p.codigo,p.cod_mes,(select m.nombre from meses m where m.codigo=p.cod_mes) as nombre_mes,p.cod_gestion,(select g.nombre from  gestiones g where g.codigo=p.cod_gestion)as nombre_gestion from planillas p where p.cod_estadoplanilla=3";
                                            //echo $query;
                                            $stmt = $dbh->query($query);
                                            while ($rowges = $stmt->fetch()){ 
                                                    $codigo_nuevo=$rowges["codigo"].",".$rowges["cod_mes"].",".$rowges["cod_gestion"];
                                                ?>
                                                <option value="<?=$codigo_nuevo;?>"><?=$rowges["nombre_mes"];?> de <?=$rowges['nombre_gestion']?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>   
                            <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal?>">
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
}
?>