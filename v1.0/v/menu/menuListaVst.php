<?php

if(count($res)>0){
    foreach($res as $row){ ?>
        <li>
            <a href="#"><i class="<?= $row['men_icono'] ?>"></i> <?= '&nbsp;'.$row['men_descripcion'] ?><span class="fa arrow"></span></a>
<?php 
        if (!class_exists('Cls_Menu')) {
            require("../../c/menu/menuCtl.php");
        }
        $menu = new Cls_Menu();
        $paginas = $menu->ListarPaginas($row['men_id']);
        if(count($paginas)>0){ ?>
            <ul class="nav nav-second-level">
        <?php
                foreach($paginas as $pag){ ?>
                <li>
                    <a href="../../v<?= $row['men_ruta'].$pag['pag_ruta'] ?>"><?= $pag['pag_descripcion'] ?></a>
                </li>
                <?php }
        ?>
            </ul>
        <?php }else{ echo "jose"; }    
            ?>
            <!-- /.nav-second-level -->
        </li>
    <?php }
}

?>