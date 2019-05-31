<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
///////////////////////////////// 
class BD_Semaforo{
    
    private $Semaforo;
    
    function LlenarSemaforo($tsi_id){
        $conexion = new Cls_DBManager();
        $sql=" SELECT * FROM
            semaforo_inventario
            INNER JOIN tipo_semaforo_inventario USING(tsi_id)
            WHERE tsi_id=$tsi_id ";
        $resultado = $conexion->Ejecutar($sql);
        $this->Semaforo = $resultado;
    }
        
    function GetSemaforoByNivel($nivel){
        foreach($this->Semaforo as $row){
            $inicial = $row['sem_porc_ini'];
            $final = $row['sem_porc_fin'];
            if($nivel>=$inicial && $nivel<=$final){
                return $row;
            }
        }
        return null;
    }
    
}
?>