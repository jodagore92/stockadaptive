<div class="table">
    <table class="table table-striped table-bordered table-sm table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col" >#</th>
                <th scope="col" >Codigo</th>
                <th scope="col" >Descripcion</th>
                <th scope="col" >PAR</th>
                <th scope="col" >DZ</th>
                <th scope="col" >Buffer</th>
                <th scope="col" >Consumo</th>
                <th scope="col" >ADU</th>
                <th scope="col" >VAR</th>
<?php for($j=0;$j<$columnasProspectiva;$j++){ ?>
                <th><?= $prosp[$j] ?></th>
<?php } ?>
                <th scope="col" >Necesidad</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i=0;
            if(count($resultado)>0){
                $style="";
                $tsi_id = -1;
                foreach($resultado as $row){
                    $style_variacion="";$style_pareto="";
                    if($row['tsi_id']<>$tsi_id){
                        $tsi_id = $row['tsi_id'];
                        $SEM->LlenarSemaforo($tsi_id);
                    }
                    $adu = $row['adu_screen'];
                    $consumo = $row['adu_consumo'];
                    $variacion = $row['variacion'];
                    $dias_zona= $row['dias_zona'];
                    $saldo = $row['sal_saldo'];
                    $buffer = $row['amo_cantidad'];
                    $necesidad = $row['necesidad'];
                    $numero_transito = $row['numero_transito'];
                    $nivel = round($row['nivel'],2);
                    $style="background-color: $row[sem_color_fondo]; color:$row[sem_color_letra];";
                    if($row['adu_color_fondo']!=""){ $style_adu="background-color:$row[adu_color_fondo];"; }
                    if($variacion>=$UA && $variacion<>100){ $style_variacion="background-color:#19C853"; }
                    if($variacion<=$UD && $variacion<>100){ $style_variacion="background-color:#C84B19"; }
                    if($row['pareto']=='P'){ $style_pareto="background-color:#0DC1CF"; }elseif($row['pareto']=='NP'){ $style_pareto="background-color:#F29043"; }else{ $style_pareto=""; }
            ?>
            <tr>
                <th scope="row"><?= ++$i; ?></th>
                <td><?= $row['art_codigo'] ?></td>
                <td><?= $row['art_descripcion'] ?></td>
                <td style="<?= $style_pareto ?>"><?= $row['pareto'] ?></td>
                <td align="right"><?= number_format($dias_zona,0,',','.') ?></td> 
                <td align="right" style="<?= $style ?>"><?= number_format($buffer,2,',','.') ?></td> 
                <td align="right" style="<?= $style_adu ?>"><?= number_format($consumo,2,',','.') ?></td>
                <td align="right" style="<?= $style_adu ?>"><?= number_format($adu,2,',','.') ?></td>  
                <td align="right" style="<?= $style_variacion ?>"><?= number_format($variacion,2,',','.') ?>%</td>
<?php 
        $fechaUltimoInventario = date('Y-m-d');

      for($j=0;$j<$columnasProspectiva;$j++){
        $style_prosp="";
        if($j==0){ $style_prosp = $style; } ?>
<?php
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
    $rowSem = $SEM->GetSemaforoByNivel(($nivel));
    $style_prosp = "background-color: $rowSem[sem_color_fondo]; color:$rowSem[sem_color_letra];";
    
}?>
                <td align="right" style="<?= $style_prosp ?>">
                    <?= number_format($saldo,2,',','.') ?>
                </td>
<?php }
                    
    if($ZONA>$nivel){
        $necesidad = $buffer-$saldo;
    }else{
        $necesidad = 0;
    }
?>
                <td align="right"><?= number_format($necesidad,2,',','.') ?></td>
            </tr>        
    <?php   }
            }else{ ?>
            <tr>
                <td colspan="9" class="center">No existen registros para mostrar</td>
            </tr>  
    <?php   }
            ?>
        </tbody>
    </table>