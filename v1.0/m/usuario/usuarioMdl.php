<?php

@session_start();
//////////////////////////
/* Modelos de las usuario */
//////////////////////////
if (!class_exists('Cls_DBManager')) {
    require("../../c/dbmanager/DBManager.php");
}

class Cls_Usuario {

    private $usu_id, $usu_usuario, $usu_nombre, $usu_email, $usu_password, $Atr_ArrayUser;

    //retorna el id del usuario
    public function getUsu_id() {
        return $this->usu_id;
    }

    public function getUsu_usuario() {
        return $this->usu_usuario;
    }

    //retorna el nombre del usuario	
    public function getUsu_nombre() {
        return $this->usu_nombre;
    }

    //retorna el email del usuario 
    public function getUsu_email() {
        return $this->usu_email;
    }

    //retorna el email del usuario 
    public function getUsu_password() {
        return $this->usu_password;
    }

    //asigna el id del usuario
    public function setUsu_id($usu_id) {
        $this->usu_id = $usu_id;
    }

    //asigna el id del usuario
    public function setUsu_usuario($usu_usuario) {
        $this->usu_usuario = $usu_usuario;
    }

    //asigna  el nombre del usuario
    public function setUsu_nombre($usu_nombre) {
        $this->usu_nombre = $usu_nombre;
    }

    //asigna  el email del usuario
    public function setUsu_email($usu_email) {
        $this->usu_email = $usu_email;
    }

    //asigna  el email del usuario
    public function setUsu_password($usu_password) {
        $this->usu_password = $usu_password;
    }

