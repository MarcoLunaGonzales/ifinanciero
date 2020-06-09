<?php
//include("functionsGeneral.php");

$globalUserX=$_SESSION['globalUser'];
//echo $globalUserX;
$globalPerfilX=$_SESSION['globalPerfil'];
$globalNameUserX=$_SESSION['globalNameUser'];
$globalNombreUnidadX=$_SESSION['globalNombreUnidad'];
$globalNombreAreaX=$_SESSION['globalNombreArea'];

$menuModulo=$_SESSION['modulo'];
switch ($menuModulo) {
  case 1:
   $nombreModulo="RRHH";
   $estiloMenu="rojo";
  break;
  case 2:
  $nombreModulo="Activos Fijos";
   $estiloMenu="amarillo";
  break;
  case 3:
  $nombreModulo="Contabilidad";
   $estiloMenu="celeste";
  break;
  case 4:
  $nombreModulo="Presupuestos / Solicitudes";
   $estiloMenu="verde";
  break;
}

if($menuModulo==0){
 ?><script>window.location.href="index.php";</script><?php
}
?>

<div class="sidebar" data-color="purple" data-background-color="<?=$estiloMenu?>" data-image="assets/img/scz.jpg">
      <div class="logo">
        <a href="http://www.creative-tim.com" class="simple-text logo-mini">
          <img src="assets/img/logo_ibnorca1.fw.png" width="30" />
        </a>
        <a href="index.php" class="simple-text logo-normal">
          ADM & FIN & OP
        </a>
      </div>
      <div class="sidebar-wrapper">
        <div class="user">
          <div class="photo">
            <img src="assets/img/faces/persona1.png" />
          </div>
          <div class="user-info">
            <a data-toggle="collapse" href="#collapseExample" class="username">
              <span>
                <?=$globalNameUserX;?>
                <!--b class="caret"></b-->
              </span>
            </a>
          </div>
        </div>

        <ul class="nav">
          <li class="nav-item ">
            <a class="nav-link" href="index.php?opcion=homeModulo">
              <i class="material-icons">home</i>
              <p> <?=$nombreModulo?>
              </p>
            </a>
          </li>  
          <?php 
          switch ($menuModulo) {
              case 1:
              ?>
          <!-- TABLAS RRHH-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#pagesExamples">
              <i class="material-icons">fullscreen</i>
              <p> Tablas RRHH
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="pagesExamples">
              <ul class="nav">              
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=areasLista">
                    <span class="sidebar-mini"> A </span>
                    <span class="sidebar-normal"> Areas</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=uoLista">
                    <span class="sidebar-mini"> OF </span>
                    <span class="sidebar-normal"> Oficinas</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listAreas_contabilizacion">
                    <span class="sidebar-mini"> AC </span>
                    <span class="sidebar-normal"> Areas Contabilización</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=cargosLista">
                    <span class="sidebar-mini"> C </span>
                    <span class="sidebar-normal"> Cargos</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=tiposCargosLista">
                    <span class="sidebar-mini"> TCA </span>
                    <span class="sidebar-normal"> Tipos Cargos</span>
                  </a>
                </li>
        
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=tipospersonalLista">
                    <span class="sidebar-mini"> TP </span>
                    <span class="sidebar-normal"> Tipos Personal</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=estadospersonalLista">
                    <span class="sidebar-mini"> EP </span>
                    <span class="sidebar-normal"> Estados Personal</span>
                  </a>
                </li>            
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=tipos_generoLista">
                    <span class="sidebar-mini"> G </span>
                    <span class="sidebar-normal"> Genero </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=personalLista">
                    <span class="sidebar-mini"> P </span>
                    <span class="sidebar-normal"> Personal</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=estadosplanillaLista">
                    <span class="sidebar-mini"> EPL </span>
                    <span class="sidebar-normal"> Estados Planilla</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=tipos_aporteafpLista">
                    <span class="sidebar-mini"> TA </span>
                    <span class="sidebar-normal"> Tipos de Aportes</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=tipos_afpLista">
                    <span class="sidebar-mini"> TAFP </span>
                    <span class="sidebar-normal"> Tipos de Afp</span>
                  </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listEscalaAntiguedad">
                      <span class="sidebar-mini"> E </span>
                      <span class="sidebar-normal"> Escalas Antiguedad </span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listPoliticaDescuento">
                      <span class="sidebar-mini"> PDR </span>
                      <span class="sidebar-normal"> Politicas de Descuento por retrasos </span>
                    </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=personalFinExterna">
                    <span class="sidebar-mini"> PFE </span>
                    <span class="sidebar-normal"> Personal Financiaci&oacute;n Externa </span>
                  </a>
                </li>


              </ul>
            </div>
          </li>
         
          <!--TRANSACCIONES RRHH-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#transaccHHRR">
              <i class="material-icons">menu</i>
              <p> Transacciones RRHH
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="transaccHHRR">
              <ul class="nav">
                
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listAnticipoPersonalMes">
                      <span class="sidebar-mini"> A </span>
                      <span class="sidebar-normal"> Anticipos de Personal</span>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listBonos">
                      <span class="sidebar-mini"> B </span>
                      <span class="sidebar-normal"> Bonos</span>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listDescuentos">
                      <span class="sidebar-mini"> D </span>
                      <span class="sidebar-normal"> Descuentos</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listDotacion">
                      <span class="sidebar-mini"> D </span>
                      <span class="sidebar-normal"> Dotaciones </span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listRcivaPersonalMes">
                      <span class="sidebar-mini"> F110 </span>
                      <span class="sidebar-normal"> Formulario 110</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listRefrigerio">
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Refrigerios </span>
                    </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=planillasSueldoPersonal">
                    <span class="sidebar-mini"> PS </span>
                    <span class="sidebar-normal"> Planilla De Sueldos </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=planillasAguinaldosPersonal">
                    <span class="sidebar-mini"> PS </span>
                    <span class="sidebar-normal"> Planilla De Aguinaldos </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=finiquitos_list">
                    <span class="sidebar-mini"> F </span>
                    <span class="sidebar-normal"> Finiquitos </span>
                  </a>
                </li>
                
              </ul>
            </div>
          </li>

          <!--REPORTES RRHH-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#reportesRRHH">
              <i class="material-icons">assessment</i>
              <p> Reportes RRHH
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="reportesRRHH">
              <ul class="nav">                
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=rptCambiosPersonal">
                    <span class="sidebar-mini"> R </span>
                    <span class="sidebar-normal"> Histórico Del Personal</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=rptDistribucionSueldos">
                    <span class="sidebar-mini"> R </span>
                    <span class="sidebar-normal"> Distribución Planilla Por Area</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=rptIngresos_Descuentos">
                    <span class="sidebar-mini"> R </span>
                    <span class="sidebar-normal"> Ingresos & Descuentos </span>
                  </a>
                </li>

              </ul>
            </div>
          </li>

              <?php
              break;
              case 2:
          ?><!--TABLAS ACTIVOS FIJOS-->          
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#tablasAF">
              <i class="material-icons">fullscreen</i>
              <p> Tablas Activos Fijos
                <b class="caret"></b>
              </p>
            </a>

            <div class="collapse" id="tablasAF">
              <ul class="nav">              
                 <!-- <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listUbicaciones">
                    <span class="sidebar-mini"> UAF </span>
                    <span class="sidebar-normal"> Ubicaciones</span>
                  </a>
                </li> -->
                <li class="nav-item ">
                 <a class="nav-link" href="?opcion=provLista">
                   <span class="sidebar-mini"> P </span>
                   <span class="sidebar-normal"> Proveedores</span>
                 </a>
               </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listDepreciaciones">
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Rubros/Depreciaciones</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listaTiposBienes">
                      <span class="sidebar-mini"> TB </span>
                      <span class="sidebar-normal"> Tipos de Bienes</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=activosfijosLista">
                      <span class="sidebar-mini"> AF </span>
                      <span class="sidebar-normal"> Activos Fijos</span>
                    </a>
                </li>
              </ul>
            </div>
          </li>
          <!--TRANSACCIONES ACTIVOS FIJOS-->          
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#transaccAF">
              <i class="material-icons">menu</i>
              <p> Transacciones Activos Fijos
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="transaccAF">
              <ul class="nav">

                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=ejecutarDepreciacionLista">
                      <span class="sidebar-mini"> D </span>
                      <span class="sidebar-normal"> Depreciaciones</span>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=afEnCustodia">
                      <span class="sidebar-mini"> AC </span>
                      <span class="sidebar-normal"> AF En Custodia</span>
                    </a>
                </li>

                
                <?php
                $valor=8;
                $VariableConf=obtenerValorConfiguracion($valor);
                  if($globalUserX==$VariableConf){
                ?>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=aftransaccion">
                      <span class="sidebar-mini"> TAF </span>
                      <span class="sidebar-normal"> Transacción De AF</span>
                    </a>
                </li>
                <?php
                  }
                ?>
                
                

              </ul>
            </div>
          </li>
          <!--REPORTES AF-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#reportesAF">
              <i class="material-icons">assessment</i>
              <p> Reportes Activos Fijos
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="reportesAF">
              <ul class="nav">
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptactivosfijos">
                      <span class="sidebar-mini"> AF </span>
                      <span class="sidebar-normal"> Activos Fijos</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptactivosfijosAsignados">
                      <span class="sidebar-mini"> AFA </span>
                      <span class="sidebar-normal"> Activos Fijos Asignados</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptxrubrosxmes">
                      <span class="sidebar-mini"> RM </span>
                      <span class="sidebar-normal"> Por Rubro por Mes</span>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptactivosfijosxunidad">
                      <span class="sidebar-mini"> RU </span>
                      <span class="sidebar-normal"> Por Unidad, Area y Responsable</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=afEtiquetasFiltro">
                      <span class="sidebar-mini"> EI </span>
                      <span class="sidebar-normal"> Etiquetas Impresión</span>
                    </a>
                </li>

              </ul>
            </div>
          </li>
          <?php
              break;
              case 3:
              ?>
          <!--TABLAS CONTABILIDAD-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#tablesConta">
              <i class="material-icons">fullscreen</i>
              <p> Tablas Contabilidad
                <b class="caret"></b>
              </p>
            </a>

            <div class="collapse" id="tablesConta">
              <ul class="nav">
                
                <!--li class="nav-item ">
                  <a class="nav-link" href="?opcion=listConfigCuentas">
                    <span class="sidebar-mini"> CC </span>
                    <span class="sidebar-normal"> Configuracion de Cuentas </span>
                  </a>
                </li-->
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=configuracionEstadosCuenta">
                    <span class="sidebar-mini"> CEC </span>
                    <span class="sidebar-normal"> Configuracion Estados de Cuentas</span>
                  </a>
                </li>                
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listDistribucionGastoArea">
                    <span class="sidebar-mini">DGA</span>
                    <span class="sidebar-normal">Distribucion Gastos Área</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listDistribucionGasto">
                    <span class="sidebar-mini">DGO</span>
                    <span class="sidebar-normal">Distribucion Gastos Oficina</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listDosificaciones">
                    <span class="sidebar-mini"> D </span>
                    <span class="sidebar-normal"> Dosificaciones</span>
                  </a>
                </li>  
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listEntidades">
                    <span class="sidebar-mini">E</span>
                    <span class="sidebar-normal">Entidades</span>
                  </a>
                </li>

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlanCuentas">
                    <span class="sidebar-mini">PC</span>
                    <span class="sidebar-normal">Plan de Cuentas</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlanCuentasCajaChica">
                    <span class="sidebar-mini">PCH</span>
                    <span class="sidebar-normal">Plan de Cuentas Caja Chica</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlanCuentasSolicitudesFacturacion">
                    <span class="sidebar-mini">PCTP</span>
                    <span class="sidebar-normal">Plan de Cuentas para tipos de pago</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlanCuentasAreas">
                    <span class="sidebar-mini">PCA</span>
                    <span class="sidebar-normal">Plan de Cuentas para Areas</span>
                  </a>
                </li>

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPartidasPres">
                    <span class="sidebar-mini">PP</span>
                    <span class="sidebar-normal">Partidas Presupuestarias</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=tipoDeCambio">
                    <span class="sidebar-mini">TC</span>
                    <span class="sidebar-normal">Tipo de Cambio de Monedas</span>
                  </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=ListaTipoCajaChica">
                      <span class="sidebar-mini">TCC</span>
                      <span class="sidebar-normal">Instancias Caja Chica</span>
                    </a>
                </li>                
              </ul>
            </div>
          </li>
          <!--TRANSACCIONES CONTABILIDAD-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#transaccConta">
              <i class="material-icons">menu</i>
              <p> Transacciones Contabilidad
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="transaccConta">
              <ul class="nav">
                
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listComprobantes">
                    <span class="sidebar-mini"> C </span>
                    <span class="sidebar-normal"> Comprobantes </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listComprobantes2">
                    <span class="sidebar-mini"> C2 </span>
                    <span class="sidebar-normal"> Comprobantes 2 </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listComprobantesRegistrados">
                    <span class="sidebar-mini"> A </span>
                    <span class="sidebar-normal"> Aprobaciones de comprobantes </span>
                  </a>
                </li> 
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=principal_CajaChica">
                    <span class="sidebar-mini"> CC </span>
                    <span class="sidebar-normal"> Caja Chica </span>
                  </a>
                </li>
                 <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listFacturasServicios_conta">
                    <span class="sidebar-mini"> SF </span>
                    <span class="sidebar-normal"> Solicitudes de Facturación</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listFacturasGeneradas">
                    <span class="sidebar-mini"> FG </span>
                    <span class="sidebar-normal"> Facturas Generadas</span>
                  </a>
                </li>
                <!--li class="nav-item ">
                  <a class="nav-link" href="?opcion=ListaRendiciones">
                    <span class="sidebar-mini"> MR </span>
                    <span class="sidebar-normal"> Mis Rendiciones </span>
                  </a>
                </li-->

              </ul>
            </div>
          </li>
          <!--REPORTES CONTABILIDAD-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#reportesConta">
              <i class="material-icons">assessment</i>
              <p> Reportes Contabilidad
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="reportesConta">
              <ul class="nav">
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportesComprobantes">
                    <span class="sidebar-mini"> LD </span>
                    <span class="sidebar-normal"> Libro Diario</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportesMayores">
                    <span class="sidebar-mini"> LM </span>
                    <span class="sidebar-normal"> Libro Mayor</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportesLibroCompras">
                    <span class="sidebar-mini"> LC </span>
                    <span class="sidebar-normal"> Libro Compras</span>
                  </a>
                </li> 
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportesLibroVentas">
                    <span class="sidebar-mini"> LV </span>
                    <span class="sidebar-normal"> Libro Ventas</span>
                  </a>
                </li>    
                <li class="nav-item ">
                  <a class="nav-link" href="caja_chica/rpt_proveedores_print.php" target="_blank">
                    <span class="sidebar-mini"> P </span>
                    <span class="sidebar-normal"> Proveedores</span>
                  </a>
                </li>    
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportesEstadoCuentas">
                    <span class="sidebar-mini"> P </span>
                    <span class="sidebar-normal"> Estado de Cuentas</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportesBalanceGeneral">
                    <span class="sidebar-mini"> BG </span>
                    <span class="sidebar-normal"> Balance General</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportesEstadoResultados">
                    <span class="sidebar-mini"> ER </span>
                    <span class="sidebar-normal"> Estado de Resultados</span>
                  </a>
                </li>                         
              </ul>
            </div>
          </li>
          
          <!--UTILITARIOS-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#utilitarios">
              <i class="material-icons">build</i>
              <p> Utilitarios
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="utilitarios">
              <ul class="nav">

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listGestionTrabajo">
                    <span class="sidebar-mini"> CG </span>
                    <span class="sidebar-normal"> Cambiar Gestion de Trabajo </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listUnidadOrganizacional">
                    <span class="sidebar-mini"> CU </span>
                    <span class="sidebar-normal"> Cambiar Oficina de Trabajo </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listNotificacionesSistema">
                    <span class="sidebar-mini"> NT </span>
                    <span class="sidebar-normal"> Notificaciones</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=mesCurso">
                    <span class="sidebar-mini"> MC </span>
                    <span class="sidebar-normal"> Mes de Trabajo </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=configuracionDeRetenciones">
                    <span class="sidebar-mini"> CR </span>
                    <span class="sidebar-normal"> Configuracion de Retenciones</span>
                  </a>
                </li> 
                <li class="nav-item ">
                  <a class="nav-link" href="utilitarios/rptClientesGeneral.php" target="_blank">
                    <span class="sidebar-mini"> CG </span>
                    <span class="sidebar-normal"> Clientes Listado</span>
                  </a>
                </li>                         
                <li class="nav-item ">
                  <a class="nav-link" href="utilitarios/rptProveedoresGeneral.php" target="_blank">
                    <span class="sidebar-mini"> P </span>
                    <span class="sidebar-normal"> Proveedores Listado</span>
                  </a>
                </li>                         
              </ul>
            </div>
          </li>
          

              <?php
              break;
              case 4:
              ?>
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#tablasPresSol">
              <i class="material-icons">fullscreen</i>
              <p> Tablas P / S
                <b class="caret"></b>
              </p>
            </a>

            <div class="collapse" id="tablasPresSol">
              <ul class="nav">
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listCheques">
                    <span class="sidebar-mini"> CH </span>
                    <span class="sidebar-normal"> Cheques</span>
                  </a>
                </li> 
               <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listDiasCreditoProveedores">
                    <span class="sidebar-mini"> DC </span>
                    <span class="sidebar-normal"> D&iacute;as de Cr&eacute;dito</span>
                  </a>
                </li>             
                 <li class="nav-item ">
                  <a class="nav-link" href="index.php?opcion=listTarifarioServicios">
                    <span class="sidebar-mini"> TS </span>
                    <span class="sidebar-normal"> Tarifario de Servicios TCP</span>
                  </a>
                </li> 
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlanCuentasSolicitudesRecursos">
                    <span class="sidebar-mini">PSR</span>
                    <span class="sidebar-normal">Plan de Cuentas Solicitud de Recursos</span>
                  </a>
                </li>   
              </ul>
            </div>
          </li>    
          <!--Solicitud de recursos-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#SMServiciosSEC">
              <i class="material-icons">content_paste</i>
              <p> Propuestas Capacitación
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="SMServiciosSEC">
              <ul class="nav">
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlantillasCostos">
                    <span class="sidebar-mini"> PTC </span>
                    <span class="sidebar-normal"> Plantillas de Presupuesto SEC</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlantillasCostosAdmin">
                    <span class="sidebar-mini"> GPT </span>
                    <span class="sidebar-normal"> Gesti&oacute;n de Plantillas Presupuesto SEC</span>
                  </a>
                </li>

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSimulacionesCostos">
                    <span class="sidebar-mini"> SC </span>
                    <span class="sidebar-normal"> Propuestas de Presupuesto SEC</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSimulacionesCostosAdmin">
                    <span class="sidebar-mini"> GS </span>
                    <span class="sidebar-normal"> Gesti&oacute;n de Propuestas SEC</span>
                  </a>
                </li> 
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=solicitud_facturacion_costos">
                    <span class="sidebar-mini"> SF </span>
                    <span class="sidebar-normal">Solicitudes de Facturación Estudiantes</span>
                  </a>
                </li> 
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=solicitud_facturacion_costos_empresas">
                    <span class="sidebar-mini"> SF </span>
                    <span class="sidebar-normal">Solicitudes de Facturación Empresas</span>
                  </a>
                </li> 
              </ul>
            </div>
          </li>

          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#SMServiciosTCPTCS">
              <i class="material-icons">content_paste</i>
              <p> Propuestas TCP/TCS
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="SMServiciosTCPTCS">
              <ul class="nav">
                 <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlantillasServicios">
                    <span class="sidebar-mini"> PSR </span>
                    <span class="sidebar-normal"> Plantillas  de Servicios TCP - TCS</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlantillasServiciosAdmin">
                    <span class="sidebar-mini"> GPS </span>
                    <span class="sidebar-normal"> Gesti&oacute;n de Plantillas Servicios TCP - TCS</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSimulacionesServicios">
                    <span class="sidebar-mini"> SS </span>
                    <span class="sidebar-normal"> Propuestas de Servicios TCP - TCS</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSimulacionesServAdmin">
                    <span class="sidebar-mini"> GSS </span>
                    <span class="sidebar-normal"> Gesti&oacute;n de Propuestas Servicios TCP - TCS</span>
                  </a>
                </li>

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listServiciosPresupuestos">
                    <span class="sidebar-mini"> S </span>
                    <span class="sidebar-normal"> Servicios</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#SMSolicitudes">
              <i class="material-icons">content_paste</i>
              <p> Solicitudes
                <b class="caret"></b>
              </p>
            </a>

            <div class="collapse" id="SMSolicitudes">
              <ul class="nav">

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSolicitudRecursos">
                    <span class="sidebar-mini"> SR </span>
                    <span class="sidebar-normal"> Solicitudes de Recursos</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSolicitudRecursosAdmin">
                    <span class="sidebar-mini"> GSR </span>
                    <span class="sidebar-normal"> Gesti&oacute;n Solicitudes Recursos</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPagoProveedores">
                    <span class="sidebar-mini"> PP </span>
                    <span class="sidebar-normal"> Pagos</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPagoProveedoresAdmin">
                    <span class="sidebar-mini"> GP </span>
                    <span class="sidebar-normal"> Gestión de Pagos</span>
                  </a>
                </li>
                <!-- <li class="nav-item ">
                  <a class="nav-link" href="?opcion=register_solicitudfacturacion_manual">
                    <span class="sidebar-mini"> SFM </span>
                    <span class="sidebar-normal"> Solicitud de Facturacion Manual</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSolicitud_facturacion_normas">
                    <span class="sidebar-mini"> SFVN</span>
                    <span class="sidebar-normal"> Solicitud de Facturacion Venta Normas</span>
                  </a>
                </li> -->
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listFacturasServicios">
                    <span class="sidebar-mini"> SF </span>
                    <span class="sidebar-normal"> Solicitudes de Facturación </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listFacturasServiciosAdmin">
                    <span class="sidebar-mini"> GSF </span>
                    <span class="sidebar-normal"> Gesti&oacute;n Solicitudes de Facturación </span>
                  </a>
                </li>
                
                <!-- <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listFacturasServicios_costos">
                    <span class="sidebar-mini"> SFC </span>
                    <span class="sidebar-normal"> Solicitudes de Facturación Capacitación</span>
                  </a>
                </li> -->
                                          

              </ul>
            </div>
          </li>  

          <!--REPORTES RRHH-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#reportesPresupuestoSol">
              <i class="material-icons">assessment</i>
              <p> Reportes P / S
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="reportesPresupuestoSol">
              <ul class="nav">
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportePlanificacion">
                    <span class="sidebar-mini"> RP </span>
                    <span class="sidebar-normal"> Reportes Planificacion SEC</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=reportePlanificacionEC">
                    <span class="sidebar-mini"> RPT </span>
                    <span class="sidebar-normal"> Reportes Planificacion TCP/TCS</span>
                  </a>
                </li>                
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listObligacionesPago">
                    <span class="sidebar-mini"> OP </span>
                    <span class="sidebar-normal"> Obligaciones de Pago</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>

              <?php
              break;
          }
          ?>

        </ul>
      </div>
    </div>
