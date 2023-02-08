<?php

class ControladorPokemon
{

  private $mensajeUsuario;

  /**
   * Constructor que comprueba si existe la session del mensaje de usuario y sino existe la deja en blanco.
   */
  public function __construct()
  {
    if (isset($_SESSION['mensajeUsuario'])) {
      $this->mensajeUsuario = $_SESSION['mensajeUsuario'];
    } else {
      $this->mensajeUsuario = '';
    }
  }

  /**
   * Función listar a la que se le pasa los parámetros del get de la url, y si existe source en el url y el igual a API, significa que queremos listar los pokemos de la API.
   */
  public function listar($params)
  {
    $switch = false;
    if (isset($params['source']) && $params['source'] === 'api') {
      $this->listarApi();
      $switch = true;
    } else {
      $this->listarBd();
    }
  }

  /**
   * Metodo privado que listar los pokemos de la APi, llamando al modelo pokemon.
   */
  private function listarApi()
  {

    $modelo_pokemon = new ModeloPokemon();
    $datos = $modelo_pokemon->getAllPokemonsFromApi();

    if (is_file("./app/vistas/pokemon/listado_pokemons.tpl.php")) {
      require_once('./app/vistas/pokemon/listado_pokemons.tpl.php');
      $_SESSION['mensajeUsuario'] = '';
    } else {
      throw new Exception('Vista no disponible');
    }
  }

  /**
   * Metodo privado que listar los pokemos de la BBDD, llamando al modelo pokemon.
   */
  private function listarBd()
  {
    $modelo_pokemon = new ModeloPokemon();
    $datos = $modelo_pokemon->getAllPokemons();
    $mensajeUsuario = $this->mensajeUsuario;

    if (is_file("./app/vistas/pokemon/listado_pokemons.tpl.php")) {
      require_once('./app/vistas/pokemon/listado_pokemons.tpl.php');
      $_SESSION['mensajeUsuario'] = '';
    } else {
      throw new Exception('Vista no disponible');
    }
  }

  /**
   * Se le pasan lo parámetros de la URL, y permite ver un solo pokemon ya sea mediante la API o mediante la BBDD.
   */
  public function ver($params)
  {

    if (isset($params['source']) && isset($params['idApi']) && !empty($params['source'] && !empty($params['idApi']))) {
      $this->verApi($params);
    } else {
      $this->verBd($params);
    }
  }

  /**
   * Función privada que permite ver los pokemons de la BBDD.
   */
  private function verBd($params)
  {

    $id = $params['id'];
    $mensajeUsuario = $this->mensajeUsuario;
    //Tenemos que asegurar que $id es un entero. En cualquier otro caso podría haber problemas de seguridad
    if (ctype_digit($id)) {
      $modelo_pokemon = new ModeloPokemon();
      $datos = $modelo_pokemon->getPokemon($id);


      if (is_file("./app/vistas/pokemon/info_pokemon.tpl.php")) {
        require_once('./app/vistas/pokemon/info_pokemon.tpl.php');
      } else {
        throw new Exception('Vista no disponible');
      }
    } else {
      throw new Exception('El parámetro no es adecuado');
    }
  }

  /**
   * Función privada que permite ver a un solo pokemon de la api.
   */
  private function verApi($params)
  {
    $id = $params['idApi'];
    $mensajeUsuario = $this->mensajeUsuario;
    if (ctype_digit($id)) {
      $modelo_pokemon = new ModeloPokemon();
      $datos = $modelo_pokemon->getPokemonFromApi($id);

      // print_r($datos);

      if (is_file("./app/vistas/pokemon/info_pokemon.tpl.php")) {
        require_once('./app/vistas/pokemon/info_pokemon.tpl.php');
      } else {
        throw new Exception('Vista no disponible');
      }
    } else {
      throw new Exception('El parámetro no es adecuado');
    }
  }

  /**
   * Se le pasan por parámetros si queremos crear un pokemon desde la api o no.
   */
  public function create($params)
  {
    if (isset($params['source']) && $params['source'] === 'Api') {
      $this->createFromApi($params);
    } else {
      $this->createFromUser();
    }
  }

  /**
   * Crea un pokemon el propio usuario desde la interfaz gráfica.
   */
  private function createFromUser()
  {
    $modelo_pokemon = new ModeloPokemon();
    $types = $modelo_pokemon->getTypes();

    if (isset($_POST) && !empty($_POST)) {

      $datos = $_POST;
      if ($modelo_pokemon->crearPokemon($datos)) {
        $_SESSION['mensajeUsuario'] = 'Pokemon Creado Correctamente';
        header('Location: ./?controlador=pokemon&metodo=listar');
      } else {
        $_SESSION['mensajeUsuario'] = 'Hubo un problema a la hora de crear el Pokemon';
        header('Location: ./?controlador=pokemon&metodo=listar');
      }
    } else {
      if (is_file("./app/vistas/pokemon/create_pokemon.tpl.php")) {
        require_once('./app/vistas/pokemon/create_pokemon.tpl.php');
      } else {
        throw new Exception('Vista no disponible');
      }
    }
  }

  /**
   * Se obtiene un pokemon de la api y se inserta en la base de datos.
   */
  private function createFromApi($params)
  {
    $modelo_pokemon = new ModeloPokemon();
    $datosPokemon = $modelo_pokemon->getPokemonFromApi($params['idApi']);
    // print_r($datosPokemon);
    if ($modelo_pokemon->crearPokemonFromApi($datosPokemon)) {
      print_r('pokemon introducido');
    } else {
      print_r('pokemon no introducido');
    }
  }

  /**
   * Se le pasa por parámetros el id del pokemon que se desea eliminar, verifica que es un número y posteriormente lo elimina.
   */
  public function remove($params)
  {
    $id = $params['id'];
    //Tenemos que asegurar que $id es un entero. En cualquier otro caso podría haber problemas de seguridad
    if (ctype_digit($id)) {

      $modelo_pokemon = new ModeloPokemon();

      if ($modelo_pokemon->eliminarPokemon($id)) {
        $this->mensajeUsuario = 'Pokemon eliminado correctamente';
      } else {
        $this->mensajeUsuario = 'No se ha encontrado al pokemon';
      }
    }

    $_SESSION['mensajeUsuario'] = $this->mensajeUsuario;

    header('Location: ./?controlador=pokemon&metodo=listar');
  }
}
