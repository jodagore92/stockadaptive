<?php 
@session_start();
if (!class_exists('Cls_Usuario')){
	require("../../m/usuario/usuarioMdl.php");
}

if($_POST){
	switch ($_GET['opcion']) {
		case 'Login':
			$usuario = new Cls_Usuario();
			$usuario->ValidarUsuario($_POST['usuario'], $_POST['contrasena']);
			$array = array();
			$array['usuario']="";
			if($usuario->getUsu_id()!=""){
                session_unset();
				session_destroy();
				session_start();
				$_SESSION['nombre_usuario']=$_POST['usuario'];
				$_SESSION['usu_id'] = $usuario->getUsu_id();
				$_SESSION['usu_nombre'] = $usuario->getUsu_nombre();
				$_SESSION['usu_correo'] = $usuario->getUsu_email();
				$_SESSION['UserVarSession777'] ="sTOCkOne";
				$array['usuario'] = $usuario->getUsu_id();
			}
			echo json_encode($array);
		break;
		case 'LoginPos':
			$usuario = new Cls_Usuario();
			$usuario->ValidarUsuario($_POST['usuario'], $_POST['contrasena'],true);
			$array = array();
			$array['usuario']="";
			if($usuario->getUsu_id()!=""){
				$array['usuario'] = $usuario->getUsu_id();
			}
			echo json_encode($array);
		break;
		case 'Permisos':
			$usuario = new UsuarioManager();
			$usuario->GetPermisos(true);
		break;
		case 'CambioPassword':
			$usuario = new UsuarioManager();
			$usuario->CambiarPassword($_POST);
		break;
		case 'PermisosDescuento':
			$usuario = new UsuarioManager();
			$usuario->GetPermisoDescuento();
		break;
		case 'Listado':
			$usuario = new UsuarioManager();
			$usuario->Listado($_POST);
		break;
		case 'GetUsuario':
			$usuario = new UsuarioManager();
			$usuario->GetUsuario($_POST['usu_id']);
		break;
		case 'EnviarUsuarios':
			$usuario = new UsuarioManager();
			$usuario->EnviarUsuarios($_POST);
		break;
		case 'Guardar':
			$usuario = new UsuarioManager();
			$usuario->Guardar($_POST);
		break;
		case 'ReiniciarClave':
			$usuario = new UsuarioManager();
			$usuario->ReiniciarClave($_POST['id']);
		break;
		case 'PaginaNuevo':
			$usuario = new UsuarioManager();
			$usuario->PaginaNuevo($_POST);
		break;
		case 'PaginaAsentar':
			$usuario = new UsuarioManager();
			$usuario->PaginaAsentar($_POST);
		break;
		case 'PaginaEditar':
			$usuario = new UsuarioManager();
			$usuario->PaginaEditar($_POST);
		break;
		case 'PaginaEliminar':
			$usuario = new UsuarioManager();
			$usuario->PaginaEliminar($_POST);
		break;
        case 'AbrirLogAsignacionAutomaticaDocumentos':
            $usuario = new UsuarioManager();
			$usuario->AbrirLogAsignacionAutomaticaDocumentos($_POST);
		break;
        case 'GenerarClave':
            $usuario = new UsuarioManager();
			$usuario->GenerarClave($_POST);
		break;
        case 'AsignarClave':
            $usuario = new UsuarioManager();
			$usuario->AsignarClave($_POST);
		break;

	}
}

class UsuarioManager  {
    
    public function GetProveedor(){
        $usuario = new Cls_Usuario();
		return $usuario->GetProveedor();
    }
    
    public function GenerarClave($var){
        try{
            $json["error"]="";
            $json["key"]=$this->generatePassword($var['len']);
        }catch(Exception $e){
            $json["error"]=$e->GetMessage();
        }
        echo json_encode($json);
    }
    
    public function AsignarClave($var){
        $usuario = new Cls_Usuario();
		return $usuario->CambiarPassword($var);
    }
    
	 public function ValidarUsuario($ParamUsuario, $ParamPass) {
		$usuario = new Cls_Usuario();
		$usuario->ValidarUsuario($ParamUsuario, $ParamPass);
  		
		if($usuario->getUsu_id()!=NULL){
  			//genera clave de seguridad
  			return "";
  		}
  		//echo "00";
    }
    
