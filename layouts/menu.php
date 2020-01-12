<?php
include("functionsGeneral.php");

$globalUserX=$_SESSION['globalUser'];
//echo $globalUserX;
$globalPerfilX=$_SESSION['globalPerfil'];
$globalNameUserX=$_SESSION['globalNameUser'];
$globalNombreUnidadX=$_SESSION['globalNombreUnidad'];
$globalNombreAreaX=$_SESSION['globalNombreArea'];

?>

<div class="sidebar" data-color="purple" data-background-color="red" data-image="assets/img/scz.jpg">
      <div class="logo">
        <a href="http://www.creative-tim.com" class="simple-text logo-mini">
          <img src="assets/img/logo_ibnorca1.fw.png" width="30" />
        </a>
        <a href="http://www.creative-tim.com" class="simple-text logo-normal">
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

          <!--TABLAS ACTIVOS FIJOS-->          
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#tablasAF">
              <i class="material-icons">fullscreen</i>
              <p> Tablas Activos Fijos
                <b class="caret"></b>
              </p>
            </a>

            <div class="collapse" id="tablasAF">
              <ul class="nav">              
                 <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listUbicaciones">
                    <span class="sidebar-mini"> UAF </span>
                    <span class="sidebar-normal"> Ubicaciones</span>
                  </a>
                </li>
                <li class="nav-item ">
                 <a class="nav-link" href="?opcion=provLista">
                   <span class="sidebar-mini"> UAF </span>
                   <span class="sidebar-normal"> Proveedores</span>
                 </a>
               </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listDepreciaciones">
                      <span class="sidebar-mini"> DAF </span>
                      <span class="sidebar-normal"> Rubros/Depreciaciones</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=listaTiposBienes">
                      <span class="sidebar-mini"> DAF </span>
                      <span class="sidebar-normal"> Tipos de Bienes</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=activosfijosLista">
                      <span class="sidebar-mini"> DAF </span>
                      <span class="sidebar-normal"> Activos Fijos</span>
                    </a>
                </li>
              </ul>
            </div>
          </li>

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
                
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listConfigCuentas">
                    <span class="sidebar-mini"> CC </span>
                    <span class="sidebar-normal"> Configuracion de Cuentas </span>
                  </a>
                </li>

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlanCuentas">
                    <span class="sidebar-mini"> PC </span>
                    <span class="sidebar-normal"> Plan de Cuentas </span>
                  </a>
                </li>

                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPartidasPres">
                    <span class="sidebar-mini"> PP </span>
                    <span class="sidebar-normal"> Partidas Presupuestarias </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=tipoDeCambio">
                    <span class="sidebar-mini"> TC </span>
                    <span class="sidebar-normal"> Tipo de Cambio de Monedas </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlantillasCostos">
                    <span class="sidebar-mini"> PTC </span>
                    <span class="sidebar-normal"> Plantillas de Costo </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listPlantillasCostosAdmin">
                    <span class="sidebar-mini"> GPT </span>
                    <span class="sidebar-normal"> Gesti&oacute;n de Plantillas de Costo </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listDistribucionGasto">
                    <span class="sidebar-mini"> DGP </span>
                    <span class="sidebar-normal"> Distribucion de Gastos</span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
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
                    <span class="sidebar-mini"> UO </span>
                    <span class="sidebar-normal"> Unidades Organizacionales</span>
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
                  <a class="nav-link" href="?opcion=tiposContratosLista">
                    <span class="sidebar-mini"> TCO </span>
                    <span class="sidebar-normal"> Tipos Contratos</span>
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
                  <a class="nav-link" href="?opcion=aportes_patronalesLista">
                    <span class="sidebar-mini"> AP </span>
                    <span class="sidebar-normal"> Aportes Patronales</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=aportes_laboralesLista">
                    <span class="sidebar-mini"> AL </span>
                    <span class="sidebar-normal"> Aportes Laborales</span>
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
                      <span class="sidebar-mini"> D </span>
                      <span class="sidebar-normal"> AF En Custodia</span>
                    </a>
                </li>

                
                <?php
                $valor=8;
                $VariableConf=obtieneValorConfig($valor);
                  if($globalUserX==$VariableConf){
                ?>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=aftransaccion">
                      <span class="sidebar-mini"> D </span>
                      <span class="sidebar-normal"> Transacción De AF</span>
                    </a>
                </li>
                <?php
                  }
                ?>
                

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
                  <a class="nav-link" href="?opcion=listComprobantesRegistrados">
                    <span class="sidebar-mini"> A </span>
                    <span class="sidebar-normal"> Aprobaciones de comprobantes </span>
                  </a>

                </li> 


            
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSimulacionesCostos">
                    <span class="sidebar-mini"> SC </span>
                    <span class="sidebar-normal"> Simulaciones de Costos</span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=listSimulacionesCostosAdmin">
                    <span class="sidebar-mini"> GS </span>
                    <span class="sidebar-normal"> Gesti&oacute;n de Simulaciones</span>
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
                    <span class="sidebar-normal"> Planilla de Sueldos </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="?opcion=personalFinExterna">
                    <span class="sidebar-mini"> PE </span>
                    <span class="sidebar-normal"> Personal Financiaci&oacute;n Externa </span>
                  </a>
                </li>
                
              </ul>
            </div>
          </li>
          <!--Solicitud de recursos-->
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#solicitudes">
              <i class="material-icons">content_paste</i>
              <p> Solicitudes de Recursos
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="solicitudes">
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
              </ul>
            </div>
          </li>
          <!--REPORTES CONTABILIDAD-->

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
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Activos Fijos</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptactivosfijosAsignados">
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Activos Fijos Asignados</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptxrubrosxmes">
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Por Rubro por Mes</span>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptactivosfijosxunidad">
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Por Unidad, Area y Responsable</span>
                    </a>
                </li>

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
              </ul>
            </div>
          </li>


          

          <!--RECURSOS HUMANOS>          
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#bono">
              <i class="material-icons">menu</i>
              <p> Recursos Humanos 2
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="bono">
              <ul class="nav">

                

              </ul>
            </div>
          </li-->




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
                  <a class="nav-link" href="?opcion=configuracionEstadosCuenta">
                    <span class="sidebar-mini"> CEC </span>
                    <span class="sidebar-normal"> Configuracion Estados de Cuentas</span>
                  </a>
                </li>                          
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
