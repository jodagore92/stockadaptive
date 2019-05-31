<?php
if (!class_exists('Cls_DBManager')){
  require("../../c/dbmanager/DBManager.php");
}
/////////////////////////////////
/*Modelos de las tipo documento*/
/////////////////////////////////
class BD_Menu{
	public function ValidarPermiso($usuario,$pagina){
		$conexion = new Cls_DBManager();
		$sql="SELECT pag_id,pag_descripcion,pag_ruta,men_id,pag_id_padre
				FROM permiso
            LEFT JOIN rel_usuario_perfil USING (per_id)
            LEFT JOIN pagina USING (pag_id)
            LEFT JOIN menu USING (men_id)
				WHERE usu_id =$usuario AND pag_id=$pagina";
		return $conexion->Ejecutar($sql); // ejecutan los querys
	} 
	
	public function Listado_Cmb(){
		$conexion = new Cls_DBManager();
		$sql="SELECT mnu_descripcion descripcion, mnu_id id FROM menu ORDER BY mnu_descripcion ";
		return $conexion->Ejecutar($sql); // ejecutan los querys
	}
	
	public function GetEstadoPagina($pag_id){
		if ($pag_id==''){ return 'A'; }
		$conexion = new Cls_DBManager();
		$sql="SELECT pag_estado FROM pagina WHERE pag_id = $pag_id ";
		$return = end($conexion->Ejecutar($sql)); // ejecutan los querys
		return $return['pag_estado'];
	}


	public function Get_Pagina($usuario,$menu = "",$pagina = ""){
		$conexion = new Cls_DBManager();
		$sql="SELECT
                DISTINCT 
                pag_id,
                pag_descripcion,
                pag_ruta,
                men_id,
                pag_id_padre,
                men_descripcion
            FROM
                permiso
            LEFT JOIN rel_usuario_perfil USING (per_id)
            LEFT JOIN pagina USING (pag_id)
            LEFT JOIN menu USING (men_id)
            WHERE
                pag_estado <> 'E'
            AND usu_id = $usuario ";
		if($menu!=""){
			$sql.=" AND men_id=$menu ";
		}
		if($pagina!=""){
			$sql.=" AND pag_id=$pagina ";
		}
		$sql.= "ORDER BY pag_descripcion";
        //echo $sql;
		return $conexion->Ejecutar($sql); // ejecutan los querys
	}
	
	public function Get_Pagina_Disponible($usuario,$menu = "",$pagina = ""){
		$conexion = new Cls_DBManager();
		$sql="SELECT
				  *
			  FROM
				  pagina
                  LEFT JOIN menu USING (mnu_id)
			  WHERE
				pag_id NOT IN(
					  SELECT
						  pag_id
					  FROM
						  rel_usuario_pagina
                          LEFT JOIN pagina USING (pag_id)
					  WHERE pag_estado<>'E' AND
						  usu_id = $usuario
				  )";
		if($menu!=""){
			$sql.=" AND mnu_id=$menu";
		}
		if($pagina!=""){
			$sql.=" AND pag_id=$pagina";
		}
		$sql.=" ORDER BY pag_nombre";
		return $conexion->Ejecutar($sql); // ejecutan los querys
	} 
	
	public function AsignarPaginas($paginas, $usuario){
		$conexion = new Cls_DBManager();
		$sql="insert into rel_usuario_pagina (usu_id, pag_id) values";
		foreach ($paginas as $key => $value){
			//echo $key.' '.$value;
			if ($value!=''){
				$sql.=" ($usuario, $value)";
			}
				$count= $key+1;
			if ($paginas[$count]!=''){
				$sql.=",";
			}
		}
		//exit;
		$conexion->Ejecutar($sql); // ejecutan los querys
	}
	
	public function EliminarPermisos($paginas, $usuario){
		$conexion = new Cls_DBManager();
		$sql="DELETE FROM rel_usuario_pagina WHERE usu_id=$usuario AND pag_id IN (";
		foreach ($paginas as $key => $value){
			//echo $key.' '.$value;
			if ($value!=''){
				$sql.="  $value";
			}
				$count= $key+1;
			if ($paginas[$count]!=''){
				$sql.=",";
			}
		}
		$sql.=");";
		//exit;
		$conexion->Ejecutar($sql); // ejecutan los querys
	}
	