    function generatePassword($len=8){
        $cadena = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz1234567890";
        $longitudCadena=strlen($cadena);
        $pass = "";
        $longitudPass=$len;

        for($i=1 ; $i<=$longitudPass ; $i++){
            $pos=rand(0,$longitudCadena-1);
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;
    }
	
	public function GetPermisos($is_json=true){
		$usuario = new Cls_Usuario();
		$js = $usuario->Get_PermisosUsuario();
		if ($is_json){
			echo json_encode($js);
		}else {
			return $js;
		}
	}
	
	public function GetPermisoDescuento($is_json=true){
		$usuario = new Cls_Usuario();
		$js = $usuario->GetPermisoDescuento();
		if ($is_json){
			echo json_encode($js);
		}else {
			return $js;
		}
	}
    
    public function Listado($var){
		$usuario = new Cls_Usuario();
		$resultado = $usuario->Listado($var);
		require ('../../html/Usuario/UsuariosListaVst.php'); 
	}
	
	public function GetInfo_Logueado(){
		$usuario = new Cls_Usuario();
		return $usuario->Get_Info_Logon();
	}
	
	public function PaginaNuevo($var){
		$usuario = new Cls_Usuario();
		return $usuario->PaginaNuevo($var);
	}
	
	public function PaginaEditar($var){
		$usuario = new Cls_Usuario();
		return $usuario->PaginaEditar($var);
	}
	
	public function PaginaAsentar($var){
		$usuario = new Cls_Usuario();
		return $usuario->PaginaAsentar($var);
	}
	
	public function PaginaEliminar($var){
		$usuario = new Cls_Usuario();
		return $usuario->PaginaEliminar($var);
	}
    
    public function ReiniciarClave($id){
		$usuario = new Cls_Usuario();
		$usuario->ReiniciarClave($id);
        $correo = end($usuario->Listado(array('usu_id'=>$id)));
        if($correo['usu_email']!=""){
            $asunto = 'Recuperacion de Contraseña - Aurora';
            $cuerpo = 'Se ha reestablecido su contraseña de acuerdo a su peticion.'."\r\n";
            $cuerpo.= 'Nueva contraseña: 123456'."\r\n";
            //$cuerpo.= 'http://181.48.19.42/awataa/html/Solicitud/SolicitudesVst.php';
            $from = 'From: sistemas@papeleriaelcid.com';
            mail($correo['usu_email'],$asunto,$cuerpo,$from);
        }
	}
    
    public function GetUsuario($id){
		$usuario = new Cls_Usuario();
		return $usuario->Listado(array('usu_id'=>$id));
	}
    
    public function Guardar($var){
		$usuario = new Cls_Usuario();
        
		$valid_usuario = end($usuario->Listado(array('usuario'=>$var['usuario'])));
        $valid_id = end($usuario->Listado(array('usu_id'=>$var['usu_id'])));
        
        if(($valid_usuario['usu_id']!='')&&($var['usu_id']=='')){
            $json['error']="El usuario ya existe.";
            echo json_encode($json);
            exit;
        }else if(($valid_id['usu_id']!='')&&($var['usu_id']!='')){
            $usuario->Actualizar($var);
            $usuario->DefinirPermisos($var['usu_id'],$var);
            $json['error']="";
            $json['usu_id'] = $var['usu_id'];
            echo json_encode($json);
            exit;
        }else{
            $json['usu_id'] = $usuario->Guardar($var);
            $usuario->DefinirPermisos($json['usu_id'],$var);
            $json['error']="";
            echo json_encode($json);
            exit;
        }
	}
	
	public function GetVendedorAsociado(){
		$usuario = new Cls_Usuario();
		return $usuario->GetVendedorAsociado();
	}
	
	public function isSuper(){
		$usuario = new Cls_Usuario();
		return $usuario->isSuper();
	}
	
	public function CambiarPassword($var){
		$usuario = new Cls_Usuario();
		//echo $_SESSION['nombre_usuario'];
		$usuario->ValidarUsuario($_SESSION['nombre_usuario'],$var['actual']);
		//print_r($var);
		if($usuario->getUsu_id()!=""){
			if (count($usuario->CambiarPassword($var))!=1){
				echo "Error";
			}
		}
		else{
			echo "La contraseña actual es invalida";
			return false;
		}
		return false;  ///
	}
    
    function Volcar($time=500000000){
		ob_flush();
		flush();
		time_nanosleep(0,$time);
	}
    
    public function EnviarUsuarios($var){
		//incluir clases necesarias
		if(!class_exists('BD_Sucursal')){
			require('../../models/SucursalMdl.php');
		}
		if (!class_exists('Cls_DBManager_Conectividad')){
		  require("../DBmanager/DBManager_Conectividad.php");
		}
		$Sucursal = new BD_Sucursal();
	
		$bodegas = explode(',',$var['bodega']);
		$usuario = new Cls_Usuario();		
		$usuarios = $usuario->Listado($var);
		
		echo '<style>
				span {font-family:Verdana;
				font-size:60%;}
			</style>';
			$this->Volcar();
		foreach($bodegas as $bodega){
			$resSucursal = end($Sucursal->Buscar($bodega));
			echo '<span>'.$resSucursal['suc_descripcion'].'</span><br>';
			$ip = $resSucursal['suc_ip'];
			$usuario = $resSucursal['suc_usuario'];
			$puerto = $resSucursal['suc_puerto'];
			$db_name = $resSucursal['suc_bdname'];
			$contrasena = $resSucursal['suc_contrasena'];
			//echo $puerto;
			echo '<span>Plataforma: </span><span><b>';
			$this->Volcar();
			if($puerto=="3306"){ echo 'Soldi'; }else{ echo 'Aurora'; }
			$this->Volcar();
			echo '</b></span><br>';
			$this->Volcar();
			$isConected=false;
			$this->Volcar();
			try{
				if($ip!=''){
					$this->Volcar();
					$BDManager_Conectivdad = new Cls_DBManager_Conectividad($ip,$usuario,$contrasena,$puerto,$db_name,false);
					$this->Volcar();
					if($BDManager_Conectivdad->isConected()==true){
						$isConected = true;
					}else{
						$isConected = false;
					}
				}else{ echo '<span>No hay informacion de conexion</span><br>';$this->Volcar(); }
			}catch(Exception $e){ $isConected= false; echo '<br><br><span><b>Error: '.$e->GetMessage().'</b></span><br><br>';}
			
			if($isConected){
                try{
				echo '<span>Conectado</span><br>';
				$this->Volcar();
				
				foreach($usuarios as $u){
					$sql_consulta=" SELECT usu_id FROM usuario WHERE usu_usuario = '$u[usu_usuario]'; ";
					$resConsulta = end($BDManager_Conectivdad->Ejecutar($sql_consulta));
					echo '<br><span>'.$u['usu_usuario'].' - </span>';
                    $this->Volcar();
                    
                        if($u['usu_super']!=1){ $u['usu_super']='0'; }
                        if($u['usu_permiso_margen']!=1){ $u['usu_permiso_margen']='0'; }
                        if($u['usu_permiso_mayorista']!=1){ $u['usu_permiso_mayorista']='0'; }
                        if($u['activo']!=1){ $u['activo']='0'; }
                        if($u['usu_permiso_margen_total']!=1){ $u['usu_permiso_margen_total']='0'; }
                        if($u['usu_permiso_editar_articulo']!=1){ $u['usu_permiso_editar_articulo']='0'; }
                        if($u['usu_permiso_ver_costo']!=1){ $u['usu_permiso_ver_costo']='0'; }
                        if($u['usu_permiso_cierre_remision']!=1){ $u['usu_permiso_cierre_remision']='0'; }
                        if($u['usu_permiso_convierte_transaccion']!=1){ $u['usu_permiso_convierte_transaccion']='0'; }
                        if($u['usu_permiso_inventario']!=1){ $u['usu_permiso_inventario']='0'; }
                        if($u['tercero_id']==""){ $u['tercero_id']='NULL'; }
                        if($u['usu_permiso_cartera_general']!=1){ $u['usu_permiso_cartera_general']='0'; }
                        if($u['usu_permiso_asigna_remision']!=1){ $u['usu_permiso_asigna_remision']='0'; }
                        if($u['usu_permiso_pos']!=1){ $u['usu_permiso_pos']='0'; }
                        if($u['usu_permiso_descuento_autorizado']!=1){ $u['usu_permiso_descuento_autorizado']='0'; }
                    
					if($resConsulta['usu_id']!=''){
                        $sql_update=" UPDATE usuario SET usu_permiso_margen=$u[usu_permiso_margen]::BOOL,
                            usu_permiso_mayorista=$u[usu_permiso_mayorista]::BOOL,
                            usu_super=$u[usu_super]::BOOL,
                            usu_permiso_margen_total=$u[usu_permiso_margen_total]::BOOL,
                            usu_permiso_editar_articulo=$u[usu_permiso_editar_articulo]::BOOL,
                            usu_permiso_ver_costo=$u[usu_permiso_ver_costo]::BOOL,
                            usu_permiso_cierre_remision=$u[usu_permiso_cierre_remision]::BOOL,
                            usu_permiso_convierte_transaccion=$u[usu_permiso_convierte_transaccion]::BOOL,
                            usu_permiso_inventario=$u[usu_permiso_inventario]::BOOL,
                            usu_permiso_cartera_general=$u[usu_permiso_cartera_general]::BOOL,
                            usu_permiso_asigna_remision=$u[usu_permiso_asigna_remision]::BOOL,
                            usu_permiso_pos=$u[usu_permiso_pos]::BOOL,
                            usu_permiso_descuento_autorizado=$u[usu_permiso_descuento_autorizado]::BOOL,
                            usu_nombre='$u[usu_nombre]',
                            usu_apellido='$u[usu_apellido]',
                            usu_email='$u[usu_email]',
                            activo=$u[activo]::BOOL,
                            usu_password='7c4a8d09ca3762af61e59520943dc26494f8941b'
                            WHERE usu_id=$resConsulta[usu_id];";
                            $BDManager_Conectivdad->Ejecutar($sql_update);
						echo "<span>Usuario Acutalizado</span><br />";
						$this->Volcar();
					}else{ 
                        
                        $sql_insert=" INSERT INTO usuario (usu_identificacion,usu_nombre,usu_apellido,usu_usuario,
                                    usu_password,usu_email,usu_id,usu_permiso_margen,
                                    usu_permiso_mayorista,activo,usu_super,usu_permiso_margen_total,
                                    usu_permiso_editar_articulo,usu_permiso_ver_costo,usu_permiso_cierre_remision,
                                    usu_permiso_convierte_transaccion,usu_permiso_inventario,
                                    usu_permiso_cartera_general,usu_permiso_asigna_remision,usu_permiso_pos,
                                    usu_permiso_descuento_autorizado)
                            VALUES ('$u[usu_identificacion]','$u[usu_nombre]','$u[usu_apellido]','$u[usu_usuario]',
                                    '$u[usu_password_text]'::bytea,'$u[usu_email]',$u[usu_id],$u[usu_permiso_margen]::BOOL,
                                    $u[usu_permiso_mayorista]::BOOL,$u[activo]::BOOL,$u[usu_super]::BOOL,$u[usu_permiso_margen_total]::BOOL,
                                    $u[usu_permiso_editar_articulo]::BOOL,$u[usu_permiso_ver_costo]::BOOL,$u[usu_permiso_cierre_remision]::BOOL,
                                    $u[usu_permiso_convierte_transaccion]::BOOL,$u[usu_permiso_inventario]::BOOL,
                                    $u[usu_permiso_cartera_general]::BOOL,$u[usu_permiso_asigna_remision]::BOOL,$u[usu_permiso_pos]::BOOL,
                                    $u[usu_permiso_descuento_autorizado]::BOOL);";//echo $sql_insert;exit;
                        $BDManager_Conectivdad->Ejecutar($sql_insert);
                        echo '<span>ID: '.$u['usu_id'].'</span><br />';
						echo '<span>Usuario Insertado</span><br />';
						$this->Volcar();
					}
                        //eliminar primero y luego agregar acceso a paginas.
                        $usuario = new Cls_Usuario();
                        $array_acceso=$usuario->ListarAcceso($u['usu_id']);
                        $sql_delete = " DELETE FROM rel_usuario_pagina WHERE usu_id = (SELECT usu_id FROM usuario WHERE usu_usuario='$u[usu_usuario]') ";
                        $BDManager_Conectivdad->Ejecutar($sql_delete);
                        echo '<span>Acceso eliminado</span><br />';
                        $this->Volcar();
                        foreach($array_acceso as $aa){
                            if($aa['rup_nuevo']!=1){ $aa['rup_nuevo']='0'; }
                            if($aa['rup_editar']!=1){ $aa['rup_editar']='0'; }
                            if($aa['rup_eliminar']!=1){ $aa['rup_eliminar']='0'; }
                            $sql_acceso="INSERT INTO rel_usuario_pagina SELECT usu_id, pag_id,$aa[rup_nuevo]::BOOL,$aa[rup_editar]::BOOL,$aa[rup_eliminar]::BOOL FROM usuario, pagina WHERE pag_id =$aa[pag_id] AND usu_usuario = '$u[usu_usuario]';";
                            $BDManager_Conectivdad->Ejecutar($sql_acceso);
                            echo "<span>Acceso creado $aa[pag_id]</span><br>";
                            $this->Volcar();
                        }echo '<span>Accesos Agregados</span><br />';
						echo '<span>Usuario Acutalizado</span><br />';
						$this->Volcar();
				}
            }catch(Exception $ex){
                echo '<br><span>'.$ex->GetMessage().'</span><br>';
            }
            }else{
				echo '<span><b>No se puede conectar con el servidor remoto, verifique su conexion a internet y/o la de la sucursal a actualizar.</b></span><br>';
			}
			
			echo '<br />';
		} // fin del ciclo
		echo '<span>Fin del proceso</span>';
	}
    
    public function UsuarioSucursal($usuario){
        $usuario = new Cls_Usuario();
		return $usuario->UsuarioSucursal($usuario);
    }
    
    public function AbrirLogAsignacionAutomaticaDocumentos($var){
		$usuario = new Cls_Usuario();
		$resultado = $usuario->Get_log_asignacion_automatica_documentos($var['fecha']);
		require ('../../html/Inventario/LogAsignacionAutoDocumentosVst.php'); 
	}    
}
?>