<?php

if ($this->validar_pagina == true) {
    if ($pagina_permitida == 0) {
        header('Location:../dashboard');
    }
}

if (!isset($_SESSION['usu_id'])) {
    header('Location:../../v/login/?pagina=' . curPageURL());
}

if (!class_exists('Cls_Configuracion')) {
    require("../../c/main/configuracionCtl.php");
}
$configuracion = new Cls_Configuracion();

if ($configuracion->GetAppKey_Configuration("off") != 0) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge; charset=<? echo $this->codificacion; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="Keywords" content="<? echo $this->keywords; ?>"> 
        
        <title><?php echo $configuracion->GetAppKey_Configuration("app_short_name") . " - " . $this->titulo; ?></title>
        
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

        <!-- Custom Fonts -->
        <link href="../../r/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        
        <link rel="shortcut icon" href="../../r/img/favicon.png" />
        
        <!-- Load funciones del sistemas -->
        <script type="text/javascript" src="../../r/js/funciones.js?id=<?php echo date('Ymdh') ?>"></script>
        
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
        
        
        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="navbar-header">
                    <a class="navbar-brand" href="../dashboard/">
                        <img src="../../r/img/Stock_Adaptive_LOG_4.png" height="27px"></a>
                </div>

                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <ul class="nav navbar-nav navbar-left navbar-top-links">
                    <li><a href="#"><i class="fa fa-home fa-fw"></i> Dashboard</a></li>
                </ul>

                <ul class="nav navbar-right navbar-top-links">
                    <li class="dropdown navbar-inverse">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell fa-fw"></i> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-comment fa-fw"></i> New Comment
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                        <span class="pull-right text-muted small">12 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> Message Sent
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-tasks fa-fw"></i> New Task
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a class="text-center" href="#">
                                    <strong>See All Alerts</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <?= $_SESSION['usu_nombre'] ?> <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#"><i class="fa fa-user fa-fw"></i> Perfil de usuario</a>
                            </li>
                            <li><a href="#"><i class="fa fa-gear fa-fw"></i> Configuración</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="../login/"><i class="fa fa-sign-out fa-fw"></i> Salir</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li class="sidebar-search">
                                <div class="input-group custom-search-form">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                </span>
                                </div>
                                <!-- /input-group -->
                            </li>
                            <li>
                                <a href="../../v/dashboard/" class="active"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <?php 
                                if (!class_exists('Cls_Menu')) {
                                    require("../../c/menu/menuCtl.php");
                                }
                                $menu = new Cls_Menu();
                                $menu->ListarMenu();
                            ?>
                        </ul>
                    </div>
                </div>
            </nav>

            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header"><?= $this->getTitulo() ?></h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->