	public function Get_Menu($usuario,$menu=""){
		//echo $menu;
		$conexion = new Cls_DBManager();
		$sql="SELECT
                DISTINCT 
				men_id,
				men_descripcion,
                men_orden,
                men_icono,
                men_ruta
			FROM
				permiso
            LEFT JOIN rel_usuario_perfil USING (per_id)
            LEFT JOIN pagina USING (pag_id)
            LEFT JOIN menu USING (men_id)
			WHERE
				usu_id=$usuario AND pag_estado='A' ";
		if($menu!=""){
			$sql.=" AND men_id=$menu";
		}
		$sql.=" ORDER BY men_orden "; 
		return $conexion->Ejecutar($sql); // ejecutan los querys
	}    
    
    public function Get_Paginas($usuario,$menu){
		//echo $menu;
		$conexion = new Cls_DBManager();
		$sql="SELECT
                DISTINCT 
				pag_id,
				pag_descripcion,
                pag_ruta
			FROM
				permiso
            LEFT JOIN rel_usuario_perfil USING (per_id)
            LEFT JOIN pagina USING (pag_id)
            LEFT JOIN menu USING (men_id)
			WHERE
				usu_id=$usuario AND men_id=$menu AND pag_estado='A'";
		$sql.=" ORDER BY pag_descripcion "; 
		return $conexion->Ejecutar($sql); // ejecutan los querys
	}    

	public function ListarUsuariosAsociados($var){
		$conexion = new Cls_DBManager();
		$sql="SELECT usu_id, (usu_nombre || ' ' || usu_apellido)nombre, usu_usuario 
                FROM usuario 
                LEFT JOIN empleado ON (tercero_id = ter_id)
				LEFT JOIN dependencia using (dep_id)
				where dep_id = ".$var['dep_id'];
		return $conexion->Ejecutar($sql);
	}

	public function ListarUsuariosNoAsociados($var){
		$conexion = new Cls_DBManager();
		$sql="SELECT usu_id, (usu_nombre || ' ' || usu_apellido)nombre, usu_usuario usuario, usu_email 
                FROM usuario
				LEFT JOIN empleado ON (tercero_id = ter_id)
				WHERE (ter_id IS NULL OR dep_id IS NULL) AND activo = TRUE ";
		return $conexion->Ejecutar($sql);		
	}

	public function ListadoUsuariosHabilitados($var){
		$conexion = new Cls_DBManager();
		$sql="SELECT usu_id,(usu_nombre || ' ' || usu_apellido) nombre, usu_usuario FROM fecha_usuario_asignado
				LEFT JOIN usuario USING (usu_id);";
		return $conexion->Ejecutar($sql);		
	}

	public function ListadoUsuariosDisponibles($var){
		$conexion = new Cls_DBManager();
		$sql="SELECT usu_id, (usu_nombre || ' ' || usu_apellido)nombre, usu_usuario usuario FROM usuario 
				LEFT JOIN empleado  ON (tercero_id = ter_id)
				LEFT JOIN dependencia using (dep_id)
				where dep_id = 10 and usu_id NOT IN (SELECT usu_id FROM fecha_usuario_asignado);";
		return $conexion->Ejecutar($sql);		
	}

	public function GetUsuario($usu_id){
		$conexion = new Cls_DBManager();
		$sql="SELECT (usu_usuario || '_' || usu_id) numero, usu_nombre, usu_apellido, tercero_id FROM usuario WHERE usu_id = $usu_id;";
		return end($conexion->Ejecutar($sql));
	}

	public function GetEmpleado($tercero_id){
		$conexion = new Cls_DBManager();
		$sql="SELECT count(ter_id)conteo FROM empleado WHERE ter_id = $tercero_id;";
		$res = end($conexion->Ejecutar($sql));
        if($res['conteo']>0){
            return true;
        }else{
            return false;            
        }
	}

	public function VerificarEmpleado($usuario){
		$conexion = new Cls_DBManager();
		$sql="SELECT tercero_id, usu_nombre||' '||usu_apellido usuario FROM usuario WHERE usu_id = $usuario;";
		return end($conexion->Ejecutar($sql));
	}

