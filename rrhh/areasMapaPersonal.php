<?php

require_once 'conexion.php';
require_once 'conexion.php';
require_once 'styles.php';

?>
<style>
    /* Estilos para carpetas abiertas */
    .jstree-open > .jstree-anchor {
        font-weight: bold;
    }
    /* Estilos para carpetas cerradas */
    .jstree-closed > .jstree-anchor {
        color: #999;
    }
    /* Estilos para nodos de hoja */
    .jstree-leaf > .jstree-anchor {
        font-style: italic;
    }

</style>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="card">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">map</i>
                        </div>
                        <div class="text-center">
                            <h4 class="card-title"><b>Mapa de Áreas y Cargos [Personal]</b></h4>
                        </div>
                    </div>
                    <div class="card-body pl-5 pr-5">
                        <div class="table-responsive" id="treeContainer">
                        </div>
                    </div>
                    <div class="card-footer ml-auto mr-auto">
                        <a href="?opcion=areasLista" class="btn btn-danger">Volver</a>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

<!-- LIBRERIA PARA ESTRUCTURA ARBOL -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

<script>
    /**
     * Funcionalidad para generar el arbol de áreas
     */
    $(document).ready(function() {
        $.ajax({
            url: 'rrhh/ajaxMapaAreasCargosPersonal.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log(response)
                // Modificar la estructura de datos para asignar iconos diferentes
                response.data.forEach(function(node) {
                    if (node.children.length > 0) {
                        node.icon = 'jstree-folder';
                    } else {
                        node.icon = 'jstree-file';
                    }
                });
                
                // Construir el árbol
                $('#treeContainer').jstree('destroy').jstree({
                    core: {
                    data: response.data
                    },
                    plugins: ['themes', 'types'],
                    types: {
                        folder: {
                            icon: 'jstree-folder' // Icono para los nodos de carpeta
                        },
                        file: {
                            icon: 'jstree-tree' // Icono para los nodos de archivo
                        }
                    }
                });
                // Abrir todas las pestañas del árbol
                setTimeout(function() {
                    $('#treeContainer').jstree('open_all');
                }, 2000); // 2000 milisegundos = 2 segundos
            },
            error: function() {
                console.log('Error al obtener los datos');
            }
        });
    });

</script>
