The Simple Life(stream)
=======================
Es una "librería" en PHP que busca por las acciones de un usuario en distintas páginas web.
De esta manera puedes agregar tus cuentas de distintos sitios y mostrar esa información en un solo lugar.

Esta librería solo pretende entregar un vector (array) con los datos necesarios para que tu decidas como manipular
esa información y como presentarla en tu página web.

Sitios Soportados
=================
- Youtube
    - Busca por videos que se hayan agregado a la lista de favoritos.
- Twitter
    - Busca por los últimos tweets enviados (siempre y cuando el perfil sea público).
- StackOverflow
    - Busca por Comentarios.
    - Busca por Preguntas/Respuestas publicadas.
    - Busca por medallas que hayas recibido.
- Github
    - Busca por proyectos iniciados o actualizados.
    - Busca por Gists Creados o actualizados.
- FacebookPages
    - Coge toda la información de una página de Facebook.
    - Ojo, Facebook Pages es distinto a tu página de perfil en facebook.

Requerimientos
==============
- PHP >= 5.2
- CURL (extension para PHP)

Como se usa?
============
Inicialmente puedes editar el archivo config.ini con los servicios necesarios y pasarlo al constructor.

    <?php
        require('directorio/a/SimpleLifestream.php');

        try {
            $configFile = dirname(__FILE__) . '/config.ini';
            $lifestream = new SimpleLifestream($configFile);
            $output = $lifestream->getLifestream();
            var_dump($output);
        } catch (Exception $e) { echo 'Un error ha ocurrido!'; }
    ?>

Tambien puedes especificar los datos al instanciar el objeto SimpleLifestream.

    <?php
        require('directorio/a/SimpleLifestream.php');

        try {

            $config = array('NombreServicio' => array('username' => 'nombre-usuario-del-servicio'),
                            'Youtube' => array('username' => 'nombre-usuario-youtube'),
                            'Twitter' => array('username' => 'nombre-usuario-twitter'),
                            'Github' => array('username' => 'nombre-usuario-Github'),
                            'FacebookPages' => array('username' => 'id-página-facebook'));

            $lifestream = new SimpleLifestream($config);
            $output = $lifestream->getLifestream();
            var_dump($output);

        } catch (Exception $e) { echo 'Un error ha ocurrido!'; }
    ?>

O puedes especificar los servicios individualmente, incluso puedes especificar varias cuentas  de un mismo servicio.

    <?php
        require('directorio/a/SimpleLifestream.php');

        try {

            $lifestream = new SimpleLifestream();
            $lifestream->loadService('Twitter', array('username' => 'nombre-usuario-twitter-uno'));
            $lifestream->loadService('Twitter', array('username' => 'nombre-usuario-twitter-dos'));
            $lifestream->loadService('Youtube', array('username' => 'nombre-usuario-youtube'));
            $output = $lifestream->getLifestream();
            var_dump($output);

        } catch (Exception $e) { echo 'Un error ha ocurrido!'; }
    ?>

El método getLifestream() acepta como atributo un número entero para delimitar el resultado de las acciones más recientes.

    <?php
        require('directorio/a/SimpleLifestream.php');

        try {

            $lifestream = new SimpleLifestream();
            $lifestream->loadService('Twitter', array('username' => 'nombre-usuario-twitter'));
            $lifestream->loadService('Youtube', array('username' => 'nombre-usuario-youtube'));
            $output = $lifestream->getLifestream(10);
            var_dump($output);

        } catch (Exception $e) { echo 'Un error ha ocurrido!'; }
    ?>

Licencia
========
MIT - El Archivo LICENSE contiene la licencia completa!

Autor
=====
Michael Pratt

[Página Personal](http://www.michael-pratt.com)