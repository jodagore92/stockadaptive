<?php 
@session_start();
if (!class_exists('BD_Menu')){
  require("../../m/menu/menuMdl.php");
}
if (!class_exists('Cls_Usuario')){
  require("../../m/usuario/usuarioMdl.php");
}


if($_POST) {
	switch ($_GET['opcion']) {
		case 'ListadoPaginas':
			$Menu =  new Cls_Menu();
			$Menu->ListadoPaginas($_POST['usu_id'],$_POST['permiso_pos']);
		break;
		case 'ListadoPaginasDisponibles':
			$Menu =  new Cls_Menu();
			$Menu->ListadoPaginasDisponibles($_POST['usu_id'],$_POST['permiso_pos']);
		break;
		case 'AsignarPaginas':
			$Menu =  new Cls_Menu();
			$Menu->AsignarPaginas($_POST);
		break;
		case 'BloquearPermisos':
			$Menu =  new Cls_Menu();
			$Menu->BloquearPermisos($_POST);
		break;
		case 'ListarMenu':
			$Menu =  new Cls_Menu();
			$Menu->ListarMenu();
		break;
		case 'ListadoUsuariosAsociados':
			$Menu =  new Cls_Menu();
			$Menu->ListarUsuariosAsociados($_POST);
		break;		
		case 'ListadoUsuariosNoAsociados':
			$Menu =  new Cls_Menu();
			$Menu->ListarUsuariosNoAsociados($_POST);
		break;
		case 'ListadoUsuarios':
			$Menu =  new Cls_Menu();
			$Menu->ListadoUsuarios($_POST);
		break;
		case 'ListadoUsuariosDisponibles':
			$Menu =  new Cls_Menu();
			$Menu->ListadoUsuariosDisponibles($_POST);
		break;
		case 'AgregarUsuario':
			$Menu =  new Cls_Menu();
			$Menu->AgregarUsuario($_POST);
		break;
		case 'AgregarPermiso':
			$Menu =  new Cls_Menu();
			$Menu->AgregarPermiso($_POST);
		break;
		case 'EliminarPermiso':
			$Menu =  new Cls_Menu();
			$Menu->EliminarPermiso($_POST);
		break;		
		case 'RetirarUsuario':
			$Menu =  new Cls_Menu();
			$Menu->RetirarUsuario($_POST);
		break;
        case 'Busqueda':
			$Menu =  new Cls_Menu();
			$Menu->Busqueda($_POST['ayuda']);
		break;
	}
}


class Cls_Menu {
	function ValidarPermiso($usuario,$pagina){
		$DBMenu = new BD_Menu();
		$res = $DBMenu->ValidarPermiso($usuario,$pagina);		
		return count($res);
	}
	
	function ListadoPaginas($usu_id,$permiso_pos){
		$DBMenu = new BD_Menu();
		$resultado = $DBMenu->Get_Pagina($usu_id,"","",$permiso_pos);
		$pagina='on';
		require("../../html/Menu/PaginasListaVst.php");
	}
	
	function ListadoPaginasDisponibles($usu_id,$permiso_pos){
		$DBMenu = new BD_Menu();
		$resultado = $DBMenu->Get_Pagina_Disponible($usu_id,"","",$permiso_pos);
		$pagina='off';
		require("../../html/Menu/PaginasListaVst.php");
	}
	
	function ListarMenu(){
		$DBMenu = new BD_Menu();
		$res = $DBMenu->Get_Menu($_SESSION['usu_id'],'');
		require("../../v/menu/menuListaVst.php");
	}
    
    function ListarPaginas($menu){
		$DBMenu = new BD_Menu();
		return $DBMenu->Get_Paginas($_SESSION['usu_id'],$menu);
    }
	
	function AsignarPaginas($var){
		$DBMenu = new BD_Menu();
		$DBMenu->AsignarPaginas($var['paginas'],$var['usu_id']);
	}
	
