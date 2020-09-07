<div class="modal fade modal-primary" id="modalFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-info card-header-text">
        <div class="card-text">
          <h5>DOCUMENTOS DE RESPALDO</h5>      
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
           <p class="text-muted"><small>Los archivos se subir&aacute;n al servidor cuando se GUARDE El Gasto de Caja Chica</small></p>
            <div class="row col-sm-11 div-center">
              <table class="table table-warning table-bordered table-condensed">
                <thead>
                  <tr>
                    <th class="small" width="30%">Tipo de Documento <a href="#" title="Otro Documento" class="btn btn-primary btn-round btn-sm btn-fab float-left" onClick="agregarFilaArchivosAdjuntosCabecera()"><i class="material-icons">add</i></a></th>
                    <th class="small">Obligatorio</th>
                    <th class="small" width="35%">Archivo</th>
                    <th class="small">Descripción</th>                  
                  </tr>
                </thead>
                <tbody id="tabla_archivos">
                  <?php
                  // $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                  // $stmtArchivo->execute();
                  // $filaA=0;
                  // while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                  //   $filaA++;
                  //   $codigoX=$rowArchivo['idClaDocumento'];
                  //   $nombreX=$rowArchivo['Documento'];
                  //   $ObligatorioX=$rowArchivo['Obligatorio'];
                  //   $Obli='<i class="material-icons text-danger">clear</i> NO';
                  //   if($ObligatorioX==1){
                  //   $Obli='<i class="material-icons text-success">done</i> SI<input type="hidden" id="obligatorio_file'.$filaA.'" value="1">';
                  //   } ?>
                    <!--  <tr>
                       <td class="text-left"><input type="hidden" name="codigo_archivo<?=$filaA?>" id="codigo_archivo<?=$filaA?>" value="<?=$codigoX;?>"><input type="hidden" name="nombre_archivo<?=$filaA?>" id="nombre_archivo<?=$filaA?>" value="<?=$nombreX;?>"><?=$nombreX;?></td>
                       <td class="text-center"><?=$Obli?></td>
                       <td class="text-right">
                         <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                         <span class="input-archivo">
                           <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                         </span>
                         <label title="Ningún archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo
                         </label>
                       </td>    
                       <td><?=$nombreX;?></td>
                     </tr> --> <?php
                  // }

                  if(isset($codigo)){
                    $sql="SELECT * From archivos_adjuntos_cajachica where cod_cajachica_detalle=$codigo";
                    // echo $sql;
                    $stmtArchivo = $dbh->prepare($sql); //2708 //2708 localhost
                    $stmtArchivo->execute();
                    $filaE=0;
                    $filaA=0;
                    while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                      $filaE++;
                      $filaA++;
                      $codigoArchivoX=$rowArchivo['codigo'];
                      $codigoX=$rowArchivo['cod_tipoarchivo'];
                      $nombreX=$rowArchivo['descripcion'];
                      $urlArchivo=$rowArchivo['direccion_archivo'];
                      $ObligatorioX=0;
                      $Obli='<i class="material-icons text-danger">clear</i> NO';
                      ?>
                      <tr id="fila_archivo<?=$filaA?>">
                        <td class="text-left"><input type="hidden" name="codigo_archivoregistrado<?=$filaE?>" id="codigo_archivoregistrado<?=$filaE?>" value="<?=$codigoArchivoX;?>">Otros Documentos</td>
                        <td class="text-center"><?=$Obli?></td>                 
                        <td class="text-right">
                            <small id="existe_archivo_cabecera<?=$filaA?>"></small>
                              <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                              <span class="input-archivo">
                                <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                              </span>                         
                            <div class="btn-group">
                              <a href="#" class="btn btn-button btn-sm" >Registrado</a>  
                              <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  
                              <a href="#" title="Quitar" class="btn btn-danger btn-sm" onClick="quitarArchivoSistemaAdjunto_solfac(<?=$filaA?>,<?=$codigoArchivoX;?>,1,2)"><i class="material-icons">delete_outline</i></a>
                            </div>     
                          </td>    
                        <td><?=$nombreX;?></td>
                      </tr> 
                        <?php                   
                    }
                  }else{
                    $filaE=0;
                    $filaA=0;
                  }

                  ?>       
                </tbody>
              </table>
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntos" name="cantidad_archivosadjuntos">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="" class="btn btn-success" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalFile_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b><h5>DOCUMENTOS DE RESPALDO</h5></b></h3>
      </div>
      <div class="modal-body">
        <div class="row">          
          <div class="col-sm-12" id="contenedor_archivos_respaldo_cajachica">
            
          </div>
        </div>
      </div>
      <div class="modal-footer">       
      </div>
    </div>
  </div>
</div>