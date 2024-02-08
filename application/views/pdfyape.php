<!DOCTYPE html>
<html>

<head>
    <style>
        .divimg {
            float: left;
            margin-left: 20px;
        }
        .limpiar{
        clear: both;
        content: "";
        display: block;
        
        }
    </style>
</head>

<body>
<?php if (!empty($datos)) : ?>
    <div class="container">
        <div class="jumbotron">
      
      
            <h1>Transacciones Bancarias(<?= $datos[0]->fechahoy ?>)</h1>
        </div>
                    <?php $i = 0 ?>
                    <?php foreach ($datos as $data) :
                    
                        $path   = base_url() . 'public/images/yape/' . $data->nombre;
                        $type   = pathinfo($path, PATHINFO_EXTENSION);
                        $dataimg   = file_get_contents($path);

                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataimg);
                        
                        $i++;
                    ?>  
                
                        <div class="<?= $i%3 != 0 ? 'divimg':'limpiar' ?>" style="">
                            <img src='<?=$base64?>' width="250px" height="450px" class="attachment-shop_catalog size-shop_catalog">
                         </div>
                   
                    <?php endforeach; ?>

    </div>
                    <?php endif; ?>
</body>

</html>