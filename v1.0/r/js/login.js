$(document).ready(function(){
            $("#altter").hide();
	
	$('#txt_usuario').focus().select();
	
	$('#txt_usuario').keyup(function(e){
		if(e.which==13){
			$('#txt_contrasena').focus().select();
		}
	});
	
	$('#btn_validar_usuario').click(function() {
		ValidarUsuario();
	});
	
	$('#txt_contrasena').keypress(function(e) {
		if(e.which == 13) {
			ValidarUsuario();
		}
	});
	
});

function ValidarUsuario(){
	if($('#txt_usuario').val()==""){
            swal("Ingrese un usuario", "", "info").then(function(){
                $('#txt_usuario').focus().select();                
            });
			return;
		}
		
		if($('#txt_contrasena').val()==""){
			swal("Ingrese una contraseña", "", "info").then(function(){
			     $('#txt_contrasena').focus().select();               
            });
			return;
		}
		
		$.ajax({
			type: "POST",
			url : "../../c/usuario/usuarioCtl.php?opcion=Login",
			//url : "UsuarioVst.php",
			async:true,
			dataType: "json",
			data:{
				usuario:$('#txt_usuario').val(),
				contrasena:hex_sha1($('#txt_contrasena').val())
			},
		 	success:function (data){
		 		//alert(data);
		 		//return false;
				if((data.usuario=="")){
					swal("Error", "Combinación de usuario y contraseña incorrectas", "error");
				}
				else{
					if ($('#pagina_redirect').val()!=''){
						window.location=$('#pagina_redirect').val();
					}else {//alert($('#pagina_redirect').val());
						document.location.href="../dashboard/";
					}
				}				 
	  		}
	   });
}
