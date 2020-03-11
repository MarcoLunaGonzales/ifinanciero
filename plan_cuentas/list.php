<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM $table p where cod_estadoreferencial=1 and p.nivel=1 order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);

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
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <!-- <input type="text" name="formulario" id="formulario" class="form-control"> -->
                    <table class="table" id="data_cuentas_2">
                      <thead>
                        <tr>
                          <th>-</th>
                          <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Padre</th>
                          <th>Nivel</th>
                          <th>Tipo</th>
                          <th>Cuentas Auxiliares</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <!-- <tbody id="resultado"> -->
                      <tbody>
                      <?php
            						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $nombre=formateaPlanCuenta($nombre, $nivel);
                          $linkAdd="";
                          if($nivel<5){
                            $linkAdd="<a href='".$urlRegister2.$codigo."'><i class='material-icons' style='color:orange;' title='Registrar Hijo'>add_circle_outline</i></a>";
                          }
                          $imgCuentaAuxiliar="";
                          if($cuentaAuxiliar==1){
                            $imgCuentaAuxiliar="<a href='index.php?opcion=listCuentasAux&codigo=$codigo' rel='tooltip' class='<?=$buttonCeleste;?>'><i class='material-icons' style='color:blue'>check_circle_outline</i></a>";
                          }
                        ?>
                        <tr >
                          <td></td>
                          <td align="center"><b><?=$index;?></b></td>
                          <td><?=$numero;?></td>
                          <td><?=$nombre;?><?=$linkAdd;?></td>
                          <td><?=$codPadre;?></td>
                          <td><?=$nivel;?></td>
                          <td><?=$codTipoCuenta;?></td>
                          <td class="td-actions text-center"><?=$imgCuentaAuxiliar;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($globalAdmin==1){
                            ?>
                            <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
                            <?php
                            }
                            ?>
                          </td>
                        </tr>
                        <?php
                          $stmt2 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                          (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=2 and p.cod_padre='$numero' ");
                          $stmt2->execute();                      
                          $stmt2->bindColumn('codigo', $codigo_2);
                          $stmt2->bindColumn('numero', $numero_2);
                          $stmt2->bindColumn('nombre', $nombre_2);
                          $stmt2->bindColumn('cod_padre', $codPadre_2);
                          $stmt2->bindColumn('nivel', $nivel_2);
                          $stmt2->bindColumn('cod_tipocuenta', $codTipoCuenta_2);
                          $stmt2->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_2);
                          $index_2=1;
                          while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
                            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
                            $linkAdd_2="";
                            if($nivel_2<5){
                              $linkAdd_2="<a href='".$urlRegister2.$codigo_2."'><i class='material-icons' style='color:orange;' title='Registrar Hijo'>add_circle_outline</i></a>";
                            }
                            $imgCuentaAuxiliar_2="";
                            if($cuentaAuxiliar==1){
                              $imgCuentaAuxiliar_2="<a href='index.php?opcion=listCuentasAux&codigo=$codigo' rel='tooltip' class='<?=$buttonCeleste;?>'><i class='material-icons' style='color:blue'>check_circle_outline</i></a>";
                            }
                            
                          ?>
                          <tr >
                            <td></td>
                            <td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$index_2;?></td>
                            <td><?=$numero_2;?></td>
                            <td><?=$nombre_2;?><?=$linkAdd_2;?></td>
                            <td><?=$codPadre_2;?></td>
                            <td><?=$nivel_2;?></td>
                            <td><?=$codTipoCuenta_2;?></td>
                            <td class="td-actions text-center"><?=$imgCuentaAuxiliar_2;?></td>
                            <td class="td-actions text-right">
                              <?php
                              if($globalAdmin==1){
                              ?>
                              <a href='<?=$urlEdit;?>&codigo=<?=$codigo_2;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                <i class="material-icons"><?=$iconEdit;?></i>
                              </a>
                              <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo_2;?>')">
                                <i class="material-icons"><?=$iconDelete;?></i>
                              </button>
                              <?php
                              }
                              ?>
                            </td>
                          </tr>
                          <?php
                            $stmt3 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                            (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=3 and p.cod_padre='$numero_2' ");
                            $stmt3->execute();                      
                            $stmt3->bindColumn('codigo', $codigo_3);
                            $stmt3->bindColumn('numero', $numero_3);
                            $stmt3->bindColumn('nombre', $nombre_3);
                            $stmt3->bindColumn('cod_padre', $codPadre_3);
                            $stmt3->bindColumn('nivel', $nivel_3);
                            $stmt3->bindColumn('cod_tipocuenta', $codTipoCuenta_3);
                            $stmt3->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_3);
                            $index_3=1;
                            while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) {
                              $nombre_3=formateaPlanCuenta($nombre_3, $nivel_3);
                              $linkAdd_3="";
                              if($nivel_3<5){
                                $linkAdd_3="<a href='".$urlRegister2.$codigo_3."'><i class='material-icons' style='color:orange;' title='Registrar Hijo'>add_circle_outline</i></a>";
                              }
                              $imgCuentaAuxiliar_3="";    
                              if($cuentaAuxiliar==1){
                                $imgCuentaAuxiliar_3="<a href='index.php?opcion=listCuentasAux&codigo=$codigo' rel='tooltip' class='<?=$buttonCeleste;?>'><i class='material-icons' style='color:blue'>check_circle_outline</i></a>";
                              }                          
                            ?>
                            <tr onclick="verDetalleCuenta_nivel3('<?=$index_3?>')">
                              <td></td>
                              <td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$index_3;?></td>
                              <td><?=$numero_3;?></td>
                              <td><?=$nombre_3;?><?=$linkAdd_3;?></td>
                              <td><?=$codPadre_3;?></td>
                              <td><?=$nivel_3;?></td>
                              <td><?=$codTipoCuenta_3;?></td>
                              <td class="td-actions text-center"><?=$imgCuentaAuxiliar_3;?></td>
                              <td class="td-actions text-right">
                                <?php
                                if($globalAdmin==1){
                                ?>
                                <a href='<?=$urlEdit;?>&codigo=<?=$codigo_3;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo_3;?>')">
                                  <i class="material-icons"><?=$iconDelete;?></i>
                                </button>
                                <?php
                                }
                                ?>
                              </td>
                            </tr>
                            <?php
                              $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                              (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='$numero_3' ");
                              $stmt4->execute();                      
                              $stmt4->bindColumn('codigo', $codigo_4);
                              $stmt4->bindColumn('numero', $numero_4);
                              $stmt4->bindColumn('nombre', $nombre_4);
                              $stmt4->bindColumn('cod_padre', $codPadre_4);
                              $stmt4->bindColumn('nivel', $nivel_4);
                              $stmt4->bindColumn('cod_tipocuenta', $codTipoCuenta_4);
                              $stmt4->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_4);
                              $index_4=1;
                              while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
                                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                                $linkAdd_4="";
                                if($nivel_4<5){
                                  $linkAdd_4="<a href='".$urlRegister2.$codigo_4."'><i class='material-icons' style='color:orange;' title='Registrar Hijo'>add_circle_outline</i></a>";
                                }
                                $imgCuentaAuxiliar_4="";   
                                if($cuentaAuxiliar==1){
                                  $imgCuentaAuxiliar_4="<a href='index.php?opcion=listCuentasAux&codigo=$codigo' rel='tooltip' class='<?=$buttonCeleste;?>'><i class='material-icons' style='color:blue'>check_circle_outline</i></a>";
                                }                             
                              ?>
                              <tr onclick="verDetalleCuenta_nivel4('<?=$index_4?>')" class="det-cuentas4-<?=$index_3?>" style="display:none">
                                <td></td>
                                <td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$index_4;?></td>
                                <td><?=$numero_4;?></td>
                                <td><?=$nombre_4;?><?=$linkAdd_4;?></td>
                                <td><?=$codPadre_4;?></td>
                                <td><?=$nivel_4;?></td>
                                <td><?=$codTipoCuenta_4;?></td>
                                <td class="td-actions text-center"><?=$imgCuentaAuxiliar_4;?></td>
                                <td class="td-actions text-right">
                                  <?php
                                  if($globalAdmin==1){
                                  ?>
                                  <a href='<?=$urlEdit;?>&codigo=<?=$codigo_4;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                  <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo_4;?>')">
                                    <i class="material-icons"><?=$iconDelete;?></i>
                                  </button>
                                  <?php
                                  }
                                  ?>
                                </td>
                              </tr>
                              <?php
                                $stmt5 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=5 and p.cod_padre='$numero_4' ");
                                $stmt5->execute();                      
                                $stmt5->bindColumn('codigo', $codigo_5);
                                $stmt5->bindColumn('numero', $numero_5);
                                $stmt5->bindColumn('nombre', $nombre_5);
                                $stmt5->bindColumn('cod_padre', $codPadre_5);
                                $stmt5->bindColumn('nivel', $nivel_5);
                                $stmt5->bindColumn('cod_tipocuenta', $codTipoCuenta_5);
                                $stmt5->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_5);
                                $index_5=1;
                                while ($row = $stmt5->fetch(PDO::FETCH_BOUND)) {
                                  $nombre_5=formateaPlanCuenta($nombre_5, $nivel_5);
                                  $linkAdd_5="";                                  
                                  $imgCuentaAuxiliar_5="";  
                                  if($cuentaAuxiliar==1){
                                    $imgCuentaAuxiliar_5="<a href='index.php?opcion=listCuentasAux&codigo=$codigo' rel='tooltip' class='<?=$buttonCeleste;?>'><i class='material-icons' style='color:blue'>check_circle_outline</i></a>";
                                  }                                
                                ?>
                                <tr class="det-cuentas5-<?=$index_4?>" style="display:none">
                                  <td></td>
                                  <td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$index_5;?></td>
                                  <td><?=$numero_5;?></td>
                                  <td><?=$nombre_5;?><?=$linkAdd_5;?></td>
                                  <td><?=$codPadre_5;?></td>
                                  <td><?=$nivel_5;?></td>
                                  <td><?=$codTipoCuenta_5;?></td>
                                  <td class="td-actions text-center"><?=$imgCuentaAuxiliar_5;?></td>
                                  <td class="td-actions text-right">
                                    <?php
                                    if($globalAdmin==1){
                                    ?>
                                    <a href='<?=$urlEdit;?>&codigo=<?=$codigo_5;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                      <i class="material-icons"><?=$iconEdit;?></i>
                                    </a>
                                    <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo_5;?>')">
                                      <i class="material-icons"><?=$iconDelete;?></i>
                                    </button>
                                    <?php
                                    }
                                    ?>
                                  </td>
                                </tr>

                        <?php
                                $index_5++;
                                }
                              $index_4++;
                              }
                              $index_3++;
                            }
                            $index_2++;
                          }
            							$index++;
            						}
                      ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <!--div class="card-footer ml-auto mr-auto"-->
              <div class="card-footer fixed-bottom">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>'">Registrar</button>
              </div>		  
            </div>
          </div>  
        </div>
    </div>

    <!-- <script>
      const cuentas = [
      {nombre: 'cuenta 1',valor:500},
      {nombre: 'cuenta 2',valor:501},
      {nombre: 'cuenta 2',valor:502},
      {nombre: 'cuenta 2',valor:503},
      ];
      const formulario =document.querySelector('#formulario');
      const resultado =document.querySelector('#resultado');
      const filtrar = () =>{
        resultado.innerHTML ='';
        const texto=formulario.value.toLowerCase();
        for(let cuenta of cuentas){
          let nombre=cuenta.nombre.toLowerCase();
          if(nombre.indexOf(texto)!==-1){
            resultado.innerHTML + ='<tr><td>${cuenta.nombre}</td><td>${cuenta.valor}</td></tr>';
          }else{
            resultado.innerHTML + ='<tr><td>cuenta No encotrada</td></tr>';
          }
        } 
      }
      formulario.addEventListener('keyup',filtrar);
    </script>
 -->

 <script >
  function verDetalleCuenta_nivel4(index){
    if($(".det-cuentas5-"+index).is(":visible")){
      $(".det-cuentas5-"+index).hide();
    }else{
      $(".det-cuentas5-"+index).show();
    }
  }
  function verDetalleCuenta_nivel3(index){
    if($(".det-cuentas4-"+index).is(":visible")){
      $(".det-cuentas4-"+index).hide();
    }else{
      $(".det-cuentas4-"+index).show();
    }
  }
 </script>