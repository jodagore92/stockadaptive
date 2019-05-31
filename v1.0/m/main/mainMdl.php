<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_Main{
    
    // funcion para traer los valores de la tabla CONFIGURACION_APLICACION
    function GetAppKey_Configuration($key){
        $conexion = new Cls_DBManager();
        $sql="SELECT app_valor FROM configuracion_aplicacion WHERE app_clave='$key'";
        //////echo $sql;
        $resultado = $conexion->Ejecutar($sql);
        $resultado = end($resultado);
        return $resultado['app_valor'];
    }
    
    // funcion para traer los valores de la tabla CONFIGURACION_GENERAL
    function GetKey_Configuration($key){
        $conexion = new Cls_DBManager();
        $sql="SELECT conf_valor FROM configuracion_general WHERE conf_clave='$key'";
        switch($key){
            case 'suc_id':
                $sql="SELECT suc_id AS conf_valor
                FROM configuracion_general 
                LEFT JOIN sucursal ON(conf_valor=suc_prefijo) 
                WHERE conf_clave='$key'";
            break;
            case 'bodega':
                $sql="SELECT conf_valor FROM configuracion_general WHERE conf_clave='suc_id'";
            break;
            case 'desc_corta':
                $sql="SELECT suc_desc_corta conf_valor FROM configuracion_general INNER JOIN sucursal ON(conf_valor=suc_prefijo) WHERE conf_clave='suc_id'";
            break; 
        }
        //////echo $sql;
        $resultado = $conexion->Ejecutar($sql);
        $resultado = end($resultado);
        return $resultado['conf_valor'];
    }
    
    // función para verificar que exista la Clave (key) en la tabla CONFIGURACIOn_GENERAL
    function exists_Key($key){
        $conexion = new Cls_DBManager();
		$sql="SELECT conf_clave FROM configuracion_general WHERE conf_clave='$key'";
        $resultado = end($conexion->Ejecutar($sql));
        if($resultado['conf_clave']==""){
            return false;
        }else{
            return true;
        }
        return false;
    }
    
    // función para crear en la tabla CONFIGURACIOn_GENERAL
    function POST($key,$value){
        $conexion = new Cls_DBManager();
		$sql="INSERT INTO configuracion_general (conf_clave,conf_valor) VALUES ('$key','$value'); ";
        $conexion->Ejecutar($sql);
    }
    
    // función para actualizar en la tabla CONFIGURACIOn_GENERAL
    function PUT($key,$value){
        $conexion = new Cls_DBManager();
		$sql="UPDATE configuracion_general SET conf_clave='$key',conf_valor='$value'
            WHERE conf_clave='$key'; ";
        $conexion->Ejecutar($sql);
    }
}
?>