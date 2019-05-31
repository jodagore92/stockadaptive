$(document).ready(function(){
    
    $("#btn_buscar").click(function(){
        Buscar();
    });
    
});

function Buscar(){
    
    var dataSend = {};
    $('.filtro').each(function(){
        dataSend['tcar_id_'+$(this).attr('tcar_id')] = $(this).val();
    });
    
    $.ajax({
        url:'../../c/inventario/ubicacionCtl.php?opcion=MapaLogistico',
        type:'POST',
        dataType:'html',
        async:true,
        data:dataSend,
        success:function(data){
            $("#div_resultado").html(data);
        }
    });
}