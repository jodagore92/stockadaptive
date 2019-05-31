<?php 

function curPageURL() {
	$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
	$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
	$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
	$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
	return $url;
}


if (!class_exists('Cls_Menu')){
    require("../../c/menu/menuCtl.php");
}
if (!class_exists('Cls_Usuario')){
    require("../../m/usuarioMdl.php");
}
class Cls_Plantilla{ 
	private $pag_id;
    private $titulo; 
    private $keywords; 
    private $codificacion; 
	private $validar_pagina;
    private $restringido; 
	private $scripts;
	private $ayudaFabricante;
	private $ayudaArticulo;
	private $ayudaProductoArmable;
	private $ayudaPuc;
	private $ayudaNiif;
	private $Comentario;
	private $Articulo;
	private $Tercero;
	private $AyudaTercero;
	private $AyudaMarca;
	private $AyudaOrdenesCompra;
	private $AyudaSelCuenta;
	private $AyudaSelCuentaNiif;
	private $AyudaDevoluciones;
	private $VistaPrevia;
	private $AyudaActividadEconomica;
    private $AyudaUsuario;
	public $Nuevo;
	public $Editar;
	public $Eliminar;
    public $Asentar;
	public $Contiene_lista;
	
	public function Cls_Plantilla($pag_id=""){
		$this->pag_id = $pag_id;
        $this->scripts = array();
        if($pag_id!=""){
            $this->SetValidar_Pagina(true);
            
        }
	}
	
	function cambiarTitulo($new){ $this->titulo=$new; } 
	function SetAyudaMarca($new){ $this->AyudaMarca=$new; } 
	function SetValidar_Pagina($new){ $this->validar_pagina=$new; } 
    function cambiarCodificacion($new){ $this->codificacion=$new; }
    function SetAyudaFabricante($new){ $this->ayudaFabricante=$new; }
    function SetAyudaArticulo($new){ $this->ayudaArticulo=$new; }
    function SetAyudaProducto($new){ $this->ayudaProductoArmable=$new; }
	function SetAyudaDocumentos($new){ $this->ayudaDocumentos=$new; }
	function SetAyudaPuc($new){ $this->ayudaPuc=$new; }
	function SetAyudaNiif($new){ $this->ayudaNiif=$new; }
	function SetAyudaTercero($new){ $this->AyudaTercero=$new; }
	function SetAyudaActividadEconomica($new){ $this->AyudaActividadEconomica=$new; }
    function SetComentario($new){ $this->Comentario=$new; }
	function SetArticulo($new){ $this->Articulo=$new; }
	function SetTercero($new){ $this->Tercero=$new; }
	function SetAyudaOrdenesCompra($new){ $this->AyudaOrdenesCompra=$new; }
	function SetAyudaSelCuenta($new){ $this->AyudaSelCuenta=$new; }
	function SetAyudaSelCuentaNiif($new){ $this->AyudaSelCuentaNiif=$new; }
	function SetAyudaDevoluciones($new){ $this->AyudaDevoluciones=$new; }
	function SetVistaPrevia($new){ $this->VistaPrevia=$new; }
	function SetCargarArchivo($new){ $this->CargarArchivo=$new; }
	function SetContiene_Lista($new){ $this->Contiene_Lista=$new; }
    function SetAyudaUsuario($new){ $this->AyudaUsuario=$new; }
    
    //function GetAyudaFabricante(){ return $this->ayudaFabricante; }
    //function GetAyudaArticulo(){ return $this->ayudaArticulo; } 
    
    function restringir(){ $this->restringido=true; }
    function AdicionarScript($new){ array_push($this->scripts,$new);}
	
    function pag_id(){ return $this->pag_id; }
	function getTitulo(){ return $this->titulo; }
	
    function ValidarUsuario(){
        $menu = new Cls_Menu(); 
        $resPag = end($menu->Get_Pagina($_SESSION['usu_id'],"",$this->pag_id));
        $this->Nuevo = $resPag['rup_nuevo'];
        $this->Asentar = $resPag['rup_asentar'];
        $this->Editar = $resPag['rup_editar'];
        $this->Eliminar = $resPag['rup_eliminar'];
    }
    
    function head(){  
        
        if($this->restringido){ 
            if(!isset($_SESSION["UserVarSession777"])){  
                echo "<h1>Área Restringida</h1>"; 
                //desde aqui tambien se puede cargar otra pagina
                $this->footer(); exit;  
            }
            else{
				$menu = new Cls_Menu(); 
				$resPag = $menu->Get_Pagina($_SESSION['usu_id'],"",$this->pag_id);
				$resMenu = $menu->Get_Menu();
            	require_once('../../v/template/PlantillaHeadVst.php');
            }
        }
        else{
			$menu = new Cls_Menu();
			if(isset($_SESSION['usu_id'])){
				$EstadoPagina="A";
				if($this->validar_pagina){
					$pagina_permitida = $menu->ValidarPermiso($_SESSION['usu_id'],$this->pag_id);
				    $EstadoPagina = $menu->GetEstadoPagina($this->pag_id);
				}
				$auxiliar = "";
				
				if($EstadoPagina=='M'){
					require_once('../../v/template/ConstruccionVst.html');
					$this->footer();
					exit;
				}else if ($EstadoPagina=='A'){
                    $Usuario = new Cls_Usuario();
                    if($Usuario->ClaveNoValida()){
                        if($this->pag_id!=43){
                            //header('Location:../../v/login/CambiarContrasenaVst.php');
                        }  
                    }
                    
					$resPag = end($menu->Get_Pagina($_SESSION['usu_id'],"",$this->pag_id));
					if($this->pag_id!='')$this->titulo = $auxiliar.$resPag['pag_descripcion'];
					
					require_once('../../v/template/headerVst.php');
				}else {
					echo "<h1>Área Restringida (Pagina no existe)</h1>"; 
					//desde aqui tambien se puede cargar otra pagina
					$this->footer(); exit;  
				}
			}
			else{
                exit;
				///Anibal, esto es para cuando la session se muere, te logueas de nuevo y te lleva hasta la pagina que intentabas abrir... Veaaa!!
				header('Location:../../v/login/?pagina='.curPageURL());
			}
        }
    } //fin de funcion
	
	
	function headBlanco(){
		require_once('../../v/template/headerBlankVst.php');
	}
    function footer(){ 
 		require_once('../../v/template/footerVst.php');
    } 
} 
?>