    //FUNCIONES
    function ValidarUsuario($usuario, $pass) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT * FROM usuario WHERE (usu_usuario = '" . $usuario . "') AND (usu_password='" . $pass . "') AND usu_estado='A' ";
        //echo $sql; exit; 
        $result = $conexion->Ejecutar($sql);
        $user = NULL;
        $row = end($result);
        $this->setUsu_id($row['usu_id']);
        $this->setUsu_usuario($row['usu_usuario']);
        $this->setUsu_nombre($row['usu_nombre']);
        $this->setUsu_email($row['usu_correo']);
        $this->setUsu_password($row['usu_password']);
        $this->Atr_ArrayUser = $row;
        return $row;
    }

    function Listado($var) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT *,usu_password::TEXT usu_password_text FROM usuario WHERE usu_id IS NOT NULL ";
        if ($var['estado'] != "") {
            $sql .= " AND activo=$var[estado] ";
        }
        if ($var['ayuda'] != "") {
            $sql .= " AND (usu_nombre ILIKE '$var[ayuda]%' OR usu_apellido ILIKE '$var[ayuda]%' OR usu_usuario ILIKE '$var[ayuda]%') ";
        }
        if ($var['usu_id'] != "") {
            $sql .= " AND usu_id = $var[usu_id] ";
        }
        if ($var['usuario'] != "") {
            $sql .= " AND usu_usuario = '$var[usuario]' ";
        }
        if ($var['usuarios_enviar'] != "") {
            $sql .= " AND usu_id IN ($var[usuarios_enviar]) ";
        }
        $sql .= " ORDER BY (usu_nombre||usu_apellido),usu_id ";
        //echo $sql;
        return $conexion->Ejecutar($sql);
    }

    function ClaveNoValida() {
        $conexion = new Cls_DBManager();
        $sql = " SELECT usu_id FROM usuario WHERE usu_password='7c4a8d09ca3762af61e59520943dc26494f8941b' AND usu_id=$_SESSION[usu_id] ";
        $return = end($conexion->Ejecutar($sql));
        if ($return['usu_id'] != '') {
            return true;
        }
        return false;
    }

    function Get_PermisosUsuario() {
        $conexion = new Cls_DBManager();
        $sql = "SELECT usu_permiso_sinet,
					usu_permiso_margen,
					usu_permiso_mayorista,
					usu_permiso_margen_total,
					usu_permiso_editar_articulo,
					usu_permiso_cierre_remision, 
					usu_permiso_inventario,
					usu_permiso_ver_costo,
					usu_permiso_cartera_general,
					usu_permiso_asigna_remision,
					usu_permiso_descuento_autorizado,
					CASE WHEN per.usu_id IS NOT NULL THEN true ELSE false END usu_permiso_atencion_pendiente,
					usu_super 
				FROM usuario usu
				LEFT JOIN usuario_pendiente_pedido per USING(usu_id)
				WHERE usu.usu_id=$_SESSION[usu_id] AND activo;";
        return end($conexion->Ejecutar($sql));
    }

    function GetPermisoDescuento() {
        $conexion = new Cls_DBManager();
        $sql = "SELECT * FROM usuario_descuento_especial WHERE usu_id=$_SESSION[usu_id]";
        return end($conexion->Ejecutar($sql));
    }

    function GetProveedor() {
        $conexion = new Cls_DBManager();
        $sql = " SELECT replace(replace(array_agg(ter_id)::text,'{',''),'}','') ter_id FROM rel_usuario_proveedor WHERE usu_id=$_SESSION[usu_id] ";
        $proveedor = end($conexion->Ejecutar($sql));
        return $proveedor['ter_id'];
    }

    function GetDependenciaLogin() {
        $conexion = new Cls_DBManager();
        $sql = "SELECT dep_id FROM empleado INNER JOIN usuario ON (tercero_id = ter_id OR emp_identificacion = usu_usuario ) WHERE usu_id=$_SESSION[usu_id]";
        $return = end($conexion->Ejecutar($sql));
        return $return['dep_id'];
    }

    function PaginaNuevo($var) {
        $conexion = new Cls_DBManager();
        $sql = " UPDATE rel_usuario_pagina SET rup_nuevo='$var[estado]' WHERE usu_id=$var[usuario] AND pag_id=$var[pagina]; ";
        return end($conexion->Ejecutar($sql));
    }

    function PaginaEditar($var) {
        $conexion = new Cls_DBManager();
        $sql = " UPDATE rel_usuario_pagina SET rup_editar='$var[estado]' WHERE usu_id=$var[usuario] AND pag_id=$var[pagina]; ";
        return end($conexion->Ejecutar($sql));
    }

    function PaginaAsentar($var) {
        $conexion = new Cls_DBManager();
        $sql = " UPDATE rel_usuario_pagina SET rup_asentar='$var[estado]' WHERE usu_id=$var[usuario] AND pag_id=$var[pagina]; ";
        return end($conexion->Ejecutar($sql));
    }

    function PaginaEliminar($var) {
        $conexion = new Cls_DBManager();
        $sql = " UPDATE rel_usuario_pagina SET rup_eliminar='$var[estado]' WHERE usu_id=$var[usuario] AND pag_id=$var[pagina]; ";
        return end($conexion->Ejecutar($sql));
    }

    function Guardar($var) {
        $conexion = new Cls_DBManager();
        if ($var['ter_id'] == '') {
            $var['ter_id'] = 'NULL';
        }
        $sql = "INSERT INTO usuario (usu_usuario,usu_nombre,usu_apellido,usu_email,activo,tercero_id)
            VALUES ('$var[usuario]','$var[nombre]','$var[apellido]','$var[email]',$var[estado]::BOOL,$var[ter_id]); ";
        $conexion->Ejecutar($sql);
        return $conexion->lastID();
    }

    function Actualizar($var) {
        $conexion = new Cls_DBManager();
        if ($var['ter_id'] == '') {
            $var['ter_id'] = 'NULL';
        }
        $sql = "UPDATE usuario SET usu_nombre='$var[nombre]',
                usu_apellido='$var[apellido]',
                usu_email='$var[email]',
                activo=$var[estado]::BOOL,
                tercero_id=$var[ter_id]
            WHERE usu_id=$var[usu_id]; ";
        $conexion->Ejecutar($sql);
    }

    function DefinirPermisos($id, $var) {
        $conexion = new Cls_DBManager();
        $sql = " UPDATE usuario SET usu_permiso_margen=$var[chk_margen_item]::BOOL,
            usu_permiso_mayorista=$var[chk_mayorista]::BOOL,
            usu_super=$var[chk_super]::BOOL,
            usu_permiso_margen_total=$var[chk_margen_total]::BOOL,
            usu_permiso_editar_articulo=$var[chk_editar_articulo]::BOOL,
            usu_permiso_ver_costo=$var[chk_costo]::BOOL,
            usu_permiso_cierre_remision=$var[chk_cierre_remision]::BOOL,
            usu_permiso_convierte_transaccion=$var[chk_convertir_trn]::BOOL,
            usu_permiso_inventario=$var[chk_inventario]::BOOL,
            usu_permiso_cartera_general=$var[chk_cartera]::BOOL,
            usu_permiso_asigna_remision=$var[chk_asigna_remision]::BOOL,
            usu_permiso_pos=$var[chk_pos]::BOOL,
            usu_permiso_descuento_autorizado=$var[chk_dcto_autorizado]::BOOL
            WHERE usu_id=$id;";
        $conexion->Ejecutar($sql);
    }

    function ReiniciarClave($id) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT 'UPDATE usuario SET usu_password='||column_default||' WHERE usu_id=$id; ' dato
            FROM information_schema.columns
            WHERE (table_schema, table_name) = ('public', 'usuario')
            AND column_name='usu_password'
            ORDER BY ordinal_position;";
        $r = end($conexion->Ejecutar($sql));
        $conexion->Ejecutar($r['dato']);
    }

    function isSuper() {
        $conexion = new Cls_DBManager();
        $sql = "SELECT usu_super FROM usuario WHERE usu_id=$_SESSION[usu_id];";
        $return = end($conexion->Ejecutar($sql));
        if ($return['usu_super'] != '') {
            return true;
        }
        return false;
    }

    function Set_UltimoLogueo($usu_id) {
        $conexion = new Cls_DBManager();
        $sql = "UPDATE usuario SET usu_ultimologin = LOCALTIMESTAMP(0) WHERE usu_id=$usu_id;";
        $conexion->Ejecutar($sql);
    }

    function GetVendedorAsociado($usu_id = '') {
        if ($usu_id == '') {
            $usu_id = $_SESSION['usu_id'];
        }
        $conexion = new Cls_DBManager();
        $sql = "SELECT tercero_id FROM usuario WHERE usu_id = $usu_id AND activo ";
        $return = end($conexion->Ejecutar($sql));
        return $return['tercero_id'];
    }

    function Get_Info_Logon() {
        $conexion = new Cls_DBManager();
        $sql = "select (usu_nombre||' '||usu_apellido) usu_nombre, usu_email, suc_descripcion, tercero_id
			from usuario,configuracion_general 
			inner join sucursal on (conf_valor=suc_prefijo) 
			where conf_clave='suc_id' and usu_id=$_SESSION[usu_id];";
        return end($conexion->Ejecutar($sql));
    }

    function Listado_Cmb($param) {
        $conexion = new Cls_DBManager();
        $sql = "select (usu_nombre||' '||usu_apellido||' ('||usu_usuario||')') descripcion, usu_id id 
            from usuario 
			WHERE activo ";
        if ($param != "") {
            $sql .= "	AND tercero_id in (SELECT ter_id FROM empleado where dep_id=$param) ";
        }
        $sql .= " ORDER BY (usu_nombre||' '||usu_apellido||' ('||usu_usuario||')') ";
        return $conexion->Ejecutar($sql);
    }

    function GetNotificaciones($usu_id) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT notificacion.* FROM rel_usuario_notificacion
			LEFT JOIN notificacion USING(not_id)
		 WHERE usu_id=$usu_id;";
        return $conexion->Ejecutar($sql);
    }

    function GetEmail($usu_id) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT usu_email FROM usuario WHERE usu_id=$usu_id;";
        $return = end($conexion->Ejecutar($sql));
        return $return['usu_email'];
    }

    function CambiarPassword($var) {
        $conexion = new Cls_DBManager();
        $sql = "UPDATE usuario SET usu_password='$var[pass]' WHERE usu_id=$var[usuario]";
        return $conexion->Ejecutar($sql);
    }

    function ListarAcceso($id) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT * FROM rel_usuario_pagina WHERE usu_id=$id ORDER BY pag_id;";
        return $conexion->Ejecutar($sql);
    }

    public function ListadoUsuarios($var) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT usu_id, usu_nombre || ' ' || usu_apellido nombre, usu_usuario ";
        $permisos = $this->GetPermisos();
        $count = 0;
        foreach ($permisos as $rows) {
            $count = $count + 1;
            if ($var['opcion'] == 'new') {
                $rows['tdri_id'] = 0;
            }
            $sql .= ",max(CASE WHEN (tdri_id = $rows[tdri_id] ";
            if ($var['opcion'] != 'new') {
                $sql .= " and asig_fch::DATE = '$var[fch]' ";
            }
            $sql .= ") THEN 1 ELSE 0 END) as aux" . $count;
        }
        $sql .= " FROM usuario
				  LEFT JOIN fecha_usuario_asignado fch USING (usu_id)
				  LEFT JOIN empleado ON (tercero_id = ter_id OR emp_identificacion = usu_usuario)
				  LEFT JOIN dependencia USING (dep_id)
                  WHERE dep_id = 10 ";
        $sql .= " GROUP BY usu_id, nombre, usu_usuario; ";
