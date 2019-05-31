<div class="table">
    <table class="table table-striped table-bordered table-sm table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col" >#</th>
                <th scope="col" >Distrito</th>
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
                <th scope="col" >NF</th>
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
                    $necesidad = $row['necesidad_jose'];
                    $nivel = round($row['nivel'],2);
                    $style="background-color: $row[sem_color_fondo]; color:$row[sem_color_letra];";
                    if($row['adu_color_fondo']!=""){ $style_adu="background-color:$row[adu_color_fondo];"; }
                    if($variacion>=$UA && $variacion<>100){ $style_variacion="background-color:#19C853"; }
                    if($variacion<=$UD && $variacion<>100){ $style_variacion="background-color:#C84B19"; }
                    if($row['pareto']=='P'){ $style_pareto="background-color:#0DC1CF"; }elseif($row['pareto']=='NP'){ $style_pareto="background-color:#F29043"; }else{ $style_pareto=""; }
            ?>
            <tr>
                <td scope="row"><?= ++$i; ?></td>
                <td><?= $row['ubi_descripcion'] ?></td>
                <td><?= $row['art_codigo'] ?></td>
                <td><?= $row['art_descripcion'] ?></td>
                <td style="<?= $style_pareto ?>"><?= $row['pareto'] ?></td>
                <td align="right"><?= number_format($dias_zona,0,',','.') ?></td> 
                <td align="right" style="<?= $style ?>"><?= number_format($buffer,2,',','.') ?></td> 
                <td align="right" style="<?= $style_adu ?>"><?= number_format($consumo,2,',','.') ?></td>
                <td align="right" style="<?= $style_adu ?>"><?= number_format($adu,2,',','.') ?></td>  
                <td align="right" style="<?= $style_variacion ?>"><?= number_format($variacion,2,',','.') ?>%</td>
<?php 
        
        for($j=0;$j<$columnasProspectiva;$j++){
            $nivel = round(($row[$prosp[$j]]/$buffer)*100,2);
            $rowSem = $SEM->GetSemaforoByNivel(($nivel));
            $style_prosp = "background-color: $rowSem[sem_color_fondo]; color:$rowSem[sem_color_letra];";
?>
                <td align="right" style="<?= $style_prosp ?>">
                    <?= number_format($row[$prosp[$j]],2,',','.') ?>
                </td>
<?php } ?>
                <td align="right"><?= number_format($necesidad,2,',','.') ?></td>
                <td align="right"><?= number_format($row['necesidad_final'],2,',','.') ?></td>
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