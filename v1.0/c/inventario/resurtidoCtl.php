<?php 
@session_start();
if (!class_exists('BD_Resurtido')){
  require("../../m/inventario/resurtidoMdl.php");
}
if (!class_exists('BD_Semaforo')){
  require("../../m/inventario/semaforoMdl.php");
}
if (!class_exists('BD_Main')){
  require("../../m/main/mainMdl.php");
}
if (!class_exists('BD_Saldo')){
  require("../../m/inventario/saldoMdl.php");
}
if(!class_exists('funciones')){
    require("../../c/main/funciones.php");
}

if($_POST) {
	switch ($_GET['opcion']) {
		case 'Resurtido':
			$Ctl =  new Cls_Resurtido();
			$Ctl->Resurtido($_POST);
		break;
	}
}

class Cls_Resurtido {
    
	function Resurtido($var){
        $DB = new BD_Resurtido();
        $SEM = new BD_Semaforo();
        $Conf = new BD_Main();
        $necesidades = array();
        
        $ZONA = round($Conf->GetKey_Configuration('zona_solicitud'));
        $UA = round($Conf->GetKey_Configuration('umbral_aumento'),2);
        $UD = round($Conf->GetKey_Configuration('umbral_disminucion'),2) * -1;
        
        $resultado = $DB->Resurtido($var);
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
            if(strtotime($fechaInicial)==strtotime($fechaCorte)){ $while=false; }
            $fechaInicial = date('Y-m-d',strtotime($fechaInicial.$diasCorte));
            if(strtotime($fechaInicial)>strtotime($fechaCorte) && $while){ $fechaInicial = $fechaCorte; $while=true; }
            if(strtotime($fechaInicial)==strtotime($fechaCorte)){ $while=true; }
        }
        
        $tsi_id = -1;
        