//        if ($_SESSION[usu_id] === 498) {
//            echo $sql;
//            exit;
//        }
        return $conexion->Ejecutar($sql);
    }

    public function GetPermisos() {
        $conexion = new Cls_DBManager();
        $sql = "SELECT UPPER(tdri_prefijo_tabla) des, tdri_id FROM tipo_doc_ref_inventario WHERE tdri_asignacion_doc = 'A' ORDER BY tdri_id ASC";
        return $conexion->Ejecutar($sql);
    }

    public function GetTipoDocumento() {
        $conexion = new Cls_DBManager();
        $sql = "SELECT tdri_id, upper(tdri_prefijo_tabla) tipo_doc FROM tipo_doc_ref_inventario WHERE tdri_asignacion_doc = 'A';";
        return $conexion->Ejecutar($sql);
    }

    public function AgregarPermiso($var) {
        $conexion = new Cls_DBManager();
        $sql = "INSERT INTO fecha_usuario_asignado (asig_fch, usu_id, tdri_id, zona_id) VALUES ('$var[fch]',$var[usu_id],$var[tipo_doc],$var[zona_id])";
        $conexion->Ejecutar($sql);
    }

    public function EliminarPermiso($var) {
        $conexion = new Cls_DBManager();
        $sql = "DELETE FROM fecha_usuario_asignado WHERE asig_fch::DATE = '$var[fch]' AND usu_id = $var[usu_id] AND tdri_id = $var[tipo_doc]";
        $conexion->Ejecutar($sql);
    }

    public function validar($var) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT * FROM fecha_usuario_asignado WHERE asig_fch::DATE = '$var[fch]'";
        return $conexion->Ejecutar($sql);
    }

    public function GetZona($user, $fecha) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT zona_id FROM fecha_usuario_asignado WHERE usu_id = $user AND asig_fch::DATE = '$fecha' LIMIT 1";
        return end($conexion->Ejecutar($sql));
    }

    public function UsuarioSucursal($usuario) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT dep.suc_id
            FROM usuario 
            INNER JOIN empleado ON (tercero_id = ter_id OR emp_identificacion = usu_usuario)
            LEFT JOIN dependencia  dep using(dep_id)
            WHERE usu_id=$usuario limit 1";
        //echo $sql;
        $res = end($conexion->Ejecutar($sql));
        return $res['suc_id'];
    }

    public function GetUsuariosResponsablesPendientes() {
        $conexion = new Cls_DBManager();
        $sql = "SELECT usu_nombre || ' ' || usu_apellido usuario FROM usuario_pendiente_pedido 
					LEFT JOIN usuario USING (usu_id) 
					WHERE usu_id_estado";
        //echo $sql;					
        return $conexion->Ejecutar($sql);
    }

    public function GetPermisoSNC($usu_id, $cnc_id, $permiso) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT
                    CASE WHEN count(*)=0 THEN '0' ELSE '1' END permiso
                FROM
                    permisos_usuario_solicitud_nota_credito
                WHERE
                    usu_id = $usu_id AND cnc_id = $cnc_id";
        if ($permiso == 'asentar') {
            $sql .= " AND pusnc_asentar";
        }
        if ($permiso == 'ejecutar') {
            $sql .= " AND pusnc_ejecutar";
        }
        //echo $sql; exit;
        return end($conexion->Ejecutar($sql));
    }

    public function GetUsuariosCausalesNotaCredito($usu_id) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT
                    replace(replace(replace(replace(array_agg(DISTINCT cnc_id)::text,'{',''),'}',''),'\"',''),',',',') causales
                FROM
                    permisos_usuario_solicitud_nota_credito
                WHERE
                    usu_id = $usu_id";
        //echo $sql; exit;
        return end($conexion->Ejecutar($sql));
    }

    public function Get_log_asignacion_automatica_documentos($fecha) {
        $conexion = new Cls_DBManager();
        $sql = "SELECT
                    usu_usuario usuario,
                    usu_nombre || ' ' || usu_apellido nombre,
                    dependencia.dep_descripcion dependencia,
                    laad_fch_registro fecha,
                    laad_estado estado
                FROM
                    log_asignacion_automatica_documentos
                LEFT JOIN usuario USING (usu_id)
                LEFT JOIN empleado ON  (tercero_id = ter_id)
                LEFT JOIN dependencia USING (dep_id)
                WHERE
                    laad_fch_registro IS NOT NULL ";
        if ($fecha != '') {
            $sql .= " AND laad_fch_registro :: DATE = '$fecha'";
        } else {
            $sql .= " AND laad_fch_registro :: DATE = CURRENT_DATE";
        }
        //echo $sql; exit;
        return $conexion->Ejecutar($sql);
    }

}

?>