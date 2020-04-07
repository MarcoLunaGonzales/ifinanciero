<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';
// require 'notificaciones_sistema/PHPMailer/send.php';

$globalAdmin=$_SESSION["globalAdmin"];
// $globalUserX=$_SESSION['globalUser'];

$dbh = new Conexion();
//envio de correo automatico para indicar el vencimiento de contratos del personal
$cod_respo=obtenerValorConfiguracion(41);
$stmtEnvioCorreo = $dbh->prepare("SELECT email,CONCAT_WS(' ',primer_nombre,paterno) as nombre_encargado from personal where codigo=$cod_respo");
$stmtEnvioCorreo->execute();
$resultEC = $stmtEnvioCorreo->fetch();
$nombre_encargado = $resultEC['nombre_encargado'];
// $email_respo = $resultEC['email'];
$email_respo="bsullcamani@gmail.com";//correo de prueba
// $fecha_actual=date('Y-m-d');
$fecha_actual="2020-09-27";
//lista de contrado con evaluacion a la fecha o 5 dias antes
$sqlContratos="SELECT codigo,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal) as personal, fecha_fincontrato from personal_contratos where cod_estadoreferencial=1 and cod_estadocontrato=1 and alerta_enviada=0 and fecha_evaluacioncontrato BETWEEN DATE_SUB('$fecha_actual',INTERVAL 5 DAY) and '$fecha_actual'";
$stmtContratosFecha = $dbh->prepare($sqlContratos);
$stmtContratosFecha->execute();
$stmtContratosFecha->bindColumn('codigo', $codigo_C);
$stmtContratosFecha->bindColumn('personal', $personal);
$stmtContratosFecha->bindColumn('fecha_fincontrato', $fecha_fincontrato);
$cont=0;
$MessgAdjunto="";
$arrayCodContrato=array();
while ($rowContratos = $stmtContratosFecha->fetch(PDO::FETCH_BOUND)) { 
  $MessgAdjunto.=$personal.", Fecha: ".$fecha_fincontrato.".<br>\n";
  array_push($arrayCodContrato,$codigo_C);
  $cont++;  
}
// var_dump($arrayCodContrato);
$stringCodCotrato=implode(",", $arrayCodContrato);
if($cont>0){
  $texto_cuerpo="Estimad@ ".$nombre_encargado.",<br>\n<br>\n queremos recordarle que el contrato del personal que se encuentra en la siguente lista, finalizará en la fecha adjunta:<br>\n<br>\n".$MessgAdjunto."<br>Saludos.";
  $asunto="FIN CONTRATO PERSONAL ".date('Y-m-d');
  $mail_username="noresponse@minkasoftware.com";//Correo electronico emisor
  $mail_userpassword="minka@2019";// contraseña correo emisor
  $mail_addAddress=$email_respo;//correo electronico destino
  $template="notificaciones_sistema/PHPMailer/email_template.html";//Ruta de la plantilla HTML para enviar nuestro mensaje
  /*Inicio captura de datos enviados por $_POST para enviar el correo */
  $mail_setFromEmail=$mail_username;
  $mail_setFromName="IBNORCA";
  $txt_message=$texto_cuerpo;
  $mail_subject=$asunto; //el subject del mensaje

  // echo $mail_username."<br>";
  // echo $mail_userpassword."<br>";
  // echo $mail_setFromEmail."<br>";
  // echo $mail_setFromName."<br>";
  // echo $mail_addAddress."<br>";
  // echo $mail_subject."<br>";
  // echo $template."<br>";  
  // echo $txt_message."<br>";  
  
  $flag=sendemail($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject,$template,0);
  if($flag!=0){//se envio correctamente
    echo "CORREO ENVIADO";
    $sqlContratos="UPDATE personal_contratos set alerta_enviada='1' where codigo in($stringCodCotrato)";
    $stmtUpdate = $dbh->prepare($sqlContratos);
    $stmtUpdate->execute();
  }else{
    echo "ERROR AL ENVIAR CORREO";
  }
}
$stmt = $dbh->prepare("SELECT p.codigo,p.identificacion,p.cod_lugar_emision,p.paterno,p.materno,p.primer_nombre,p.bandera,p.ing_contr,p.cod_estadopersonal,
  (select c.nombre from cargos c where c.codigo=cod_cargo)as xcargo,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as xuonombre,
 (select a.abreviatura from areas a where a.codigo=cod_area)as xarea,
 (select ep.nombre from estados_personal ep where ep.codigo=cod_estadopersonal)as xestado,
 (select tp.nombre from tipos_personal tp where tp.codigo=cod_tipopersonal)as xcod_tipopersonal
 
 from personal p
 where p.cod_estadoreferencial=1
 order by p.paterno, p.materno, p.primer_nombre
 ");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('identificacion', $ci);
$stmt->bindColumn('cod_lugar_emision', $ci_lugar_emision);
$stmt->bindColumn('paterno', $paterno);
$stmt->bindColumn('materno', $materno);
$stmt->bindColumn('primer_nombre', $primer_nombre);
$stmt->bindColumn('bandera', $bandera);
$stmt->bindColumn('cod_estadopersonal', $cod_estadopersonal);

$stmt->bindColumn('ing_contr', $fecha_ingreso);
$stmt->bindColumn('xcargo', $xcargo);
$stmt->bindColumn('xuonombre', $xuonombre);
$stmt->bindColumn('xarea', $xarea);
$stmt->bindColumn('xestado', $xestado);
$stmt->bindColumn('xcod_tipopersonal', $xcod_tipopersonal);

?>

<div class="content">
	<div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header <?=$colorCard;?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?=$iconCard;?></i>
              </div>
              
              <h4 class="card-title" ><?=$nombrePluralPersonal?> </h4>
              <h4 align="right" >
                <a  style="height:10px;width: 10px;"  href='<?=$urlListPersonalRetirado;?>' >
                <i class="material-icons" title="Lista Personal Retirado">rowing</i>
              </a>  
              </h4>
              

            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table" id="tablePaginator">
                  <thead>
                      <tr>
                        <th></th>
                        <th>Código</th>
                        <th>Nombre</th>      
                        <th>Ci</cIte></th>
                        <th>Cargo</th>
                        <th>Oficina-Area</th>                        
                        <th>Tipo Personal</th>                                                
                        <th>F.Ingreso</th>
                        <th>Estado</th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                      if ($cod_estadopersonal==3) {
                          $labelestadoPersonal='<span class="badge badge-danger">';
                      }elseif ($cod_estadopersonal==1) {
                        $labelestadoPersonal='';
                      }else $labelestadoPersonal='<span class="badge badge-warning">';
                      ?>
                      <tr>
                        <td  class="td-actions text-right">    
                          <a href='<?=$urlprintPersonal;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-info">
                            <i class="material-icons" style="color:black" title="Ficha de Personal">print</i>
                          </a>
                        </td>
                        <td><?=$codigo?></td>
                        <td><?=$paterno;?> <?=$materno;?> <?=$primer_nombre;?></td>      
                        <td><?=$ci;?>-<?=$ci_lugar_emision;?></td>
                        <td><?=$xcargo;?></td>
                        <td><?=$xuonombre;?>-<?=$xarea;?></td>                        
                        <td><?=$xcod_tipopersonal;?></td>                                              
                        <td><?=$fecha_ingreso;?></td>
                        <td><?=$labelestadoPersonal.$xestado."</span>";?></td>
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1 and $cod_estadopersonal!=3){
                          ?>
                            <a href='<?=$urlFormPersonalContratos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-info">
                              <i class="material-icons" title="Contratos">assignment</i>
                            </a>
                            <a href='<?=$urlFormPersonalAreaDistribucion;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-warning">            
                              <i class="material-icons" title="Area-Distribución" style="color:black;">call_split</i>
                            </a>
                            <?php
                              }else{?>
                                  <a href='<?=$urlFormPersonalContratos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-info">
                                <i class="material-icons" title="Contratos">assignment</i>
                            </a>
                              <?php }
                            ?>        
                        </td>
                          
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>
                            <a href='<?=$urlFormPersonal;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeletePersonal;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                            <?php
                              }
                            ?>                      
                        </td>
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1 and $bandera==1 and $cod_estadopersonal!=3){
                          ?>
                            <div class="dropdown">
                              <button class="btn btn-primary dropdown-toggle" type="button" id="editar_otros" data-toggle="dropdown" aria-extended="true">
                                <i class="material-icons" title="Editar"><?=$iconEdit;?></i>                        
                                <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu" role="menu" aria-labelledby="editar_otros">
                                <!-- <li role="presentation" class="dropdown-header"><small>U.O.</small></li> -->
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=1&codigo_p=<?=$codigo;?>'><small>Oficina/Area</small></a></li>
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=2&codigo_p=<?=$codigo;?>'><small>Cargo</small></a></li>
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=3&codigo_p=<?=$codigo;?>'><small>Grado Acad</small></a></li>
                                <li role="presentation"><a role="item" href='<?=$urledit_uo_area_personal;?>&codigo_item=4&codigo_p=<?=$codigo;?>'><small>Haber Básico</small></a></li>
                              </ul>
                            </div>                            
                            <?php
                              }
                            ?>                      
                        </td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>                                      
                </table>
              </div>
            </div>
          </div>
          <?php

          if($globalAdmin==1){
          ?>
  				<div class="card-footer fixed-bottom">               
                <button class="btn btn-success"  onClick="location.href='<?=$urlsaveWSPersonal;?>'">Actualizar Datos</button>
          </div>
          <div id="resultados">
            <ul></ul>
          </div>
          <?php
          }
          ?>
  
        </div>
      </div>  
    </div>
</div>