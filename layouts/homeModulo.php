<?php
switch ($codModulo) {
  case 1:
   $nombreModulo="RRHH";
   $cardTema="card-themes";
   $iconoTitulo="local_atm";
   $estiloHome="#DC5143";
   $fondoModulo="fondo-dashboard-recursoshumanos";
  break;
  case 2:
   $nombreModulo="Activos Fijos";
   $cardTema="card-snippets";
   $iconoTitulo="home_work";
   $estiloHome="#DCB943";
   $fondoModulo="fondo-dashboard-activos";
  break;
  case 3:
   $nombreModulo="Contabilidad";
   $cardTema="card-templates";
   $iconoTitulo="insert_chart_outlined";
   $estiloHome="#1B82DD";
   $fondoModulo="fondo-dashboard-contabilidad";
  break;
  case 4:
   $nombreModulo="Presupuestos / Solicitudes";
   $cardTema="card-guides";
   $iconoTitulo="list_alt";
   $estiloHome="#4FA54F";
   $fondoModulo="fondo-dashboard-solicitudes";
  break;
}

?>


       
   
<section class="after-loop">
  <div class="container">
    <div class="div-center text-center">
      
     <img src="assets/img/logo_ibnorca.png" width="160" height="160" alt="">
      <h3>Modulo <?=$nombreModulo?></h3>
      <p>
        <a href="index.php" class="btn btn-primary btn-lg">IR A LA PAGINA DE INICIO</a>
      </p>
      <!--<p>
        <a href="layouts/homeModulo2.php?mod=<?=$codModulo?>" target="_blank" class="btn btn-warning"><i class="material-icons">access_time</i> REPORTE DE INGRESOS</a>
      </p>-->
    </div>


    <!--<div class="mb-5 mb-lg-0 mx-auto">
       <a href="#" class="after-loop-item card border-0 <?=$cardTema?> shadow-lg">
          <div class="card-body d-flex align-items-end flex-column text-right">
             <h4>Modulo <?=$nombreModulo?></h4>
             <p class="w-75">Descripción del Modulo!</p>
             <i class="material-icons"><?=$iconoTitulo?></i>
          </div>
       </a>
     </div>-->
 
  </div>
</section>