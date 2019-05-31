<?php 
if (!class_exists('Cls_Configuracion')){
    require("../../c/main/configuracionCtl.php");
}
$configuracion = new Cls_Configuracion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge; charset=<? echo $this->codificacion; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="Keywords" content="<? echo $this->keywords; ?>"> 
    <title><? echo $configuracion->GetAppKey_Configuration("app_name")." > ".$this->titulo; ?></title> 
    <link rel="shortcut icon" href="../../r/images/favicon.png" />
    
    <!-- Bootstrap Core CSS -->
    <link href="../../r/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../../r/css/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="../../r/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../r/css/startmin.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="../../r/css/morris.css" rel="stylesheet">
    <link href="../../r/css/" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../../r/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    
</head>
<body>
    <!-- jQuery -->
    <script src="../../r/js/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../r/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../../r/js/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../../r/js/startmin.js"></script>

    <!-- Sweet Alert JavaScript -->
    <script src="../../r/js/sweetalert.js"></script>
    
    <!-- Load funciones del sistemas -->
    <script type="text/javascript" src="../../r/js/funciones.js"></script>
<?php if(count($this->scripts)>0){
    foreach($this->scripts as $scr){
        ?>
    <script type="text/javascript" src="../../r/js/<?= $scr ?>.js?id=<?= date('Ymdhisds') ?>"></script>
<?php
    }
} ?>