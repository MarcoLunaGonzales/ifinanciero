<?php

date_default_timezone_set('America/La_Paz');

class Conexion extends PDO {      
    public function __construct() {
        //Sobreescribo el método constructor de la clase PDO.
        try{
            $DATABASE_HOST = "lpsit.ibnorca.org";
            $DATABASE_NAME = "bdifinanciero";
            $DATABASE_PORT = "4606";
            $DATABASE_USER = "ingresobd";
            $DATABASE_PASSWORD = "ingresoibno";
            // Oficial
            parent::__construct('mysql:host='.$DATABASE_HOST.';dbname='.$DATABASE_NAME.';port='.$DATABASE_PORT, $DATABASE_USER, $DATABASE_PASSWORD,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            
        }catch(PDOException $e){
            echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
            exit;
        }
    } 
} 
/**
 * Función para buscar en el array por la propiedad
 * @param ArrayFacturaSuscripcion
 * @return Bolean Codigo de Factura Venta
 */
function buscarArrayFactura($array, $cod_facturaventa) {
    foreach ($array as $elemento) {
        if ($elemento['codigo'] == $cod_facturaventa) {
            return true;
        }
    }
    return false;
}
// Verificar Tabla Suscripciones
function buscarSuscripcion($cod_solicitudfacturacion){
    $dbh = new Conexion();
    $sql = "SELECT fst.codigo, fst.cod_factura 
            FROM facturas_suscripcionestienda fst
            WHERE fst.cod_solicitudfacturacion = '$cod_solicitudfacturacion'";
    $stmtSuscripcion = $dbh->prepare($sql);
    $stmtSuscripcion->execute();
    $filasEncontradas = $stmtSuscripcion->rowCount();

    if ($filasEncontradas > 0) {
        return true;
    } else {
        return false;
    }
}
// Verificar Tabla Ventas Normas
function buscarVentaNorma($cod_solicitudfacturacion, $cod_facturaventadetalle){
    $dbh = new Conexion();
    $sql = "SELECT vn.IdVentaNormas, vn.idNorma 
            FROM ibnorca.ventanormas vn
            WHERE vn.idSolicitudfactura = '$cod_solicitudfacturacion'
            OR vn.idFacturaDetalle = '$cod_facturaventadetalle'";
    $stmtVentaNorma = $dbh->prepare($sql);
    $stmtVentaNorma->execute();
    $filasEncontradas = $stmtVentaNorma->rowCount();

    if ($filasEncontradas > 0) {
        return true;
    } else {
        return false;
    }
}

// Variables
$fecha_inicio = empty($_GET['fecha_inicio']) ? date('Y-m-d') : $_GET['fecha_inicio']; 
$fecha_fin    = empty($_GET['fecha_fin']) ? date('Y-m-d') : $_GET['fecha_fin']; 
$mostrar      = empty($_GET['mostrar']) ? 1 : $_GET['mostrar']; 
$claServicio  = empty($_GET['cla_servicio']) ? "" : $_GET['cla_servicio'];

$dbh = new Conexion();
// FACTURA SUSCRIPCIÓN
$sql = "SELECT fv.codigo, fv.nro_factura, sfd.codigo as cod_facturadetalle, fv.fecha_factura, fv.razon_social, fv.nit, vn.IdVentaNormas, vn.Catalogo, vn.idNorma,
        CASE
        WHEN vn.Catalogo='N' THEN 
            (select CONCAT(v.abreviatura,' ',v.nombre) from v_normas v where v.codigo=sus.id_norma)
        WHEN vn.Catalogo='I' THEN 
            (select CONCAT_WS(vi.abreviatura,' ',vi.nombre) from v_normas_int vi where vi.codigo=sus.id_norma)
        WHEN vn.Catalogo='ISO' THEN
        (SELECT CONCAT(i.reference,' ',t.value) FROM ibnorca_entidades.isos i 
            INNER JOIN ibnorca_entidades.iso_titles t ON i.iso_id = t.iso_id AND t.lang = 'en' WHERE i.iso_id = sus.id_norma)
        ELSE ''
        END as nombrenorma, fv.cod_solicitudfacturacion
        from solicitudes_facturacion sf, solicitudes_facturaciondetalle sfd, facturas_suscripcionestienda sus, facturas_venta fv, ibnorca.ventanormas vn
        where sf.codigo=sfd.cod_solicitudfacturacion and sf.cod_estadosolicitudfacturacion<>2 and 
        sfd.codigo=sus.cod_facturadetalle and fv.cod_solicitudfacturacion=sf.codigo and vn.idSolicitudfactura=sf.codigo and vn.idNorma=sus.id_norma 
        AND DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
        order by fv.codigo ASC"; 
// echo $sql;
$stmtSuscripcion = $dbh->prepare($sql);
$stmtSuscripcion->execute();

$resultadosFacturaSuscripcion = array();
while ($row = $stmtSuscripcion->fetch(PDO::FETCH_ASSOC)) {
    $objeto = array(
        "codigo"             => $row['codigo'],
        "nro_factura"        => $row['nro_factura'],
        "cod_facturadetalle" => $row['cod_facturadetalle'],
        "fecha_factura"      => $row['fecha_factura'],
        "razon_social"       => $row['razon_social'],
        "nit"                => $row['nit'],
        "Catalogo"           => $row['Catalogo'],
        "idNorma"            => $row['idNorma'],
        "nombrenorma"        => $row['nombrenorma'],
        "IdVentaNormas"      => $row['IdVentaNormas'],
        "cod_solicitudfacturacion" => $row['cod_solicitudfacturacion']
    );
    $resultadosFacturaSuscripcion[] = $objeto;
}

// FACTURAS VENTAS (vs Nueva tabla ventanormas_facturas)
$sql = "SELECT fv.fecha_factura,fv.codigo,fv.nro_factura,f.codigo as codfacturadetalle, ibnorca.d_abrevclasificador(fvd.cod_Area) as area,
        ibnorca.d_abrevclasificador(fv.cod_unidadorganizacional) as oficina, 
            f.cantidad, f.descripcion_alterna ,(((f.cantidad*f.precio)-f.descuento_bob)*(fvd.porcentaje/100)) as importe_total,
        ((((f.cantidad*f.precio)-f.descuento_bob)*.87)*(fvd.porcentaje/100)) as importe_neto, fvd.porcentaje, 
            fv.cod_solicitudfacturacion, fv.razon_social, fv.nit, f.cod_claservicio, fv.cod_tipoobjeto, fv.created_by, vnf.IdVentaNormas
        from facturas_ventadetalle f 
        left join ibnorca.claservicios c on f.cod_claservicio=c.IdClaServicio
        inner join facturas_venta fv on f.cod_facturaventa=fv.codigo
        inner join facturas_venta_distribucion fvd on fvd.cod_factura=f.cod_facturaventa
        LEFT JOIN ventanormas_facturas vnf ON vnf.cod_facturaventadetalle = f.codigo
        where fvd.cod_area in (12) 
        and fv.cod_estadofactura<>2 ".
        (empty($claServicio) ? '' : "and f.cod_claservicio = $claServicio")
        ." and fv.cod_solicitudfacturacion != '-100'
        AND DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
        order by fv.fecha_factura"; 
// echo $sql;
// exit;

$stmtFactura = $dbh->prepare($sql);
$stmtFactura->execute();

?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .filter-form {
            text-align: center;
            margin-top: 20px;
            font-family: 'Arial', sans-serif; /* Utiliza una fuente legible y formal */
            background-color: #f7f7f7; /* Un color de fondo suave */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-form input[type="date"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #337ab7; /* Un color azul formal */
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #235a9d; /* Cambia el color al pasar el mouse */
        }

