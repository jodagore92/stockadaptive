<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_TipoCaracteristica{
        
    // función para actualizar en la tabla CONFIGURACIOn_GENERAL
    function ListadoCombo($var){
        $conexion = new Cls_DBManager();
		$sql=" SELECT car_id id, car_descripcion descripcion, car_desc_corta desc_corta
            FROM caracteristica
            WHERE car_estado='A' AND tcar_id=$var[tcar_id] ";
        return $conexion->Ejecutar($sql);
    }
}
?>