	public function AgregarEmpleado($usuario,$empleado,$dep_id){
		$conexion = new Cls_DBManager();
		$sql="INSERT INTO empleado (emp_identificacion,emp_primer_nombre,emp_primer_apellido,emp_id,dep_id,emp_estado) VALUES ('$usuario[numero]', '$usuario[usu_nombre]', '$usuario[usu_apellido]', $empleado[emp_id], $dep_id,'A');";
		$conexion->Ejecutar($sql);
	}

	public function CambioDeDependencia($tercero_id,$dep_id){
		$conexion = new Cls_DBManager();
		$sql="UPDATE empleado SET dep_id = $dep_id WHERE ter_id = $tercero_id;";
		$conexion->Ejecutar($sql);
	}

	public function AsociarUsuario($tercero_id,$empleado){
		$conexion = new Cls_DBManager();
		$sql="UPDATE empleado SET dep_id = $empleado[ter_id] WHERE ter_id = $tercero_id;";		
		$conexion->Ejecutar($sql);	
	}

	public function RetirarDeDependencia($tercero_id){
		$conexion = new Cls_DBManager();
		$sql="UPDATE empleado SET dep_id = NULL WHERE ter_id = $tercero_id;";		
		$conexion->Ejecutar($sql);	
	}

	public function RetirarEmpleado($ter_id){
		$conexion = new Cls_DBManager();
		$sql="DELETE FROM empleado WHERE ter_id = $ter_id;";		
		$conexion->Ejecutar($sql);	
	}

	public function GetTipoDocumento(){
		$conexion = new Cls_DBManager();
		$sql="SELECT tdri_id, upper(tdri_prefijo_tabla) tipo_doc FROM tipo_doc_ref_inventario WHERE tdri_asignacion_doc = 'A';";		
		return $conexion->Ejecutar($sql);		
	}

	public function AdicionarUsuario($usuario,$tipo_doc){
		$conexion = new Cls_DBManager();
		$sql="INSERT INTO fecha_usuario_asignado (usu_id, asig_fch, tdri_id) VALUES ($usuario,LOCALTIMESTAMP(0),$tipo_doc);";
		$conexion->Ejecutar($sql);
	}

	public function AgregarPermiso($var){
		$conexion = new Cls_DBManager();
		$sql="INSERT INTO fecha_usuario_asignado (usu_id, asig_fch, tdri_id) VALUES ($var[usu_id],LOCALTIMESTAMP(0),$var[tipo_doc]);";
		echo $sql;
		$conexion->Ejecutar($sql);
	}

	public function EliminarUsuario($usuario){
		$conexion = new Cls_DBManager();
		$sql="DELETE FROM fecha_usuario_asignado WHERE usu_id = $usuario;";
		$conexion->Ejecutar($sql);
	}

	public function ListadoUsuarios($var){
		$conexion = new Cls_DBManager();
		$sql="SELECT usu_id,(usu_nombre || ' ' || usu_apellido) nombre, usu_usuario FROM usuario
					LEFT JOIN empleado ON (tercero_id = ter_id)
					WHERE dep_id = $var[dep_id];"; 
		return $conexion->Ejecutar($sql);		
	}

	public function ConsultarPermisos($usu_id){
		$conexion = new Cls_DBManager();
		$sql="SELECT tdri_id FROM fecha_usuario_asignado WHERE usu_id = $usu_id";
		return $conexion->Ejecutar($sql);
	}
    
	public function Busqueda($ayuda){
		$conexion = new Cls_DBManager();
		$sql="INSERT INTO log_busqueda_menu (usu_id, lbm_fch_registro, lbm_busqueda)
		SELECT $_SESSION[usu_id], LOCALTIMESTAMP, '$ayuda';";
		$conexion->Ejecutar($sql);
		$sql="  
                SELECT '' as label,'' as id, '' as value, '' as icon
                UNION ALL
                SELECT pag_nombre as label, pag_ruta as id, pag_nombre as value, '../../resources/images/'||pag_imagen as icon
                FROM pagina
                LEFT JOIN rel_usuario_pagina USING (pag_id)
                WHERE pag_keywords ILIKE '%$ayuda%' 
                AND pag_id_padre IS NULL AND NOT pag_pos
                AND usu_id = $_SESSION[usu_id] 
                ";
		return $conexion->Ejecutar($sql);
	}
}
?>