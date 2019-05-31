<select class="form-control <?= $var['class'] ?>" id="<?= $var['id'] ?>" name="<?= $var['id'] ?>" tcar_id="<?= $var['tcar_id'] ?>"
<?php 
if(isset($var['attrs'])){
    if(count($var['attrs'])>0){
        foreach($var['attrs'] as $key => $value){
            echo "$key='$value' ";
        }
    }
} ?>        
>
<?php
    if(count($opciones)>0){
        if($var['seleccione']==1){ echo "<option value=''>Seleccione</option>"; }
        foreach($opciones as $opc){
            echo "<option value='$opc[id]'>$opc[descripcion]</option>";
        }
    }
    ?>
</select>