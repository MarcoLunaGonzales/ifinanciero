<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

/*
$gestion=$_POST["gestion"];
$nameGestion=nameGestion($gestion);
*/
//recibimos las variables
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];

$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}


// $sql="SELECT codigo,cod_tipo_identificacion,identificacion,cod_lugar_emision,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,haber_basico,CONCAT_WS(' ',paterno,materno,primer_nombre)as personal,cod_tipoafp,celular,telefono,email,email_empresa,ing_planilla
// from personal  where cod_estadopersonal=1 and cod_estadoreferencial=1 and cod_area in ($areaString) and cod_unidadorganizacional in ($unidadOrgString) order by paterno ";  


$stmtActivos = $dbh->prepare("SELECT p.*,
      CONCAT_WS(' ',paterno,materno,primer_nombre) as personal,
      (select ga.nombre from personal_grado_academico ga where ga.codigo=p.cod_grado_academico) as nombre_grado_academico,
      (select ca.nombre from cargos ca where ca.codigo=p.cod_cargo) as nombre_cargo,
      (select uo.nombre from unidades_organizacionales uo where uo.codigo=p.cod_unidadorganizacional) as nombre_uo,
      (select a.nombre from areas a where a.codigo=p.cod_area) as nombre_area,
      tp.nombre as tipopersonal,
      uo.nombre as oficina,
      a.nombre as area,
      pga.nombre as grado_academico,
      ta.nombre as tipo_afp,
      taa.nombre as tipoaporteafp,
      b.nombre as banco
    from personal p 
    left join tipos_personal tp ON tp.codigo=p.cod_tipopersonal 
    left join unidades_organizacionales uo ON uo.codigo=p.cod_unidadorganizacional 
    left join areas a ON a.codigo=p.cod_area
    left join personal_grado_academico pga ON pga.codigo=p.cod_grado_academico 
    left join tipos_afp ta ON ta.codigo=p.cod_tipoafp 
    left join tipos_aporteafp taa ON taa.codigo=p.cod_tipoaporteafp 
    left join bancos b ON b.codigo=p.cod_banco
    where p.cod_estadopersonal=1 
    and p.cod_estadoreferencial=1 
    and p.cod_area in ($areaString) 
    and p.cod_unidadorganizacional in ($unidadOrgString) 
    order by p.paterno");
$stmtActivos->execute();

?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <h6 class="card-title">Exportar como:</h6>
                  </div>
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Personal 
                  </h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
                  <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <?php
                    $html='<table class="table table-bordered table-condensed" id="tablePaginatorFixed_personal">'.
                      '<thead class="bg-secondary text-white">'.
                        '<tr >'.
                          '<th class="font-weight-bold">-</th>'.
                          '<th class="font-weight-bold">Personal</th>'.
                          '<th class="font-weight-bold">C.I.</th>'.
                          '<th class="font-weight-bold">Of/Area</th>'.
                          
                          '<th class="font-weight-bold">F. Ing.</th>'.
                          '<th class="font-weight-bold">Cargo</th>'.
                          '<th class="font-weight-bold">Básico</th>'.
                          '<th class="font-weight-bold">Afp</th>'.
                          '<th class="font-weight-bold">Tel.</th>'. 
                          '<th class="font-weight-bold">Email</th>'.
                          
                          '<th class="font-weight-bold">Nro casillero</th>'. 
                          '<th class="font-weight-bold">Identificación</th>'. 
                          '<th class="font-weight-bold">Pais</th>'. 
                          '<th class="font-weight-bold">Departamento</th>'. 
                          '<th class="font-weight-bold">Ciudad</th>'.    
                          '<th class="font-weight-bold">Estado Civil</th>'.
                          '<th class="font-weight-bold">Genero</th>'.        
                          '<th class="font-weight-bold">Fecha Nacimiento</th>'. 
                          '<th class="font-weight-bold">Direccion</th>'.        
                          '<th class="font-weight-bold">Personal de Confianza</th>'. 
                          '<th class="font-weight-bold">Tipo Personal</th>'.      
                          '<th class="font-weight-bold">Oficina</th>'.            
                          '<th class="font-weight-bold">Area</th>'.               
                          '<th class="font-weight-bold">Grado Académico</th>'.    
                          '<th class="font-weight-bold">Jubilado</th>'.         
                          '<th class="font-weight-bold">Nua / Cua Asignado</th>'.  
                          '<th class="font-weight-bold">AFP</th>'.     
                          '<th class="font-weight-bold">Tipo de Aporte AFP</th>'.   
                          '<th class="font-weight-bold">Nro. Seguro</th>'.     
                          '<th class="font-weight-bold">Cod. Dependiente RC-IVA</th>'.    
                          '<th class="font-weight-bold">Banco</th>'.       
                          '<th class="font-weight-bold">Cuenta Bancaria</th>'.    
                        '</tr>'.
                      '</thead>'.
                      '<tbody>';
                        //<?php  
                        $contador = 0;
                        while ($row = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                          if($row['identificacion']=="")$row['identificacion']=0;
                          $contador++;   
                          $html.='<tr>'.
                            '<td class="text-center small">'.$contador.'</td>'.
                            '<td class="text-left small">'.$row['personal'].'</td>'.
                            '<td class="text-left small">'.obtenerNombreIdentificacionPersona($row['cod_tipo_identificacion'],1).' '.$row['identificacion'].' '.obtenerlugarEmision($row['cod_lugar_emision'],1).'</td>'.
                            '<td class="text-left small">'.abrevUnidad_solo($row['cod_unidadorganizacional']).'/'.abrevArea_solo($row['cod_area']).'</td>'.
                            
                            '<td class="text-right small">'.$row['ing_planilla'].'</td>'.
                            '<td class="text-left small">'.nameCargo($row['cod_cargo']).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($row['haber_basico']).'</td>'.
                            '<td class="text-left small">'.obtenerNameAfp($row['cod_tipoafp'],1).'</td>'.
                            '<td class="text-left small">'.trim($row['telefono'].' - '.$row['celular'],' - ').'</td>'.
                            '<td class="text-left small">'.trim($row['email'].' - '.$row['email_empresa'],' - ').'</td>'.
                            
                            '<td class="text-left small">'.$row['nro_casillero'].'</td>'.
                            '<td class="text-left small">'.$row['identificacion'].'</td>'.
                            '<td class="text-left small">'.obtenerNombreNacionalidadPersona($row['cod_pais'],2).'</td>'.
                            '<td class="text-left small">'.obtenerlugarEmision($row['cod_departamento'],2).'</td>'.
                            '<td class="text-left small">'.obtenerNombreCiudadPersona($row['cod_ciudad']).'</td>'.
                            '<td class="text-left small">'.obtenerNombreEstadoCivilPersona($row['cod_estadocivil']).'</td>'.
                            '<td class="text-left small">'.obtenerNombreGeneroPersona($row['cod_genero']).'</td>'.
                            '<td class="text-left small">'.$row['fecha_nacimiento'].'</td>'.
                            '<td class="text-left small">'.$row['direccion'].'</td>'.
                            '<td class="text-left small">'.($row['personal_confianza'] == '0' ? 'NO' : 'SI').'</td>'.
                            '<td class="text-left small">'.$row['tipopersonal'].'</td>'.
                            '<td class="text-left small">'.$row['oficina'].'</td>'.
                            '<td class="text-left small">'.$row['area'].'</td>'.
                            '<td class="text-left small">'.$row['grado_academico'].'</td>'.
                            '<td class="text-left small">'.($row['jubilado'] == '0' ? 'NO' : 'SI').'</td>'.
                            '<td class="text-left small">'.$row['nua_cua_asignado'].'</td>'.
                            '<td class="text-left small">'.$row['tipo_afp'].'</td>'.
                            '<td class="text-left small">'.$row['tipoaporteafp'].'</td>'.
                            '<td class="text-left small">'.$row['nro_seguro'].'</td>'.
                            '<td class="text-left small">'.$row['codigo_dependiente'].'</td>'.
                            '<td class="text-left small">'.$row['banco'].'</td>'.
                            '<td class="text-left small">'.$row['cuenta_bancaria'].'</td>'.
                          '</tr>';
                        } 
                      $html.='</tbody>'.
                    '</table>';
                    echo $html;
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
