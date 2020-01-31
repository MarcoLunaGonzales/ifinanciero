<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$sqlpersonal="SELECT r.codigo,r.cod_personal,p.primer_nombre,p.paterno,p.materno
from personal_retiros r,personal p
where r.cod_personal=p.codigo and r.cod_estadoreferencial=1";
$stmtpersonal = $dbh->prepare($sqlpersonal);
$stmtpersonal->execute();
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
    $fecha_retiro = $result['fecha_retiro'];
    $cod_tiporetiro = $result['cod_tiporetiro'];

}else {
    $codigo = 0;
    $cod_personal = 0;
    $fecha_retiro = ' ';
    $cod_tiporetiro = ' ';
}
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
                    <div class="row">
                          <label class="col-sm-2 col-form-label">Personal</label>
                          <div class="col-sm-7">
                          <div class="form-group">

                              <select name="cod_personal" id="cod_personal" class="selectpicker form-control" data-style="btn btn-primary" >
                                  <option ></option>
                                  <?php 
                                      while ($row = $stmtpersonal->fetch()){ 
                                  ?>
                                       <option <?=($cod_personal==$row["cod_personal"])?"selected":"";?> value="<?=$row["cod_personal"];?>"><?=$row["paterno"];?> <?=$row["materno"];?> <?=$row["primer_nombre"];?></option>
                                   <?php 
                                      } 
                                  ?>
                               </select>
                              
                          </div>
                          </div>
                    </div><!--fin campo nombre -->
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Años pagados de trabajo</label>
                        <div class="col-sm-7">
                        <div class="form-group">
                            <input class="form-control" type="number" name="anios_trabajados_pagados" id="anios_trabajados_pagados" required="true" value="0" />

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