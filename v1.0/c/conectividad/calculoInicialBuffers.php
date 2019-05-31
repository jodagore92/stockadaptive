<?php 
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
ob_implicit_flush(1);
ini_set('memory_limit', '2048M');
ob_start();


if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}

$dias_reposicion=30;

//CONEXION A AURORA
$conexionPSQL = new Cls_DBManager();
$conexionPSQL->Ejecutar("SET client_encoding = 'UTF8';");
$conexionPSQL->Ejecutar("SET NAMES 'UTF8';");
//$sucursal=end($conexionPSQL->Ejecutar("SELECT suc_descripcion descripcion FROM sucursal WHERE suc_id=$suc_id"));

$fch_ini=strtotime('2019-01-15');
$fch_fin=strtotime('2019-03-31');

$seconds_diff = $fch_fin - $fch_ini;

$dias_calculo=$seconds_diff/60/60/24;

$fecha_fin_primer_analisis = strtotime("+$dias_reposicion day",$fch_ini);
$periodos = ($fch_fin - $fecha_fin_primer_analisis)/60/60/24;

$sql="SELECT art_codigo,art_descripcion, ubi_descripcion, COALESCE(GREATEST(COALESCE(v1,0)";
for($i=2;$i<=$periodos;$i++){
    $sql.=" ,COALESCE(v$i,0)";
}
$sql.="),0) buffer_nuevo_$dias_reposicion FROM articulo left join ubicacion on (true) left join (";
$sql.=" SELECT
        sku,distrito";
for($i=1;$i<=$periodos;$i++){
    $sql.=" ,SUM(CASE WHEN (fecha::Date BETWEEN '".date('Y-m-d',$fch_ini)."'::Date+interval '".($i-1)." days' AND ('".date('Y-m-d',$fch_ini)."'::Date+interval '".($i-1)." days')::Date+interval '$dias_reposicion days') THEN cantidad END) v$i";
}
$sql.=" FROM
        migracion.ventas
    WHERE fecha::date BETWEEN '".date('Y-m-d',$fch_ini)."' AND '".date('Y-m-d',$fch_fin)."'
    GROUP BY sku,distrito) as rel on (sku=art_codigo AND distrito=ubi_descripcion)
    where art_codigo = '31010200001'
ORDER BY art_codigo ";
$resultado = $conexionPSQL->Ejecutar($sql);

if(count($resultado)>0){
    echo "<table border='1'>";
    $cabecera = $resultado[0];
    echo "<tr>";
    foreach($cabecera as $key => $value){
        echo "<th>$key</th>";
    }
    echo "</tr>";
    foreach($resultado as $res){
        echo "<tr>";
        foreach($res as $item){
            echo "<td>$item</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

?>