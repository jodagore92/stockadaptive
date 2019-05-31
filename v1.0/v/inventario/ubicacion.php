<?php
error_reporting(0);
if (!class_exists('Cls_Pagina')) {
    require("../../c/template/plantillaCtl.php");
}
if (!class_exists('Cls_Configuracion')) {
    require("../../c/main/configuracionCtl.php");
}
$configuracion = new Cls_Configuracion();
$pagina = new Cls_Plantilla(1);
$pagina->AdicionarScript('ubicacion');
$pagina->head();
?>
<div class="row">
<?php

    if(!class_exists("Cls_Articulo")){
        require("../../c/articulo/articuloCtl.php");
    }
    $articulo = new Cls_Articulo();
    
    //Mostrar filtros
    $filtros = $articulo->GetFiltros(array('pag_id'=>$pagina->pag_id()));
    if(count($filtros)>0){
        foreach($filtros as $fil){
            if($fil['tco_desc_corta']=="combo"){ ?>
                <div class="form-group col-lg-3">
                    <label><?= $fil['tcar_descripcion'] ?></label>
        <?php   if(!class_exists("Cls_Combo")){
                    require("../../c/template/comboCtl.php");
                }
                new Cls_Combo(array('tipo'=>'tipo_caracteristica',
                                    'tcar_id'=>$fil['tcar_id'],
                                    'seleccione'=>1,
                                    'class'=>'filtro',
                                    'id'=>'cmb_'.strtolower($fil['tcar_desc_corta'])));
        ?>
                </div>
        <?php
            }
        }
    }
?>
    <div class="col-lg-3 form-group">
        <button type="button" id="btn_buscar" class="btn btn-primary">Buscar</button>
    </div>
</div>
<div class="row" id='div_resultado'>
    
</div>
<?php
$pagina->footer(); 
?>