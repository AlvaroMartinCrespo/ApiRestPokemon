listeners();

/**
 * Función que crea todos los listeners necesarios para que la página funcione correctamente de forma dinámica.
 */
function listeners() {
  //Mensaje de información al usuario
  window.addEventListener(
    'load',
    () => {
      if (document.getElementById('alert')) {
        setTimeout(() => {
          document.getElementById('alert').classList.add('invisible');
        }, 2000);
      }
    },
    false
  );

  /**
   * Crea un objeto Ajax con el que se manda una petición Get a nuestro controlador de forma asíncrona para que realice una petición a la pokeApi para que nos genere más pokemon para inyectarlo en la página.
   */
  document.querySelector('.verMas').addEventListener(
    'click',
    () => {
      let xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          document.querySelector('.containerCartas').innerHTML += xhttp.response;
        }
      };
      xhttp.open('GET', 'http://localhost/pokemonDefinitivo/?controlador=api&metodo=verMas', true);
      xhttp.send();
    },
    false
  );
}
