<?php ini_set('date.timezone','America/Lima');
    $fecha = date('Y-m-d');
?>
<script>
    <?php echo $grilla; ?>
</script>

<div class="container single_product_container">
    <div class="row head">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h5>LISTA DE IMAGENES DE TRANSFERENCIA DE PAGOS</h5>
        </div>
        <div class="btn-group col-lg-6 col-md-12 col-sm-12 col-xs-12" role="group">
            <button type="button" name="button" id="btn_view" class="btn btn-lg btn-info">VER</button>
        </div>
        <div class="btn-group col-lg-6 col-md-12 col-sm-12 col-xs-12" role="group">
        <button type="button" name="button" id="btn_imprimir" class="btn btn-sm btn-warning">IMPRIMIR</button>
        <input type="date" class="form-control form-control-sm" aria-describedby="button-addon2" name="filtroFecha" id="filtroFechaImg" value="<?= $fecha;?>">
        </div>
        
    </div>
</div>
<br><br>

<div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="data-table-list">
                    <div class="table-responsive">
                        <table id="tablaImgTransf" class="table table-striped">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalimg" class="modal-img">
  <span class="closeimg">&times;</span>
  <img class="modal-content-img" id="img01">
  <div id="caption"></div>
</div>

