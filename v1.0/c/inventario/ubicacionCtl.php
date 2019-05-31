<?php 
@session_start();
if (!class_exists('BD_Ubicacion')){
  require("../../m/inventario/ubicacionMdl.php");
}

if($_POST) {
	switch ($_GET['opcion']) {
		case 'MapaLogistico':
			$Ctl =  new Cls_Ubicacion();
			$Ctl->MapaLogistico($_POST);
		break;
	}
}

class Cls_Ubicacion {
    
	function MapaLogistico($var){
        $DB = new BD_Ubicacion();
        $resultado = $DB->MapaLogistico($var);
        $ubicaciones = $DB->UbicacionesMapaLogistico();
        require("../../v/inventario/ubicacionInventarioVst.php");
	}
}