<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_Resurtido{
        
    function ListadoCombo($var){
        $conexion = new Cls_DBManager();
        $sql=" SELECT DISTINCT
                ubi_id_origen ID,
                ubi_descripcion descripcion
            FROM
                configuracion_logistica
            inner join ubicacion on (ubi_id_origen = ubicacion.ubi_id)
            WHERE
                ope_id = 2 ";
        return $conexion->Ejecutar($sql);
    }
    
    function Resurtido($var){
        $conexion = new Cls_DBManager();
        $sql="SELECT
            art_id,
            art_codigo,
            art_descripcion,
            ubi_id,
            ubi_descripcion,
            ope_id,
            amo_cantidad,
            sal_saldo,
            nivel,
            sem_color_fondo,
            sem_color_letra,
            sem_desc_corta,
            COALESCE(adu,0) adu,
            CASE WHEN COALESCE(adu,0)=0 THEN round(COALESCE(amo_cantidad,0)/COALESCE(adu_leadtime,1),2) ELSE adu END adu_screen,
            case when adu is null or adu = 0 then '#D2B4DE' else null end adu_color_fondo,
            adu_leadtime,
            adu_consumo,
            CASE WHEN nivel<=zona.valor THEN amo_cantidad-sal_saldo ELSE 0 END necesidad,
            tsi_id,
            ROUND(CASE WHEN COALESCE(adu_consumo,0)=0 THEN 100 ELSE ((adu_consumo/amo_cantidad)-1)*100 END,2) variacion,
            CASE WHEN sem_id_historico=sem_id THEN COALESCE(flo_dias_zona,0)+1 ELSE 0 END dias_zona,
            COALESCE(numero_transito,0) numero_transito,
            pareto,
            0 necesidad_final,
            0 ultimo_nivel,
            null ultimo_sem
        FROM
            configuracion_logistica cfg
        INNER JOIN vw_esquema_logistico USING (art_id, ope_id, ubi_id)
        INNER JOIN ubicacion USING (ubi_id)
        LEFT JOIN (SELECT art_id,ubi_id,ope_id,flo_dias_zona, sem_id sem_id_historico FROM historico.foto_logistica WHERE flo_fch_registro::DATE = CURRENT_DATE-1) AS foto USING (art_id,ubi_id,ope_id)
                left join articulo using (art_id)
                left join adu using (art_id,ubi_id,ope_id)
                left join tipo_semaforo_inventario using (tsi_id)
                left join semaforo_inventario using (tsi_id)
                LEFT JOIN (SELECT art_id,ubi_id,ope_id,count(*) numero_transito
                    FROM transito
                    WHERE tra_estado = 'P'
                    GROUP BY art_id,ubi_id,ope_id) AS tra USING (art_id,ubi_id,ope_id)
                LEFT JOIN vw_pareto USING (art_id,ubi_id,ope_id),
                (select conf_valor::float valor from configuracion_general WHERE conf_clave='zona_resurtido') as zona 
        WHERE cfg.art_id = cfg.art_id_origen
        and nivel BETWEEN sem_porc_ini and sem_porc_fin ";
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
        $sql.=" ORDER BY ubicacion.ubi_codigo ";
        return $conexion->Ejecutar($sql);
    }
}
?>