<?php 
@session_start();


if($_POST) {
	switch ($_GET['opcion']) {
		case 'Cls_Combo':
			$Cmb =  new Cls_Combo();
			$Cmb->Cls_Combo($_POST);
		break;
	}
}


class Cls_Combo {
    
	function Cls_Combo($var){
        switch ($var['tipo']){
            case 'tipo_caracteristica':
                if (!class_exists('BD_TipoCaracteristica')){
                  require("../../m/articulo/tipoCaracteristicaMdl.php");
                }
                $DB = new BD_TipoCaracteristica();
            break;
            case 'ubicaciones_solicitud':
                if (!class_exists('BD_Reposicion')){
                  require("../../m/inventario/reposicionMdl.php");
                }
                $DB = new BD_Reposicion();
            break;
            case 'ubicaciones_resurtido':
                if (!class_exists('BD_Resurtido')){
                  require("../../m/inventario/resurtidoMdl.php");
                }
                $DB = new BD_Resurtido();
            break;
        }
        $opciones = $DB->ListadoCombo($var);
        require("../../v/template/comboVst.php");
	}
}