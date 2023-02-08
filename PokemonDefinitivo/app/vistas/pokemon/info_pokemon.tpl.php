<?php include_once('./app/vistas/inc/header.tpl.php'); ?>
<div class="containerInfoPkm">
    <div class="d-flex align-items-center mt-3 mb-5 h-100">
        <h1 class="m-auto">Detalles Pokemon</h1>
    </div>
    <div class="d-flex align-items-center h-100">
        <div class="card m-auto" style="width: 18rem;">
            <img src="<?php echo $datos['url_imagen']; ?>" class="card-img-top" alt="Pokemon">
            <div class="card-body">
                <h5 class="card-title"><?php echo ucwords($datos['nombre']); ?></h5>
                <span>ID: <?php echo $datos['id_pokemon']; ?> </span>
                <p class="card-text"><?php echo $datos['descripcion']; ?></p>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-center h-100 mt-5">
        <a href="./?controlador=pokemon&metodo=listar" class="btn btn-primary m-2">Volver a Listar</a>
        <a href="./?controlador=pokemon&metodo=remove&id=<?php echo $datos['id_pokemon'] ?>" class="btn btn-primary m-2">Eliminar Pokemon</a>
    </div>
</div>


<?php include_once('./app/vistas/inc/footer.tpl.php'); ?>