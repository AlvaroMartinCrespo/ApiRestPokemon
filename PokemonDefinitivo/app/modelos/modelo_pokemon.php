<?php

class ModeloPokemon
{

    private $host = DB_HOST;
    private $usuario = DB_USER;
    private $password = DB_PASSWORD;
    private $nombre_base = DB_NAME;

    private $manejador_conexion;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->nombre_base;
        //Explicar en qué contexto puede ser útil la conexión persistente tal y como se crea.
        $opts = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );

        //Explicar por qué hemos decidido no utilizar aquí un bloque try-catch
        $this->manejador_conexion = new PDO($dsn, $this->usuario, $this->password, $opts);
        $this->manejador_conexion->exec('set names utf8');
    }

    /**
     * Función que realiza una llamada a la apiPokemon de forma asíncrona, seleccionando la cantidad de pokemon y a partir de que pokemon quieres listar
     */
    public function verMas()
    {
        //Número de pokemos que tenemos al principio, por lo cual a partir de ese id traeremos los pokemons
        $numPokemonInicial = 20;
        //Número de pokemons que nos queremos traer.
        $limite = 3;


        if (isset($_SESSION['pagina']) && !empty($_SESSION['pagina'])) {
            $_SESSION['pagina'] += $limite;
            $numPagPkm = $_SESSION['pagina'];
        } else {

            $numPagPkm = $numPokemonInicial + $limite;
            $_SESSION['pagina'] = $numPagPkm;
        }

        $url = 'https://pokeapi.co/api/v2/pokemon?limit=' . $limite . '&offset=' . $numPagPkm . '';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseApi = curl_exec($ch);
        $responseApi = json_decode($responseApi);
        curl_close($ch);
        $html = '';

        for ($i = 0; $i < $limite; $i++) {

            $url = $responseApi->results[$i]->url;
            $ch = curl_init($url);
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );
            $response = curl_exec($ch);
            $response = json_decode($response);
            curl_close($ch);

            $html .= '        <div>
            <div class="card">
                <img class="card-img-top" src="' . $response->sprites->front_default . '" alt="Imagen de la tarjeta" />
                <div class="card-body d-flex justify-content-center align-items-center flex-column">
                    <h5 class="card-title">' . $responseApi->results[$i]->name . '</h5>
                    <p class="card-text">' . $response->types[0]->type->name . '</p>
                    <a href="./?controlador=pokemon&metodo=ver&id=' . $response->id . '" class="btn btn-primary">Detalles</a>
                </div>
            </div>
        </div>';
        }

        $_SESSION['next'] = $responseApi->next;

        return $html;
    }

    /**
     * Esta función obtiene los diferentes tipos que hay en la base de datos.
     */
    public function getTypes()
    {
        $query = 'SELECT tipos.id_tipo, tipos.nombre FROM tipos';
        return $this->manejador_conexion->query($query)->fetchAll();
    }

    /**
     * Obtiene todos los pokemons de la base de datos.
     */
    public function getAllPokemons()
    {
        $resultado = $this->manejador_conexion->query('SELECT pokemons.id_pokemon, pokemons.nombre, tipos.nombre AS tipo, pokemons.url_imagen FROM pokemons INNER JOIN tipos ON pokemons.tipo = tipos.id_tipo')->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    /**
     * Obtiene los n primeros pokemons de la PokeApi.
     */
    public function getAllPokemonsFromApi()
    {
        $numPokemons = 20;
        $urlApi = 'https://pokeapi.co/api/v2/pokemon?limit=' . $numPokemons . '';

        $ch = curl_init($urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseApi = curl_exec($ch);
        $responseApi = json_decode($responseApi);
        curl_close($ch);

        $datos = [];

        for ($i = 0; $i < $numPokemons; $i++) {

            $url = $responseApi->results[$i]->url;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $response = json_decode($response);
            curl_close($ch);

            $datos[$i]['nombre'] = $responseApi->results[$i]->name;
            $datos[$i]['id_pokemon'] = $response->id;
            $datos[$i]['tipo'] = $response->types[0]->type->name;
            $datos[$i]['url_imagen'] = $response->sprites->front_default;
        }

        return $datos;
    }

    /**
     * Se le pasa un parámetros Id y obtiene el pokemon que posea esa id de la PokeApi.
     */
    public function getPokemonFromApi($id)
    {

        $urlApi = 'https://pokeapi.co/api/v2/pokemon/' . $id;
        $datos = [];
        $ch = curl_init($urlApi);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseApi = curl_exec($ch);
        $responseApi = json_decode($responseApi);
        curl_close($ch);

        $datos['nombre'] = $responseApi->forms[0]->name;
        $datos['id_pokemon'] = $responseApi->id;
        $datos['descripcion'] = 'Esta es una descripción genérica de Pokemons ya que en la Api no hay descripción del Pokemon';
        $datos['tipo'] = $responseApi->types[0]->type->name;
        $datos['url_imagen'] = $responseApi->sprites->front_default;


        return $datos;
    }

    /**
     * Se le pasa como parámetro un id, y se realiza una query a la base de datos y devuelve el pokemon que posea esa id.
     */
    public function getPokemon($id)
    {
        $resultado = $this->manejador_conexion->query('SELECT pokemons.id_pokemon, pokemons.nombre, tipos.nombre AS tipo, pokemons.url_imagen, pokemons.descripcion FROM pokemons INNER JOIN tipos ON pokemons.tipo = tipos.id_tipo WHERE pokemons.id_pokemon = \'' . $id . '\'')->fetchAll(PDO::FETCH_ASSOC);

        return reset($resultado);
    }

    /**
     * Se le para como parámetro un id y los datos del pokemon que se quiere actualizar, y el pokemon que posea esa id es actualizado en la base de datos.
     */
    public function updatePokemon($id, $datos)
    {
        $query = 'UPDATE pokemons SET nombre = "' . $datos['nombre'] . '", tipo="' . $datos['tipo'][0] . '", url_imagen="' . $datos['url'] . '", descripcion="' . $datos['description'] . '" WHERE id_pokemon ="' . $id . '"';
        return $this->manejador_conexion->query($query);
    }

    /**
     * Se le pasa como parámetro un id, y el pokemon que posea ese id será eliminado.
     */
    public function eliminarPokemon($id)
    {
        $query = 'DELETE FROM pokemons WHERE id_pokemon=' . $id;
        return $this->manejador_conexion->query($query);
    }

    /**
     * Se le pasa por parámetros los datos del pokemon que se quiere crear, se realiza una query y se inserta en la base de datos.
     */
    public function crearPokemon($datos)
    {
        $tipo = reset($datos['tipo']);
        $query = 'INSERT INTO pokemons (nombre, tipo, url_imagen, descripcion) values ("' . $datos['nombre'] . '",' . $tipo . ',"' . $datos['url'] . '","' . $datos['descripcion'] . '")';
        return $this->manejador_conexion->query($query);
    }

    /**
     * Se la pasa por parámetros los datos de la Api, y se insertan en la base de datos mediante una query.
     */
    public function crearPokemonFromApi($datos)
    {
        // $query = 'INSERT INTO pokemons (nombre, tipo, url_imagen, descripcion) values ("' . $datos['nombre'] . '",' . $datos['tipo'] . ',"' . $datos['url_imagen'] . '","' . $datos['descripcion'] . '")';
        // return $this->manejador_conexion->query($query);

        // print_r($datos);
        $query = 'INSERT INTO pokemons (nombre, tipo, url_imagen, descripcion) values ("' . $datos['nombre'] . '",' . 1 . ',"' . $datos['url_imagen'] . '","' . $datos['descripcion'] . '")';
        return $this->manejador_conexion->query($query);
    }
}
