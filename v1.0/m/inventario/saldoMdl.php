<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_Saldo{
     
    
    function GetSaldo($var){
        $conexion = new Cls_DBManager();
        $sql=" SELECT
                art_id,
                art_codigo,
                art_descripcion,
                ubi_id,
                ubi_descripcion,
                sal_saldo
            FROM
                vw_saldo
            INNER JOIN articulo USING (art_id)
            INNER JOIN ubicacion USING (ubi_id)
            WHERE
                ubi_id = 2
            AND sal_saldo > 0 ";
        if(count($var)>0){
            $sql.=" AND art_id IN (SELECT art_id FROM articulo
                INNER JOIN rel_articulo_caracteristica USING (art_id)
                INNER JOIN caracteristica USING (car_id) WHERE true ";
            foreach($var as $key => $value){
                $posicion = strpos($key,"tcar_id_");
                if($posicion!==false){
                    $tcar_id = substr($key,$posicion+strlen("tcar_id_"));
                    if($tcar_id>0 && $value!==""){
                        $sql.=" AND (tcar_id=$tcar_id AND car_id=$value) ";
                    }
                }
            }
            $sql.=" ) ";
        }
        if(isset($var['descripcion'])){
            if($var['descripcion']<>""){
                $sql.=" AND (art_codigo ILIKE '%$var[descripcion]%'
                    OR art_descripcion ILIKE '%$var[descripcion]%') ";
            }
        }
        if(isset($var['ubi_id'])){
            if($var['ubi_id']<>""){
                $sql.=" AND ubi_id=$var[ubi_id] ";
            }
        }
        $sql.=" ORDER BY art_codigo ";
        return $conexion->Ejecutar($sql);
    }
}
?>