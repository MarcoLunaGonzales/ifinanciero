<?php

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
              </ul>
            </div>
          </li>

          <!--REPORTES CONTABILIDAD-->
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
                    <a class="nav-link" href="?opcion=rptxrubrosxmes">
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Por Rubro por Mes</span>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="?opcion=rptactivosfijos">
                      <span class="sidebar-mini"> R </span>
                      <span class="sidebar-normal"> Activos Fijos</span>
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

        </ul>
      </div>
    </div>