        .resaltado-verde {
            background-color: #c7f4c3;
            color: #000;
        }

        .resaltado-rojo {
            background-color: #f4c3c3;
            color: #000;
        }
        .cards {
            display: flex;
            justify-content: center;
            margin: 20px;
        }

        .card {
            margin-right: 10px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .green {
            background-color: #c7f4c3;
        }

        .red {
            background-color: #f4c3c3;
        }
        .blue{
            background-color: #3498db;
            color: #fff;
        }

        .table {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
            border: 1px solid #ccc;
            max-height: 400px;
        }
        tr:hover {
            background-color: #c0c0c0;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            background-color: white; /* Ajusta el color de fondo según tu diseño */
            z-index: 1;
        }


        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <form method="GET">
        <div class="filter-form">
            <label for="fecha_inicio">Fecha Inicio: </label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" placeholder="Fecha de inicio" value="<?=$fecha_inicio?>">
            <label for="fecha_fin">Fecha Fin: </label>
            <input type="date" name="fecha_fin" id="fecha_fin" placeholder="Fecha de fin" value="<?=$fecha_fin?>">
            <label for="fecha_fin">Tipo: </label>
            <select name="mostrar" id="mostrar">
                <option value="1" <?= $mostrar == 1 ? 'selected' : '' ?>>Todos</option>
                <option value="2" <?= $mostrar == 2 ? 'selected' : '' ?>>Encontrado</option>
                <option value="3" <?= $mostrar == 3 ? 'selected' : '' ?>>No encontrado</option>
            </select>
            <label for="fecha_fin">ClaServicio: </label>
            <select name="cla_servicio" id="cla_servicio">
                <option value="" selected>Todos</option>
                <?php
                    // CLA SERVICIO, se lista en base al rango de fechas
                    $sqlServicio = "SELECT DISTINCT(fvd.cod_claservicio) as cla_servicio
                            FROM facturas_venta fv
                            LEFT JOIN facturas_ventadetalle fvd ON fvd.cod_facturaventa=fv.codigo
                            Inner join facturas_venta_distribucion dist on dist.cod_factura=fvd.cod_facturaventa
                            where dist.cod_area in (12) 
                            AND fv.cod_estadofactura<>2
                            AND DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
                            and fv.cod_solicitudfacturacion != '-100'
                            ORDER BY cla_servicio DESC"; 
                    // echo $sqlServicio;
                    $stmtServicio = $dbh->prepare($sqlServicio);
                    $stmtServicio->execute();
                    while($rowServicio = $stmtServicio->fetch(PDO::FETCH_ASSOC)){
                ?>
                    <option value="<?= $rowServicio['cla_servicio']; ?>" <?= $rowServicio['cla_servicio'] == $claServicio ? 'selected' : '' ?>><?= $rowServicio['cla_servicio']; ?></option>
                <?php
                    } 
                ?>
            </select>
            <button type="submit">Filtrar</button>
        </div>
    </form>


    <div class="cards">
        <div class="card blue" style="text-align:center;">
            <h2>Total <br>Registros</h2>
            <p id=""><?= $stmtFactura->rowCount() ?></p>
        </div>
        <div class="card green" style="text-align:center;">
            <h2>Total <br>Encontradas</h2>
            <p id="nro_encontrado">0</p>
        </div>
        <div class="card red" style="text-align:center;">
            <h2>IdVentaNormas no<br>Encontradas</h2>
            <p id="nro_no_encontrado">0</p>
        </div>
        <div class="card blue" style="text-align:center;">
            <h2>Total <br>Importe Neto</h2>
            <p id="importe_neto">0</p>
        </div>
    </div>


    <table class="table">
        <thead class="sticky-header">
            <tr>
                <th>#</th>
                <th>Código FV</th>
                <th>Número de Factura</th>
                <th>Código de FV Detalle</th>
                <th>Tipo Objeto</th>
                <th>Usuario</th>
                <th>NIT</th>
                <th>Razón Social</th>
                <th>ClaServicio</th>
                <th>Fecha de Factura</th>
                <th>Área</th>
                <th>Oficina</th>
                <th>Cantidad</th>
                <th>Descripción Factura</th>
                <th>Importe Total</th>
                <th>Importe Neto</th>
                <th>Porcentaje</th>
                <th>Código de SF</th>
                <th>Verificación<br> Factura / Norma</th>
                <th>ID Venta Norma</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $total_importe_total = 0;
                $total_importe_neto  = 0;

                $nro_encontrado = 0;
                $nro_no_encontrado = 0;
                $nro = 0;
                while ($rowFactura = $stmtFactura->fetch(PDO::FETCH_ASSOC)) {
                    
                    $facturaValida      = (empty($rowFactura['IdVentaNormas']) || $rowFactura['IdVentaNormas'] == 0) ? false : true;
                    $idVentaNorma       = buscarVentaNorma($rowFactura['cod_solicitudfacturacion'], $rowFactura['codfacturadetalle']);
                    $facturaValidaNorma = empty($idVentaNorma) ? false : true;
                    $nro_encontrado     = $facturaValida ? ($nro_encontrado + 1) : $nro_encontrado;
                    $nro_no_encontrado  = !$facturaValida ? ($nro_no_encontrado + 1) : $nro_no_encontrado;
                    if(($mostrar == 1) || ($mostrar == 2 && $facturaValida) || ($mostrar == 3 && !$facturaValida)){
                        $nro++;
            ?>
            <tr class="<?= !$facturaValida ? 'resaltado-rojo' : ''; ?>">
                <td><?=$nro;?></td>
                <td style="background-color: #c7f4f9;color: #000;"><?= $rowFactura['codigo']; ?></td>
                <td><?= $rowFactura['nro_factura']; ?></td>
                <td style="background-color: #c7f4f9;color: #000;"><?= $rowFactura['codfacturadetalle']; ?></td>
                <td><?= $rowFactura['cod_tipoobjeto']; ?></td>
                <td><?= $rowFactura['created_by']; ?></td>
                <td><?= $rowFactura['nit']; ?></td>
                <td><?= $rowFactura['razon_social']; ?></td>
                <td style="background-color: #B0B0B0;color: #000;"><?= $rowFactura['cod_claservicio']; ?></td>
                <td><?= $rowFactura['fecha_factura']; ?></td>
                <td><?= $rowFactura['area']; ?></td>
                <td><?= $rowFactura['oficina']; ?></td>
                <td><?= $rowFactura['cantidad']; ?></td>
                <td><?= $rowFactura['descripcion_alterna']; ?></td>
                <td><?= $rowFactura['importe_total']; ?></td>
                <td><?= $rowFactura['importe_neto']; ?></td>
                <td><?= $rowFactura['porcentaje']; ?></td>
                <td style="background-color: #FFD699;;color: #000;"><?= $rowFactura['cod_solicitudfacturacion']; ?></td>
                <td class="<?= $facturaValidaNorma ? 'resaltado-verde' : 'resaltado-rojo'; ?>"><b><?=$facturaValidaNorma ? 'Encontrado' : 'No Encontrado';?></b></td>
                <?php
                    $verificaSuscripcion = buscarSuscripcion($rowFactura['cod_solicitudfacturacion']);
                    if($facturaValida){
                        $total_importe_total += $rowFactura['importe_total'];
                        $total_importe_neto  += $rowFactura['importe_neto'];
                    }
                ?>
                <!-- Pinta de color por la suscripción -->
                <td class="<?= $verificaSuscripcion ? 'blue' : '' ?>"><?= $rowFactura['IdVentaNormas']; ?></td>
            </tr>
            <?php 
                    }
                }
            ?>
            <!-- Totales -->
            <tr>
                <td colspan="14"></td>
                <td><?= round($total_importe_total, 2); ?></td>
                <td><?= round($total_importe_neto, 2); ?></td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#nro_encontrado').html(<?=$nro_encontrado;?>);
        $('#nro_no_encontrado').html(<?=$nro_no_encontrado;?>);
        $('#importe_neto').html(<?=$total_importe_neto;?>+" Bs.");
    });
</script>

</body>
</html>
