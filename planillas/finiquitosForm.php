<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

$dbh = new Conexion();
$query_retiro = "SELECT * from tipos_retiro_personal where cod_estadoreferencial=1 order by 2";
$statementTiposRetiro = $dbh->query($query_retiro);

//tipo de retiro                   
$querytiporetiro = "SELECT codigo,nombre from tipos_retiro_personal where cod_estadoreferencial=1 order by 2";
$stmtTipoRetiro = $dbh->query($querytiporetiro);
//por is es edit
if ($codigo > 0){
    //EDIT GET1 no guardar, sino obtener
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * FROM finiquitos where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $cod_personal = $result['cod_personal'];
    // $fecha_retiro = $result['fecha_retiro'];
    $cod_tiporetiro = $result['cod_tiporetiro'];
    $codigo_contrato = $result['cod_contrato'];

    $anios_pagados = $result['anios_pagados'];
    $dias_vacaciones_pagar = $result['dias_vacaciones_pagar'];
    $duodecimas = $result['duodecimas'];
    $otros_pagar = $result['otros_pagar'];
}else {    
  if(isset($_GET['codigo_contrato'])){
    $codigo_contrato=$_GET['codigo_contrato'];
  }else{
    $codigo_contrato = 0;  
  }
  $codigo = 0;
  // $fecha_retiro = ' ';
  $cod_tiporetiro = ' ';
  $anios_pagados = 0;
  $dias_vacaciones_pagar = 0;
  $duodecimas = 0;
  $otros_pagar = 0;
}
$sql="SELECT cod_tipocontrato from personal_contratos where codigo=$codigo_contrato";
$stmtTipoContrato = $dbh->prepare($sql);
$stmtTipoContrato->execute();
$resultTipoContrato = $stmtTipoContrato->fetch();
$cod_tipocontrato = $resultTipoContrato['cod_tipocontrato'];

if($cod_tipocontrato!=1){
  $sw_contratoidefinido=0;  
    $sqlpersonal="SELECT  c.codigo,c.cod_personal,p.primer_nombre,p.paterno,p.materno, DATE_FORMAT(c.fecha_iniciocontrato,'%d/%m/%Y')as ing_contr, DATE_FORMAT(c.fecha_fincontrato,'%d/%m/%Y')as fecha_retiro, DATEDIFF(c.fecha_fincontrato,c.fecha_iniciocontrato) as dias
      FROM personal p,personal_contratos c
      WHERE c.cod_personal=p.codigo and c.codigo=$codigo_contrato";
}else{//indefinido
  $sw_contratoidefinido=1;  
  $sqlpersonal="SELECT  c.codigo,c.cod_personal,p.primer_nombre,p.paterno,p.materno, DATE_FORMAT(c.fecha_iniciocontrato,'%d/%m/%Y')as ing_contr, DATE_FORMAT(pr.fecha_retiro,'%d/%m/%Y')as fecha_retiro, DATEDIFF(pr.fecha_retiro,c.fecha_iniciocontrato) as dias
  FROM personal p,personal_contratos c,personal_retiros pr
  WHERE c.cod_personal=p.codigo and pr.cod_personal=p.codigo and c.codigo=$codigo_contrato ORDER BY pr.codigo desc limit 1";
}
// echo $sqlpersonal;
$stmtpersonal = $dbh->prepare($sqlpersonal);
$stmtpersonal->execute();

?>

<div class="content">
	<div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlSaveFiniquitos;?>" method="post">
                <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularfiniquito;?></h4>
                    </div>
                  </div>
                  <h4 align="center"> Seleccione al personal Retirado Por favor.</h4>
                  <div class="card-body ">
                    <input type="hidden" name="codigo_contrato" id="codigo_contrato" value="<?=$codigo_contrato?>">
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Personal</label>
                        <div class="col-sm-8">
                          <div class="form-group">
                            <select name="cod_personal" id="cod_personal" data-style="btn btn-info" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                                  <option value="" disabled="disabled"></option>
                                  <?php 
                                      while ($row = $stmtpersonal->fetch()){ 
                                        $dias=$row['dias'];                                        
                                        ?>

                                       <option  <?=($dias<89)?"disabled":"";?> data-subtext="FI:<?=$row["ing_contr"];?> FF:<?=$row["fecha_retiro"];?> (<?=$row["dias"];?> días)"  value="<?=$row["cod_personal"];?>"><?=$row["paterno"];?> <?=$row["materno"];?> <?=$row["primer_nombre"];?> </option>
                                   <?php } ?>
                            </select>
                          </div>
                        </div>
                    </div><!--fin campo nombre -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label" >Tipo De Retiro</label>
                      <div class="col-sm-8">
                        <div class="form-group">              
                            <select name="cod_tiporetiro" id="cod_tiporetiro" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required data-show-subtext="true" data-live-search="true">
                            <?php while ($row = $statementTiposRetiro->fetch()){ ?>
                                <option <?=($cod_tiporetiro==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Años pagados de trabajo</label>
                        <div class="col-sm-8">
                        <div class="form-group">
                            <input class="form-control" type="number" name="anios_trabajados_pagados" id="anios_trabajados_pagados" required="true" value="<?=$anios_pagados?>" />
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Días de Vacaciones a Pagar</label>
                        <div class="col-sm-8">
                        <div class="form-group">
                            <input class="form-control" type="number" name="vacaciones_pagar" id="vacaciones_pagar" required="true" value="<?=$dias_vacaciones_pagar?>" />
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Doudécimas a Pagar</label>
                        <div class="col-sm-8">
                        <div class="form-group">
                            <input class="form-control" type="number" name="duodecimas" id="duodecimas" required="true" value="<?=$duodecimas?>" />
                        </div>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Otros a Pagar</label>
                        <div class="col-sm-8">
                        <div class="form-group">
                            <input class="form-control" type="number" name="otros" id="otros" required="true" value="<?=$otros_pagar?>" />
                        </div>
                        </div>
                    </div>                    
                  </div>
                  <div class="card-footer ml-auto mr-auto">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                    <a href="<?=$urlFiniquitosList;?>" class="<?=$buttonCancel;?>">Volver</a>
                  </div>
                </div>
              </form>
            </div>
        </div>		
	</div>
</div>