	function BloquearPermisos($var){
		$DBMenu = new BD_Menu();
		$DBMenu->EliminarPermisos($var['paginas'],$var['usu_id']);
	}

	function Get_Menu($usuario,$menu=''){
		$DBMenu = new BD_Menu();
		return $DBMenu->Get_Menu($usuario,$menu);
	}

	function Get_Pagina($usuario,$menu = "",$pagina = ""){
		$DBMenu = new BD_Menu();
		return $DBMenu->Get_Pagina($usuario,$menu,$pagina);
	}
	
	function GetEstadoPagina($pag_id){
		$DBMenu = new BD_Menu();
		return $DBMenu->GetEstadoPagina($pag_id);
	}

	function ListarUsuariosAsociados($var){
		$bd = new BD_Menu();
		$resultado = $bd->ListarUsuariosAsociados($var);		
		$usuario = 'on';
		require("../../html/Menu/UsuariosDependenciaListaVst.php");
	}

	function ListarUsuariosNoAsociados($var){
		$bd = new BD_Menu();
		$resultado = $bd->ListarUsuariosNoAsociados($var);			
		$usuario = 'off';
		require("../../html/Menu/UsuariosDependenciaListaVst.php");
	}

	function ListadoUsuarios($var){
		$bd  = new Cls_Usuario();
		if($var['opcion']!='limpiar'){
			$validar = $bd->validar($var);
			if(count($validar)>0){
				$var['opcion']='edit';
			}
			$resultado = $bd->ListadoUsuarios($var);
			$permisos  = $bd->GetPermisos();						
			$usuario = 'on';
			$fecha = $var['fch'];
		}
		require("../../html/Inventario/UsuariosHabilitadosListaVst.php");
	}

	function ListadoUsuariosDisponibles($var){
		$bd = new BD_Menu();
		$resultado = $bd->ListadoUsuariosDisponibles($var);		
		$usuario = 'off';
		require("../../html/Inventario/UsuariosHabilitadosListaVst.php");
	}

	function AgregarUsuario($var){
		$bd = new BD_Menu();
		if(isset($var['opcion'])){		
			if($var['opcion']=='bodega'){
				foreach ($var['usuarios'] as $key => $value) {		
					$bd->AdicionarUsuario($value,$var['tipo_doc']);
				}
			}
		}else{
			foreach ($var['usuarios'] as $key => $value) {
				//$usuario  = $bd->GetUsuario($value); 
				$validar  = $bd->VerificarEmpleado($value);
				if($validar['tercero_id']!=''){
				    if(!$empleado = $bd->GetEmpleado($validar['tercero_id'])){
                        echo "El empleado ".$validar['usuario'].", no tiene un Tercero asociado "; exit;
                    }
					$bd->CambioDeDependencia($validar['tercero_id'],$var['dep_id']);
				}else{
                    echo " El usuario ".$validar['usuario'].", no tiene un Tercero asociado ";exit;
                }
                /*else{
					$bd->AgregarEmpleado($usuario,$empleado,$var['dep_id']);
					$bd->AsociarUsuario($value,$empleado);					
				}*/				
			}
		}
	}

	function AgregarPermiso($var){
		$bd = new Cls_Usuario();
		$bd->AgregarPermiso($var);		
	}

	function EliminarPermiso($var){
		$bd = new Cls_Usuario();
		$bd->EliminarPermiso($var);		
	}

	function RetirarUsuario($var){		
		$bd = new BD_Menu();
		if(isset($var['opcion'])){		
			if($var['opcion']=='bodega'){		
				foreach ($var['usuarios'] as $key => $value) {		
					$bd->EliminarUsuario($value);
				}
			}
		}else{		
			foreach ($var['usuarios'] as $key => $value) {
				$usuario = $bd->GetUsuario($value);
				$bd->RetirarDeDependencia($usuario['tercero_id']);				
			}
		}
	}
    
    function Busqueda($ayuda){
        $bd = new BD_Menu();
        $resultado = $bd->Busqueda($ayuda);
        echo json_encode($resultado);
    }
}
?>