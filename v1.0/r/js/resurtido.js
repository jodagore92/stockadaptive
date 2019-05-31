$(document).ready(function(){
    
    $("#btn_buscar").click(function(){
        Buscar();
    });
    
});

function Buscar(){
    
    $("#div_resultado").html("Cargando");
    var dataSend = {};
    $('.filtro_tcar').each(function(){
        dataSend['tcar_id_'+$(this).attr('tcar_id')] = $(this).val();
    });
    $('.filtro').each(function(){
        dataSend[$(this).attr('filtro')] = $(this).val();
    });
    
    $.ajax({
        url:'../../c/inventario/resurtidoCtl.php?opcion=Resurtido',
        type:'POST',
        dataType:'html',
        async:true,
        data:dataSend,
        success:function(data){
            $("#div_resultado").html(data);
        }
    });
}