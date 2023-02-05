<?php include_once('./app/vistas/inc/header.tpl.php'); ?>

<?php

if (isset($_SESSION['mensajeUsuario']) && !empty($_SESSION['mensajeUsuario'])) {
?>
    <div id="alert" class="alert alert-info" role="alert">
        <?php echo $_SESSION['mensajeUsuario']; ?>
    </div>
<?php
}

?>

<div class="jumbotron text-center titulo">
    <img class="pokedexPng w-25 " src="./public/img/bdcd20f5411ee5785889542d303ad4cb.png" alt="pokedex_png">
</div>

<div class="containerCartas">
    <?php foreach ($datos as $pokemon => $datos_pokemon) : ?>

        <div>
            <div class="card">
                <img class="card-img-top" src="<?php echo $datos_pokemon['url_imagen']; ?>" alt="Imagen de la tarjeta" />
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5 class="card-title"><?php echo ucwords($datos_pokemon['nombre']); ?></h5>
                    <p class="card-text"><?php echo ucwords($datos_pokemon['tipo']); ?></p>
                    <a href="./?controlador=pokemon&metodo=ver&id=<?php echo $datos_pokemon['id_pokemon']; ?>" class="btn btn-primary">Detalles</a>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
</div>

</table>

<div class="d-flex align-items-center justify-content-center mb-5">

    <a href="./?controlador=pokemon&metodo=create"><button class="btn btn-primary ml-2 mr-2">Crear Pokemon</button></a>

    <a href="./?controlador=pokemon&metodo=listar"><button class="btn btn-primary ml-2 mr-2">Listar Pokemons desde BBDD</button></a>

    <a href="./?controlador=pokemon&metodo=listar&source=api"><button class="btn btn-primary ml-2 mr-2">Listar Pokemons desde Api</button></a>

    <button class="btn btn-primary ml-2 mr-2 verMas">Ver Más</button>

</div>

<?php include_once('./app/vistas/inc/footer.tpl.php'); ?>