<?php 
@session_start();
if (!class_exists('BD_Menu')){
  require("../../m/menu/menuMdl.php");
}
if (!class_exists('BD_Articulo')){
  require("../../m/articulo/articuloMdl.php");
}


if($_POST) {
	switch ($_GET['opcion']) {
		case 'GetFiltros':
			$Art =  new Cls_Articulo();
			$Art->GetFiltros($_POST);
		break;
	}
}


class Cls_Articulo {
	function GetFiltros($var){
		$DBArticulo = new BD_Articulo();
		$res = $DBArticulo->GetFiltros($var);		
		return $res;
	}
}