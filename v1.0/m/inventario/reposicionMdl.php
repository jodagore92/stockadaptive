<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_Reposicion{
        
    function ListadoCombo($var){
        $conexion = new Cls_DBManager();
        $sql=" SELECT DISTINCT
                ubi_id ID,
                ubi_desc_corta descripcion
            FROM
                configuracion_articulo_operacion
            inner join ubicacion using (ubi_id)
            WHERE true ";
        if($var['ope_id']!=""){
            $sql.=" AND ope_id = $var[ope_id] ";
        }
        $sql.=" ORDER BY 2";
        return $conexion->Ejecutar($sql);
    }
    
    function Solicitud($var){
        $conexion = new Cls_DBManager();
        $sql=" SELECT
            art_id,
            art_codigo,
            art_descripcion,
            ubi_id,
            ope_id,
            amo_cantidad,
            sal_saldo,
            nivel,
            sem_color_fondo,
            sem_color_letra,
            adu,
            CASE WHEN COALESCE(adu,0)=0 THEN round(amo_cantidad/adu_leadtime,2) ELSE adu END adu_screen,
            case when adu is null or adu = 0 then '#D2B4DE' else null end adu_color_fondo,
            adu_leadtime,
            adu_consumo,
            CASE WHEN nivel<=zona.valor THEN amo_cantidad-sal_saldo ELSE 0 END necesidad,
            tsi_id,
            ROUND(CASE WHEN COALESCE(adu_consumo,0)=0 THEN 100 ELSE ((adu_consumo/amo_cantidad)-1)*100 END,2) variacion,
            CASE WHEN sem_id_historico=sem_id THEN COALESCE(flo_dias_zona,0)+1 ELSE 0 END dias_zona,
            COALESCE(numero_transito,0) numero_transito,
            pareto
        FROM
            vw_esquema_logistico
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
        (select conf_valor::float valor from configuracion_general WHERE conf_clave='zona_solicitud') as zona 
        WHERE
            ubi_id = $var[ubicacion]
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
        $sql.=" ";
        return $conexion->Ejecutar($sql);
    }
    
    function GetTransito($var){
        $error = "";
        if(isset($var['art_id'])){
            if($var['art_id']==""){
                $error = "Debe enviar un ARTICULO valido";
            }
        }else{ $error = "Debe enviar un ARTICULO"; }
        
        if(isset($var['ubi_id'])){
            if($var['ubi_id']==""){
                $error = "Debe enviar UBICACION valida";
            }
        }else{ $error = "Debe enviar UBICACION"; }
        
        if(isset($var['ope_id'])){
            if($var['ope_id']==""){
                $error = "Debe enviar una OPERACION valida";
            }
        }else{ $error = "Debe enviar una OPERACION"; }
        
        if(isset($var['fecha_inicial'])){
            if($var['fecha_inicial']==""){
                $error = "Debe enviar una FECHA_INICIAL valida";
            }
        }else{ $error = "Debe enviar una FECHA_INICIAL"; }
        
        if(isset($var['fecha_final'])){
            if($var['fecha_final']==""){
                $error = "Debe enviar una FECHA_FINAL valida";
            }
        }else{ $error = "Debe enviar una FECHA_FINAL"; }
        
        
        $conexion = new Cls_DBManager();
        $sql=" SELECT 
            tra_fch_llegada::DATE fecha,
            SUM(tra_cantidad) cantidad
        FROM transito
        WHERE tra_estado='P'
            AND art_id=$var[art_id]
            AND ubi_id=$var[ubi_id]
            AND ope_id=$var[ope_id]
            AND tra_fch_llegada::DATE>='$var[fecha_inicial]' AND tra_fch_llegada::DATE<'$var[fecha_final]'
        GROUP BY tra_fch_llegada::DATE 
        ORDER BY tra_fch_llegada::DATE ";
        return $conexion->Ejecutar($sql);
    }
}
?>