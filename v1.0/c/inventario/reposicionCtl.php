<?php 
@session_start();
if (!class_exists('BD_Reposicion')){
  require("../../m/inventario/reposicionMdl.php");
}
if (!class_exists('BD_Semaforo')){
  require("../../m/inventario/semaforoMdl.php");
}
if (!class_exists('BD_Main')){
  require("../../m/main/mainMdl.php");
}

if($_POST) {
	switch ($_GET['opcion']) {
		case 'Solicitud':
			$Ctl =  new Cls_Reposicion();
			$Ctl->Solicitud($_POST);
		break;
	}
}

class Cls_Reposicion {
    
	function Solicitud($var){
        $DB = new BD_Reposicion();
        $SEM = new BD_Semaforo();
        $Conf = new BD_Main();
        
        $ZONA = round($Conf->GetKey_Configuration('zona_solicitud'));
        $UA = round($Conf->GetKey_Configuration('umbral_aumento'),2);
        $UD = round($Conf->GetKey_Configuration('umbral_disminucion'),2) * -1;
        
        $resultado = $DB->Solicitud($var);
        if($var['fecha_corte']==""){
            echo "Ingrese una fecha de corte.";
            exit;
        }
        $fechaCorte = date('Y-m-d',strtotime($var['fecha_corte']));
        $fechaInicial = date('Y-m-d');
        if(strtotime($fechaInicial)>strtotime($fechaCorte)){
            echo "La fecha de corte, debe ser superior a la fecha actual.";
            exit;
        }
        $diasCorte = " + ".$var['tiempo_corte']." ".$var['intervalo_corte']." ";
        
        $columnasProspectiva=0;
        $prosp = array();
        $while=true;
        while($while){
            $columnasProspectiva++;
            $prosp[$columnasProspectiva-1] = $fechaInicial;
            
            if(strtotime($fechaInicial)==strtotime($fechaCorte)){
                $while=false;
            }
            
            $fechaInicial = date('Y-m-d',strtotime($fechaInicial.$diasCorte));
            if(strtotime($fechaInicial)>strtotime($fechaCorte) && $while){
                $fechaInicial = $fechaCorte;
                $while=true;
            }
            if(strtotime($fechaInicial)==strtotime($fechaCorte)){
                $while=true;
            }
        }
        
        
        require("../../v/reposicion/solicitudDetalleVst.php");
	}
}