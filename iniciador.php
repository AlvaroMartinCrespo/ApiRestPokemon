<?php
require_once('./app/config/config.php');
session_start();
spl_autoload_register(function ($nombre_clase) {
  //Buscamos la ruta a la clase dentro del directorio app
  //La convención será buscar el nombre de archivo como el de la clase, pero con snake case en lugar de camel case
  $nombre_clase_snake_case = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $nombre_clase)), '_');
  $directory = new RecursiveDirectoryIterator(RUTA_APP);
  $iterator = new RecursiveIteratorIterator($directory);
  foreach ($iterator as $directory_name) {
    if ($directory_name->getFilename() == $nombre_clase_snake_case . '.php') {
      //Cargamos
      require_once($directory_name->getPathname());
      return;
    }
  }
  //Si llega aquí no se ha podido cargar la clase
  throw new Exception('Clase ' . $nombre_clase . ' no disponible');
});
try {
  new Core();
} catch (Exception $e) {
  header('HTTP/1.0 404 Not found');
  die($e->getMessage());
}
