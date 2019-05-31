<?php 
if (!class_exists('DB_Main')){
  require("../../m/main/mainMdl.php");
}
date_default_timezone_set('America/Bogota');

if($_POST){
    switch ($_GET['opcion']) {
		case 'ActualizarValor':
			$Ctl_Configuracion =  new Cls_Configuracion();
			$resultado = $Ctl_Configuracion->SetKeyConfiguration($_POST['key'],$_POST['value']);
		break;
    }
}

class Cls_Configuracion{
 
	function GetKey_Configuration($key){
		$Main = new BD_Main();
		return $Main->GetKey_Configuration($key);
	}
 
	function GetAppKey_Configuration($key){
		$Main = new BD_Main();
		return $Main->GetAppKey_Configuration($key);
	}
    
    function dateDiff($primera, $segunda){
        $valoresPrimera = explode ("-", $primera);   
        $valoresSegunda = explode ("-", $segunda); 
        $diaPrimera    = $valoresPrimera[2];  
        $mesPrimera  = $valoresPrimera[1];  
        $anyoPrimera   = $valoresPrimera[0]; 
        $diaSegunda   = $valoresSegunda[2];  
        $mesSegunda = $valoresSegunda[1];  
        $anyoSegunda  = $valoresSegunda[0];
        $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);  
        $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);     
        if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){
            $return['dif']=0;
            $return['error']='First date wrong!';
        }elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
            $return['dif']=0;
            $return['error']='Last date wrong!';
        }else{
            $return['dif']=$diasPrimeraJuliano-$diasSegundaJuliano;
            $return['error']='';
        }
        return $return;
    }
    
    // función para crear/actualizar los valores de la tabla CONFIGURACION_GENERAL
    function SetKeyConfiguration($key,$value){
        try{
            $json['error']="";
            // validar que no venga en blanco
            if(($key=="") || ($value=="")){ throw new Exception("Verifique la información a registrar."); }

            $Main = new BD_Main();
            if(!$Main->exists_Key($key)){
                $Main->POST($key,$value);
            }else{
                $Main->PUT($key,$value);
            }
        }catch (Exception $e){
            $json['error']=$e->GetMessage();
        }
        echo json_encode($json);
    }
} 
?>