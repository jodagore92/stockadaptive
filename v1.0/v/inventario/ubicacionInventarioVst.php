<div class="table">
    <table class="table table-striped table-bordered table-sm table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col" rowspan="2">#</th>
                <th scope="col" rowspan="2">Codigo</th>
                <th scope="col" rowspan="2">Descripcion</th>
<?php foreach($ubicaciones as $u){ ?>
                <th scope="col" colspan="2"><?= $u['desc_corta'] ?></th>
<?php }  ?>
            </tr>
            <tr>
<?php foreach($ubicaciones as $u){ ?>
                <th scope="col" >I</th>
                <th scope="col" >A</th>
<?php }  ?> 
            </tr>
        </thead>
        <tbody>
            <?php
            $i=0;
            if(count($resultado)>0){
                foreach($resultado as $row){
            ?>
            <tr>
                <th scope="row"><?= ++$i; ?></th>
                <td><?= $row['art_codigo'] ?></td>
                <td><?= $row['art_descripcion'] ?></td>
<?php foreach($ubicaciones as $u){
                $fondo = $row['fondo_'.$u['id']];
                $letra = $row['letra_'.$u['id']];
                $style="background-color: $fondo; color:$letra; "; 
?>
                <td align="right" style="<?= $style ?>"><?= number_format($row['sal_'.$u['id']],2,',','.') ?></td>
                <td align="right" style="<?= $style ?>"><?= number_format($row['amo_'.$u['id']],2,',','.') ?></td>
<?php }  ?>     
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