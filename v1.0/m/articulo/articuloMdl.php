<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_Articulo{
        
    // función para actualizar en la tabla CONFIGURACIOn_GENERAL
    function GetFiltros($var){
        $conexion = new Cls_DBManager();
		$sql=" SELECT * FROM rel_pagina_tipo_caracteristica
            INNER JOIN tipo_caracteristica USING (tcar_id)
            INNER JOIN tipo_control USING (tco_id)
            WHERE pag_id = $var[pag_id] ";
        return $conexion->Ejecutar($sql);
    }
}
?>