<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMesActivo=$_SESSION['globalMes'];
$dbh = new Conexion();
// Preparamos
$stmt = $dbh->prepare("SELECT pcc.codigo,pcc.cod_plancuenta,pc.numero,pc.nombre,pc.nivel from flujo_efectivo_gruposcuentas pcc,plan_cuentas pc 
where pcc.cod_plancuenta=pc.codigo and pcc.cod_flujoefectivogrupo=1");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigoFila);
$stmt->bindColumn('cod_plancuenta', $cod_plancuenta);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('nivel', $nivel);

// Preparamos
$stmt2 = $dbh->prepare("SELECT pcc.codigo,pcc.cod_plancuenta,pc.numero,pc.nombre,pc.nivel from flujo_efectivo_gruposcuentas pcc,plan_cuentas pc 
where pcc.cod_plancuenta=pc.codigo and pcc.cod_flujoefectivogrupo=2");
// Ejecutamos
$stmt2->execute();
// bindColumn
$stmt2->bindColumn('codigo', $codigoFila);
$stmt2->bindColumn('cod_plancuenta', $cod_plancuenta);
$stmt2->bindColumn('numero', $numero);
$stmt2->bindColumn('nombre', $nombre);
$stmt2->bindColumn('nivel', $nivel);

// Preparamos
$stmt3 = $dbh->prepare("SELECT pcc.codigo,pcc.cod_plancuenta,pc.numero,pc.nombre,pc.nivel from flujo_efectivo_gruposcuentas pcc,plan_cuentas pc 
where pcc.cod_plancuenta=pc.codigo and pcc.cod_flujoefectivogrupo=3");
// Ejecutamos
$stmt3->execute();
// bindColumn
$stmt3->bindColumn('codigo', $codigoFila);
$stmt3->bindColumn('cod_plancuenta', $cod_plancuenta);
$stmt3->bindColumn('numero', $numero);
$stmt3->bindColumn('nombre', $nombre);
$stmt3->bindColumn('nivel', $nivel);

?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-text">
                  <a href="#" onclick="cambiarTresDivPantallaClase('list_div_1','list_div_2','list_div_3','btn-orange')">
                    <div id="button_list_div_1" class="card-text bg-default btn-orange text-white">
                      <h4 class=""><?=obtenerNombreFlujoEfectivoGrupo(1)?></h4>
                    </div>
                  </a>
                  <a href="#" onclick="cambiarTresDivPantallaClase('list_div_2','list_div_1','list_div_3','btn-orange')">
                     <div id="button_list_div_2" class="card-text bg-default text-white">
                        <h4 class=""><?=obtenerNombreFlujoEfectivoGrupo(2)?></h4>
                     </div>
                  </a>
                  <a href="#" onclick="cambiarTresDivPantallaClase('list_div_3','list_div_1','list_div_2','btn-orange')">
                     <div id="button_list_div_3" class="card-text bg-default text-white">
                        <h4 class=""><?=obtenerNombreFlujoEfectivoGrupo(3)?></h4>
                     </div>
                  </a>
                  <h4 class="card-title float-right"><b>Gestón de Cuentas</b> - <b class="color-orange">Flujo Efectivo (Reporte)</b></h4>
                </div>
                <div class="card-body">
                  <div id="list_div_1">
                    <table class="table table-condesed" id="tablePaginator100">
                      <thead>
                        <tr class="bg-orange">
                          <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Nivel</th>                          
                          <th width="8%" class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php
                        $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                        ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="font-weight-bold">[<?=$numero;?>]</td>
                          <td><?=$nombre;?></td>
                          <td class="font-weight-bold"><?=$nivel?></td>
                          <td class="td-actions text-right">
                           <button title="Eliminar Cuenta" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>?codigo=<?=$codigoFila;?>')">
                                <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                          </td>
                        </tr>
                        <?php
                          $index++;
                        }
                        ?>
                      </tbody>
                    </table>
                    <br><br><br><br><br><br><br><br>
                   </div><!--Fin List 1--> 
                   <div id="list_div_2" class="d-none">
                     <table class="table table-condesed" id="tablePaginator100NoFidexHead">
                      <thead>
                        <tr class="bg-orange">
                          <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Nivel</th>                          
                          <th width="8%" class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $index=1;
                        while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
                        ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="font-weight-bold">[<?=$numero;?>]</td>
                          <td><?=$nombre;?></td>
                          <td class="font-weight-bold"><?=$nivel?></td>
                          <td class="td-actions text-right">
                           <button title="Eliminar Cuenta" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>?codigo=<?=$codigoFila;?>')">
                                <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                          </td>
                        </tr>
                        <?php
                          $index++;
                        }
                        ?>
                      </tbody>
                    </table>
                    <br><br><br><br><br><br><br><br>
                   </div><!--FIN DIV 2--> 
                  <div id="list_div_3" class="d-none">
                    <table class="table table-condesed" id="tablePaginator100_2">
                      <thead>
                        <tr class="bg-orange">
                          <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Nivel</th>                          
                          <th width="8%" class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                       <?php
                        $index=1;
                        while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) {
                        ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="font-weight-bold">[<?=$numero;?>]</td>
                          <td><?=$nombre;?></td>
                          <td class="font-weight-bold"><?=$nivel?></td>
                          <td class="td-actions text-right">
                           <button title="Eliminar Cuenta" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>?codigo=<?=$codigoFila;?>')">
                                <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                          </td>
                        </tr>
                        <?php
                          $index++;
                        }
                        ?>
                      </tbody>
                    </table>
                    <br><br><br><br><br><br><br><br>
                   </div><!--Fin List 3-->  
                </div>
              </div>
              <div class="card-footer fixed-bottom col-sm-9">
                <a href="<?=$urlRegisterCuenta1;?>" class="btn btn-default" id="boton_registrar_plan">REGISTRAR <?=obtenerNombreFlujoEfectivoGrupo(1)?></a>
                <a href="<?=$urlRegisterCuenta2;?>" class="btn btn-default d-none" id="boton_registrar_plan_2">REGISTRAR <?=obtenerNombreFlujoEfectivoGrupo(2)?></a>
                <a href="<?=$urlRegisterCuenta3;?>" class="btn btn-default d-none" id="boton_registrar_plan_3">REGISTRAR <?=obtenerNombreFlujoEfectivoGrupo(3)?></a>
                <a href="index.php?opcion=listPlanCuentas" target="_blank" class="btn btn-danger">Volver</a>
              </div>    
            </div>
          </div>  
        </div>
    </div>