        foreach($resultado as &$row){
            $adu = $row['adu_screen'];
            $sku = $row['art_codigo'];
            $consumo = $row['adu_consumo'];
            $variacion = $row['variacion'];
            $dias_zona= $row['dias_zona'];
            $saldo = $row['sal_saldo'];
            $buffer = $row['amo_cantidad'];
            $necesidad = $row['necesidad'];
            $numero_transito = $row['numero_transito'];
            $nivel = round($row['nivel'],2);
            
            $fechaUltimoInventario = date('Y-m-d');

            for($j=0;$j<$columnasProspectiva;$j++){
                if($j<>0){
                    $diasInventario = ceil($saldo/$adu);
                    $fechaInicio = $prosp[$j-1];
                    $fechaFin = $prosp[$j];
                    $diasPeriodo = (strtotime($prosp[$j])-strtotime($prosp[$j-1]))/60/60/24;
                    $consumoProspectado = 0;
                    if($diasInventario>$diasPeriodo){
                        $consumoProspectado = $diasPeriodo * $adu;
                        $diasInventario -= $diasPeriodo;
                        $fechaUltimoInventario = $fechaFin;
                    }elseif($diasInventario>0){
                        $consumoProspectado = $diasInventario * $adu;
                        $fechaUltimoInventario = date('Y-m-d',strtotime($fechaInicio." + $diasInventario days "));
                        $diasInventario -= $diasInventario;
                    }
                    $saldo -= $consumoProspectado;
                    if($saldo < $adu){ $saldo = 0; }

                    if($numero_transito>0){
                        $ingresos = $DB->GetTransito(array(
                            'art_id'=>$row['art_id'],
                            'ubi_id'=>$row['ubi_id'],
                            'ope_id'=>$row['ope_id'],
                            'fecha_inicial'=>date('Y-m-d',strtotime($prosp[$j-1]." + 1 days ")),
                            'fecha_final'=>date('Y-m-d',strtotime($prosp[$j]." + 1 days "))
                        ));
                        $cantidad_entradas = count($ingresos);
                        if($cantidad_entradas>0){
                            foreach($ingresos as $ing){
                                $pendiente = $ing['cantidad'];
                                $fechaEntrada = $ing['fecha'];
                                $diasInventarioEntrada = round($pendiente / $adu);

                                if($adu>0){
                                    if($fechaEntrada > $fechaUltimoInventario){
                                        $diasInventario += $diasInventarioEntrada;
                                        $saldo += $pendiente;

                                        $diasConsumoEntrada = 1+(strtotime($fechaFin)-strtotime($fechaEntrada))/60/60/24;
                                        if($diasConsumoEntrada>$diasInventario){
                                            $diasConsumoEntrada = $diasInventario;
                                        }
                                        $diasInventario -= $diasConsumoEntrada;
                                        $saldo -= round($diasConsumoEntrada * $adu,2);
                                        if(strtotime($fechaEntrada." + $diasInventarioEntrada days ")>strtotime($fechaFin)){
                                            $fechaUltimoInventario = $fechaFin;
                                        }else{
                                            $fechaUltimoInventario = date('Y-m-d',strtotime($fechaEntrada." + $diasInventarioEntrada days "));
                                        }
                                    }else{
                                        $saldo += $pendiente;
                                        $diasInventario += $diasInventarioEntrada;
                                        if(strtotime($fechaUltimoInventario)<strtotime($fechaFin)){
                                            $diasConsumoEntrada = 1+(strtotime($fechaFin)-strtotime($fechaUltimoInventario))/60/60/24;
                                            if($diasConsumoEntrada>$diasInventario){
                                                $diasConsumoEntrada = $diasInventario;
                                            }
                                            $diasInventario -= $diasConsumoEntrada;
                                            $saldo -= round($diasConsumoEntrada * $adu,2);
                                            if(strtotime($fechaEntrada." + $diasInventarioEntrada days ")>strtotime($fechaFin)){
                                                $fechaUltimoInventario = $fechaFin;
                                            }else{
                                                $fechaUltimoInventario = date('Y-m-d',strtotime($fechaEntrada." + $diasInventarioEntrada days "));
                                            }
                                        }
                                    }
                                }else{
                                    $fechaUltimoInventario = $fechaEntrada;
                                    $saldo = $pendiente;
                                }
                            }
                        }
                    }
                    $nivel = round(($saldo/$buffer)*100,2);
                } // end if $j <>0
                $row[$prosp[$j]]=$saldo;
            } // end For $meses_prospectiva
            if($ZONA>$nivel){
                $necesidad = $buffer-$saldo;
                $row['ultimo_nivel'] = $nivel;
                if($row['tsi_id']<>$tsi_id){
                    $tsi_id = $row['tsi_id'];
                    $SEM->LlenarSemaforo($tsi_id);
                }
                $rowSem = $SEM->GetSemaforoByNivel($nivel);
                $row['ultimo_sem'] = $rowSem['sem_desc_corta'];
            }else{
                $necesidad = 0;
            }
            $row['necesidad_final'] = $necesidad;
            $row['necesidad_jose'] = $necesidad;
            $this->LlenarNecesidades($necesidades,$sku,$necesidad);
        } //End foreach $resultado
        $this->CompletarSaldo($necesidades, $var['ubicacion']);
        $this->EliminarNecesidadesSinExpeditar($necesidades);
        $this->MarcarCeroSinSaldo($necesidades,$resultado);
        $expdd = $this->Expeditar($necesidades,$resultado);
        $func = new funciones();
        echo "Expeditar";
        $func->ImprimirTablaHtml($expdd);
        echo "Necesidades";
        $func->ImprimirTablaHtml($necesidades);
        require("../../v/reposicion/resurtidoDetalleVst.php");
	} // end function
    
    function LlenarNecesidades(&$necesidades,$sku,$cantidad){
        $conteo = count($necesidades);
        if($conteo>0){
            $i=1;
            foreach($necesidades as &$row){
                if($row['sku']==$sku){
                    $row['cantidad']+=$cantidad;
                    break;
                }
                $i++;
            }
            if($conteo<$i){
                array_push($necesidades,array('sku'=>$sku,'cantidad'=>$cantidad));
            }
        }else{
            array_push($necesidades,array('sku'=>$sku,'cantidad'=>$cantidad));
        }
    }
    
    function CompletarSaldo(&$necesidades, $ubicacion){
        $conteo = count($necesidades);
        if($conteo>0){
            $DB_Saldo = new BD_Saldo();
            foreach($necesidades as &$row){
                $rowSaldo = $DB_Saldo->GetSaldo(array('descripcion'=>$row['sku'],'ubi_id'=>$ubicacion));
                $rowSaldo = end($rowSaldo);
                if($rowSaldo['sal_saldo']<>""){
                    $row['saldo'] = $rowSaldo['sal_saldo'];
                }else{
                    $row['saldo'] = 0;
                }
            }
        }
    }
    
    //función para eliminar los SKU que no necesitan expeditar.
    function EliminarNecesidadesSinExpeditar(&$necesidades){
        $conteo = count($necesidades);
        if($conteo>0){
            $i=0;
            foreach($necesidades as &$row){
                if($row['saldo']>=$row['cantidad']){
                    unset($necesidades[$i]);
                }
                $i++;
            }
        }
    }
    
    function MarcarCeroSinSaldo(&$necesidades,&$resultado){
        $conteoNecesidad = count($necesidades);
        $conteoResultado = count($resultado);
        if($conteoNecesidad>0 && $conteoResultado>0){
            $i=0;
            foreach($necesidades as &$nec){
                if($nec['saldo']==0){
                    foreach($resultado as &$row){
                        if($row['art_codigo']==$nec['sku']){
                            $row['necesidad_final']=0;
                        }
                    }
                    unset($necesidades[$i]);
                }
                $i++;
            }
        }
    }
    
    function Expeditar(&$necesidades,&$resultado){
        $arrayExpeditar = array();
        $arrayExpeditar = $this->ArmarArregloParaExpeditar($necesidades,$resultado);
        $this->TotalizarNuevaRealidad($necesidades,$arrayExpeditar);
        $conteo = count($arrayExpeditar);
        if($conteo>0){
            foreach($necesidades as $nec){
                $saldoOrigen = $nec['saldo'];
                $necesidadTotal = $nec['cantidad'];
                foreach($arrayExpeditar as &$exp){
                    if($exp['art_codigo']==$nec['sku']){
                        $exp['nueva_necesidad'] = round(round(($exp['necesidad']/$necesidadTotal),4) * $saldoOrigen ,2);
                    }
                }
            }
            foreach($arrayExpeditar as $expe){
                foreach($resultado as &$row){
                    if(($row['art_codigo']===$expe['art_codigo']) && ($row['ubi_id']==$expe['ubicacion'])){
                        $row['necesidad_final'] = $expe['nueva_necesidad'];
                    }
                }
            }
        }
        return $arrayExpeditar;
    }
    
    function ArmarArregloParaExpeditar($necesidades,$resultado){
        $conteoNecesidad = count($necesidades);
        $conteoResultado = count($resultado);
        $expeditar = array();
        if($conteoNecesidad>0 && $conteoResultado>0){
            foreach($necesidades as $nec){
                foreach($resultado as $row){
                    $niveles_permitidos = array("ROJO","NEGRO");
                    if(($row['art_codigo']==$nec['sku']) && ($row['necesidad_jose']>0) && (in_array($row['ultimo_sem'],$niveles_permitidos))){
                        $push = array('art_codigo'=>$row['art_codigo'],
                                                    'ubicacion'=>$row['ubi_id'],
                                                    'necesidad'=>$row['necesidad_jose'],
                                                    'nivel'=>$row['ultimo_nivel'],
                                                    'sem'=>$row['ultimo_sem']);
                        array_push($expeditar,$push);
                    }
                }
            }
        }
        return $expeditar;
    }
    
    function TotalizarNuevaRealidad(&$necesidades,$expeditar){
        foreach($necesidades as &$nec){
            $nec['cantidad']=0;
        }
        foreach($necesidades as &$nec){
            foreach($expeditar as $exp){
                if($exp['art_codigo']==$nec['sku']){
                    $nec['cantidad'] += $exp['necesidad'];
                }
            }
        }
    }

}