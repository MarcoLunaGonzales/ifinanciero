<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$cod_tcc=$cod_tcc;
$stmtTCC = $dbh->prepare("SELECT nombre,cod_personal,(SELECT CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal) as responsable from tipos_caja_chica where  codigo= $cod_tcc");
$stmtTCC->execute();
$resultTCC=$stmtTCC->fetch();
$nombre_tipoCC=$resultTCC['nombre'];
$responsable_tipoCC=$resultTCC['responsable'];
$cod_personal_tipoCC=$resultTCC['cod_personal'];


//verificamos si todos sus contratos estan finalizados
$sqlControlador="SELECT cod_estado from caja_chica where cod_tipocajachica=$cod_tcc ORDER BY codigo desc";
$stmtControlador = $dbh->prepare($sqlControlador);
$stmtControlador->execute();
$resultControlador=$stmtControlador->fetch();
$cod_estado_aux=$resultControlador['cod_estado'];
if($cod_estado_aux==2 || $cod_estado_aux==null || $codigo>0){
    //por is es edit
    if ($codigo > 0){
        $codigo=$codigo;

        $stmt = $dbh->prepare("SELECT * from caja_chica where codigo =:codigo");
        //Ejecutamos;
        $stmt->bindParam(':codigo',$codigo);
        $stmt->execute();
        $result = $stmt->fetch();
        $cod_tcc = $result['cod_tipocajachica'];
        $fecha = $result['fecha'];
        $numero = $result['numero'];    
        $monto_inicio = $result['monto_inicio'];    
        // $monto_reembolso = $result['monto_reembolso'];    
        $observaciones = $result['observaciones'];    
        $cod_personal = $result['cod_personal']; 
           
    } else {
        //para el numero correlativo y el monto anterior
        $sql="SELECT numero,codigo,monto_inicio from caja_chica where cod_estadoreferencial=1 and cod_tipocajachica=$cod_tcc order by codigo desc LIMIT 1";
        $stmtCC = $dbh->prepare($sql);
        // echo $sql;
        $stmtCC->execute();
        $resultCC = $stmtCC->fetch();
        $numero_caja_chica_aux = $resultCC['numero'];
        $monto_inicio_anterior = $resultCC['monto_inicio'];
        $cod_cajachica = $resultCC['codigo'];//sacamos el monto anterior de caja chica
        if($numero_caja_chica_aux==null){
            $numero_caja_chica_aux=0;
        }
        //sacamos saldo
        $monto_anterior_x=importe_total_cajachica($cod_cajachica);
        $monto_anterior=$monto_inicio_anterior+$monto_anterior_x;
        //$codigo=$codigo_caja_chica_aux+1;
        $cod_tipocajachica = 0;
        $fecha = "";
        $numero = $numero_caja_chica_aux+1;
        $monto_inicio = $monto_anterior;    
        // $monto_reembolso = 0;    
        $observaciones = null;    
        $cod_personal = 0;    
        $cod_estadoreferencial = 1;
    }

    ?>

    <div class="content">
    	<div class="container-fluid">
    		<div class="col-md-12">
    		  <form id="form1" class="form-horizontal" action="<?=$urlSaveCajaChica;?>" method="post" onsubmit="return valida(this)">
                <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
    			<div class="card">
    			  <div class="card-header <?=$colorCard;?> card-header-text">
    				<div class="card-text">
    				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularCajaChica;?></h4>
    				</div>
    			  </div>
    			  <div class="card-body ">			
                       
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Instancia caja chica</label>
                          <div class="col-sm-8">
                            <div class="form-group">
                                <input class="form-control" type="hidden" name="cod_tipocajachica" id="cod_tipocajachica"  value="<?=$cod_tcc;?>" />
                                <input class="form-control" type="text" readonly="readonly" name="nombre_tipocajachica" id="nombre_tipocajachica"  value="<?=$nombre_tipoCC;?>" />
                            </div>
                          </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha" id="fecha" required="true" value="<?=$fecha;?>" />
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Nro. Correlativo</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="number" name="numero" id="numero" value="<?=$numero;?>" readonly onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                            </div>
                        </div> <!--fin campo fecha numero-->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Monto Inicio</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="monto_inicio" id="monto_inicio" value="<?=$monto_inicio;?>" step="any" required/>
                                </div>
                            </div>
                            <!-- <label class="col-sm-2 col-form-label">Monto Reembolso</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="monto_reembolso" id="monto_reembolso" value="<?=$monto_reembolso;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                            </div> -->
                        </div><!--monto inicio y reembolso-->
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Responsable</label>
                          <div class="col-sm-8">
                            <div class="form-group">
                                <input class="form-control" type="text"  value="<?=$responsable_tipoCC?>" readonly="readonly">
                                <input class="form-control" type="text" name="cod_personal" id="cod_personal" value="<?=$cod_personal_tipoCC?>" hidden="hidden">                                
                            </div>
                          </div>
                        </div>


                        <div class="row">
                            <label class="col-sm-2 col-form-label">Detalle</label>
                            <div class="col-sm-7">
                            <div class="form-group">
                                <textarea class="form-control rounded-0" name="observaciones" id="observaciones" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true"><?=$observaciones;?></textarea>

                                <!-- <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
                            </div>
                            </div>
                        </div><!--fin campo nombre -->              
    			  </div>
    			  <div class="card-footer ml-auto mr-auto">
    				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
    				<a href="<?=$urlListCajaChica;?>&codigo=<?=$cod_tcc?>" class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
    			  </div>
    			</div>
    		  </form>
    		</div>
    	
    	</div>
    </div>
    <script type="text/javascript">
        function valida(f) {
          var ok = true;
          
            // if(f.elements["monto_inicio"].value == 0)
            // {    
            //     var msg = "El Monto de 'Inicio' no debe ser menor a '0'...\n";
            //     ok = false;
            // }else{
                if(f.elements["observaciones"].value == null)
                {    
                    var msg = "El Campo 'Detalle' no debe ir vacío...\n";
                    ok = false;
                }    
            // }            
            if(ok == false)
                alert(msg);
            return ok;
        }
    </script>
<?php 
}else{
    $flagSuccess=false;
    showAlertSuccessErrorCajachica($flagSuccess,$urlListCajaChica."&codigo=".$cod_tcc);
}
?>