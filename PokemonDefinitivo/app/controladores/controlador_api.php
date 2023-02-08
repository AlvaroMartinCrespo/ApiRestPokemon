<?php

class ControladorApi
{
    public function __construct()
    {
    }

    /**
     * Manda como respuesta a la petición un error 400.
     */
    private function error400()
    {
        $json = [
            'status' => 400,
            'results' => 'Bad Request',
        ];

        $json =
            json_encode($json);
        header('Content-Type: application/json; charset=utf-8');
        echo $json;
    }

    /**
     * Manda como respuesta a la petición un error 404.
     */
    private function error404()
    {
        $json = [
            'status' => 404,
            'results' => 'Not Found',
        ];

        $json =
            json_encode($json);
        header('Content-Type: application/json; charset=utf-8');
        echo $json;
    }

    /**
     * El servidor envia a la peticion un código de respuesta 200.
     */
    private function respuesta200()
    {
        $json = [
            'status' => 200,
            'results' => 'OK',
        ];

        $json =
            json_encode($json);
        header('Content-Type: application/json; charset=utf-8');
        echo $json;
    }

    /**
     * Se le pasa como parámetro una array, la convierte en Json y la envia como respuesta
     */
    private function enviarJson($array)
    {
        $toSend =
            json_encode($array);

        header('Content-Type: application/json; charset=utf-8');
        echo $toSend;
    }

    /**
     * Se le pasan los datos del parametro path y comprueba si son correctos los datos o no
     */
    private function comprobarDatos($path)
    {
        $correcto = false;
        $procesos = explode('/', $path);
        if ($procesos[0] === 'pokemon') {
            $correcto = true;
        }
        return $correcto;
    }

    /**
     * Se le pasa como parámetros los parámetros get que llegan por la URL, se comprueban que no esten vacios y con un switch se comprueba que tipo de solicitud ha hecho el cliente, y se manda una respuesta en cada caso.
     */
    public function procesar($params)
    {
        if (isset($params['path']) && !empty($params['path'])) {
            //Tipo de petición
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'PUT':
                    if ($this->comprobarDatos($params['path'])) {
                        $this->procesarPeticionPut($params['path']);
                    } else {
                        $this->error400();
                    }
                    break;
                case 'POST':
                    if ($this->comprobarDatos($params['path'])) {
                        $this->procesarPeticionPost();
                    } else {
                        $this->error400();
                    }
                    break;
                case 'GET':
                    if ($this->comprobarDatos($params['path'])) {
                        $this->procesarPeticionGet($params['path']);
                    } else {
                        $this->error400();
                    }
                    break;
                case 'DELETE':
                    if ($this->comprobarDatos($params['path'])) {
                        $this->procesarPeticionDelete($params['path']);
                    } else {
                        $this->error400();
                    }
                    break;
            }
        } else {
            $this->error400();
        }
    }

    /**
     * Acepta como parámetros los parámetros get de la URL, si tiene un id en los parámetros se le pasa el pokemon con ese id, sino tiene id, se mandan todos los pokemons.
     */
    private function procesarPeticionGet($params)
    {
        $datos = explode('/', $params);
        $modeloPokemon = new ModeloPokemon();

        if (!empty($datos[1]) && is_numeric($datos[1])) {
            $pokemon = $modeloPokemon->getPokemon($datos[1]);
            $arrayJson = ['nombre' => [$pokemon['nombre']], 'tipo' => [$pokemon['tipo']], 'descripcion' => ['descripcion'], 'id' => [$pokemon['id_pokemon']], 'url_pokemon' => [$pokemon['url_imagen']]];
            $this->enviarJson($arrayJson);
        } else {
            $pokemons = $modeloPokemon->getAllPokemons();
            $this->enviarJson($pokemons);
        }
    }

    /**
     * Si no hay información en el POST se manda un error 400, en el caso contrario los datos de post se introducen en una array con la que posteriormente se va a llamar al modelo pokemon para crear un nuevo pokemon y mandar un mensaje 200.
     */
    private function procesarPeticionPost()
    {
        if (isset($_POST) && !empty($_POST)) {

            $modeloPokemon = new ModeloPokemon();
            $datos = [];
            $datos['name'] = $_POST['nombre'];
            $datos['tipo'][0] = $_POST['tipo'];
            $datos['url'] = $_POST['url_imagen'];
            $datos['description'] = $_POST['descripcion'];
            $modeloPokemon->crearPokemon($datos)->fetchAll();

            $this->respuesta200();
        } else {
            $this->error400();
        }
    }

    /**
     * Se le pasa por parámetros el id del pokemos que se desea eliminar, si el parámetro esta vacío se mandará un error 400, sino el id no coincide con ningún pokemon se mandará un error 404, y si la query se ha ejecutado correctamente se mandará un mensaje 200.
     */
    private function procesarPeticionDelete($params)
    {
        $modeloPokemon = new ModeloPokemon();
        $datos = explode('/', $params);

        if (!empty($datos[1])) {
            if ($modeloPokemon->eliminarPokemon($datos[1])->rowCount()) {
                $this->respuesta200();
            } else {
                $this->error404();
            }
        } else {
            $this->error400();
        }
    }

    /**
     * Obtenemos los datos de los pokemons, llamamos al modelo y con el id del pokemon actualizamos a dicho pokemon, en caso de que no hubiera información, respondería con un error 400.
     */
    private function procesarPeticionPut($params)
    {

        $datos = explode('/', $params);

        if (!empty(file_get_contents("php://input"))) {

            $datosRecibidos = file_get_contents("php://input");
            $datosRecibidosDecode = json_decode($datosRecibidos);
            $modeloPokemon = new ModeloPokemon();
            if (!empty($datos[1])) {
                $datos = [];

                $datos['nombre'] = $datosRecibidosDecode->nombre;
                $datos['tipo'][0] = $datosRecibidosDecode->tipo;
                $datos['url'] = $datosRecibidosDecode->url;
                $datos['description'] = $datosRecibidosDecode->descripcion;

                $modeloPokemon->updatePokemon($datos[1], $datos)->fetchAll();

                $this->respuesta200();
            } else {
                $this->error400();
            }
        } else {
            $this->error400();
        }
    }

    /**
     * Función que llama al modelo Pokemon, para generar de forma asíncrona nuevos pokemons en la página.
     */
    public function verMas()
    {
        $modeloPokemon = new ModeloPokemon();
        echo $modeloPokemon->verMas();
    }
}
