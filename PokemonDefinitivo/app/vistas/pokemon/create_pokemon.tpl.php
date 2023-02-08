<?php
include_once('./app/vistas/inc/header.tpl.php');

?>
<div class="containerCrearPokemon fondo-blur h-100 mt-5">
    <div class="d-flex align-items-center">
        <h1 class="m-auto">Creación de Pokémon</h1>
    </div>
    <div class="containerForm">
        <div class="d-flex align-items-centers">
            <form action="./?controlador=pokemon&metodo=create" method="post" class="m-auto">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input name="nombre" type="text" class="form-control" id="nombre">
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" class="form-control" id="descripcion"></textarea>
                </div>
                <div class="form-group">
                    <label for="url">URL de la imagen</label>
                    <input name="url" type="text" class="form-control" id="url">
                </div>
                <label for="" class="label">Elige Tipo</label>
                <div class="types form-group mt-2">

                    <?php

                    for ($i = 0; $i < count($types); $i++) {

                    ?>

                        <label><input class=" mb-2" name="tipo[<?php echo $types[$i]['id_tipo'] ?>]" type="checkbox" value="<?php echo $types[$i]['id_tipo'] ?>"> <?php echo ucwords($types[$i]['nombre']) ?></label> <br>

                    <?php

                    }

                    ?>

                </div>
                <div class="mt-2 mb-5">
                    <a href="./?controlador=pokemon&metodo=listar" class="btn btn-primary m-auto">Volver a Listar</a>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>

            </form>
        </div>
    </div>
</div>



<?php include_once('./app/vistas/inc/footer.tpl.php'); ?>