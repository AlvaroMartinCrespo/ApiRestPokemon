<?php

Class Core{
  
    protected $controladorActual = 'controlador_pokemon';
    protected $metodoActual = 'listar';
    protected $parametros = [];

    //constructor
    public function __construct(){
        //Aquí sobreescribimos el controlador actual, el método y los parámetros que hay por defecto.
        if((isset($_GET['controlador']))&&(!empty($_GET['controlador']))){
          $this->controladorActual = 'controlador_'.filter_var($_GET['controlador'], FILTER_SANITIZE_URL);
        }
        if((isset($_GET['metodo']))&&(!empty($_GET['metodo']))){
            $this->metodoActual = filter_var($_GET['metodo'], FILTER_SANITIZE_URL);
        }
        
        $parametros = array_filter($_GET, fn($element)=>!in_array($element, ['controlador','metodo']), ARRAY_FILTER_USE_KEY);
        
        $this->parametros = filter_var_array($parametros, FILTER_SANITIZE_URL);
        
        $nombre_controlador = str_replace('_','' ,ucwords($this->controladorActual,'_'));    
        $this->controladorActual = new $nombre_controlador();

        if(method_exists($this->controladorActual, $this->metodoActual)){            
            $nombreMetodo = $this->metodoActual;
            $this->controladorActual->$nombreMetodo($this->parametros);
        }else{
          throw new Exception('Método no válido para el controlador');
        }
    
    }

}