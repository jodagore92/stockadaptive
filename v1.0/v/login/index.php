<?php
error_reporting(0);
if (!class_exists('Cls_Pagina')) {
    require("../../c/template/plantillaCtl.php");
}
if (!class_exists('Cls_Configuracion')) {
    require("../../c/main/configuracionCtl.php");
}
$configuracion = new Cls_Configuracion();
$pagina = new Cls_Plantilla();
$pagina->cambiarTitulo("Iniciar sesión");
$pagina->AdicionarScript('login');
$pagina->AdicionarScript('sha1');
$pagina->headBlanco();

if (isset($_GET)) {
    if (isset($_GET['pagina'])) {
        ?>
        <script>
            $(document).ready(function () {
                $('#pagina_redirect').val('<?php echo $_GET['pagina']; ?>');
            });
        </script>
        <?php
    }
}
?>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading" style="text-align:center;">
                        <h3 class="panel-title"><img src="../../r/img/Stock_Adaptive_LOG_1.png" width="70%"></h3>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <div class="form-group">
                                <input placeholder='Usuario' type="text" name="txt_usuario"  class="form-control" id="txt_usuario" />
                            </div>
                            <div class="form-group">
                                <input type="password" placeholder='Contraseña' name="txt_contrasena" class="form-control"id="txt_contrasena"  />
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Recordar contraseña">Remember Me
                                </label>
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <button class="btn btn-lg btn-warning btn-block" id="btn_validar_usuario">Iniciar sesión</button>
                            <div>
                                <span>No tengo cuenta, </span><a href="../login/registro.php" >ir al registro</a>.
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="pagina_redirect">
<?php
//$pagina->footer(); 
@session_start();
@session_destroy();
unset($_SESSION);
?>