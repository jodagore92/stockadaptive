<?php

/*CREATE OR REPLACE FUNCTION "public"."round"(float8, int4)
  RETURNS "pg_catalog"."float8" AS $BODY$

       select cast(round(cast($1 as numeric),$2) as double precision);

$BODY$
  LANGUAGE 'sql' IMMUTABLE COST 100
;

ALTER FUNCTION "public"."round"(float8, int4) OWNER TO "postgres";*/

if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_Ubicacion{
        
    function UbicacionesMapaLogistico(){
        $conexion = new Cls_DBManager();
        $sql=" SELECT ubi_id id,ubi_desc_corta desc_corta FROM ubicacion INNER JOIN amortiguador USING (ubi_id) GROUP BY ubi_id,ubi_desc_corta,ubi_codigo ORDER BY ubi_codigo ";
        return $conexion->Ejecutar($sql);
    }
    
    // función para actualizar en la tabla CONFIGURACIOn_GENERAL
    function MapaLogistico($var){
        $conexion = new Cls_DBManager();        
        $ubicaciones = $this->UbicacionesMapaLogistico();

		$sql=" SELECT
                art_id,
                art_codigo,
                art_descripcion ";
        foreach($ubicaciones as $u){
            $sql.=" ,SUM(CASE WHEN ubi_id=$u[id] THEN sal_saldo ELSE 0 END) sal_$u[id]
                ,SUM(CASE WHEN ubi_id=$u[id] THEN amo_cantidad ELSE 0 END) amo_$u[id]
                ,MAX(CASE WHEN ubi_id=$u[id] THEN sem_color_fondo ELSE null END) fondo_$u[id]
                ,MAX(CASE WHEN ubi_id=$u[id] THEN sem_color_letra ELSE null END) letra_$u[id] ";
        }
        $sql.=" FROM
                vw_esquema_logistico AS rel
            INNER JOIN tipo_semaforo_inventario tsi USING (tsi_id)
            INNER JOIN semaforo_inventario sem USING (tsi_id)
            LEFT JOIN articulo USING (art_id)
            LEFT JOIN ubicacion USING (ubi_id)
            WHERE
                nivel BETWEEN sem_porc_ini AND sem_porc_fin ";
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
        $sql.=" 
            GROUP BY art_id,art_codigo,art_descripcion
            ORDER BY art_codigo ";
        //echo $sql; exit;
        return $conexion->Ejecutar($sql);
    }
}
?>