<?php
error_reporting(0);
if (!class_exists('Cls_Pagina')) {
    require("../../c/template/plantillaCtl.php");
}
if (!class_exists('Cls_Configuracion')) {
    require("../../c/main/configuracionCtl.php");
}
$configuracion = new Cls_Configuracion();
$pagina = new Cls_Plantilla(3);
$pagina->AdicionarScript('resurtido');
$pagina->head();
$fechaControl = date('Y-m-d',strtotime(date('Y-m-d').' + 2 days '));
?>
<div class="row">
    <div class="form-group col-lg-2">
        <label>Ubicación</label>
    <?php   if(!class_exists("Cls_Combo")){
        require("../../c/template/comboCtl.php");
    }
    new Cls_Combo(array('tipo'=>'ubicaciones_resurtido',
                        'seleccione'=>1,
                        'class'=>'filtro',
                        'id'=>'cmb_ubicacion',
                        'attrs'=>array("filtro"=>"ubicacion")));
    ?>
    </div>
    <div class="col-lg-1 form-group">
        <label>Corte</label>
        <input type="text" class="form-control filtro" value="1" filtro="tiempo_corte" id="txt_tiempo_corte" name="txt_tiempo_corte" />
    </div>
    <div class="col-lg-2 form-group">
        <label>Intervalo</label>
        <select class="form-control filtro" filtro="intervalo_corte" id="cmb_corte_prospectiva" name="cmb_corte_prospectiva">
            <option value="days">Dias</option>
            <option value="week">Semanas</option>
            <option value="month">Meses</option>
            <option value="year">Años</option>
        </select>
    </div>
    <div class="col-lg-2 form-group">
        <label>Fecha de corte</label>
        <input type="date" class="form-control filtro" value="<?= $fechaControl ?>" filtro="fecha_corte" id="txt_fecha_corte" name="txt_fecha_corte" />
    </div>
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
                <div class="form-group col-lg-2">
                    <label><?= $fil['tcar_descripcion'] ?></label>
        <?php   if(!class_exists("Cls_Combo")){
                    require("../../c/template/comboCtl.php");
                }
                new Cls_Combo(array('tipo'=>'tipo_caracteristica',
                                    'tcar_id'=>$fil['tcar_id'],
                                    'seleccione'=>1,
                                    'class'=>'filtro_tcar',
                                    'id'=>'cmb_'.strtolower($fil['tcar_desc_corta'])));
        ?>
                </div>
        <?php
            }
        }
    }
?>
    <div class="col-lg-2 form-group">
        <label>Codigo/Descripcion</label>
        <input type="text" class="form-control filtro" filtro="descripcion" id="txt_descripcion" name="txt_descripcion" />
    </div>
    <div class="col-lg-1 form-group">
        <button type="button" id="btn_buscar" class="btn btn-primary">Buscar</button>
    </div>
</div>
<div class="row" id='div_resultado'>
    
</div>
<?php
$pagina->footer(); 
?>