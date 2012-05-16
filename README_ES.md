The Simple Life(stream)
=======================
Es una librería en PHP que busca por las actividades que ha hecho un usuario en distintas páginas web.
De esta manera puedes agregar tus cuentas de distintos sitios y mostrar esa información en un solo lugar.

Esta librería solo pretende entregar un vector (array) con los datos necesarios para que tu decidas como manipular
esa información y como presentarla en tu página web.

Simple Life(stream) usa internamente un sistema de cache para mejorar un poco su rendimiento. Por defecto
la duración del cache es de 10 minutos, pero eso es fácilmente modificable.

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
- Atom/RSS Feeds
    - Busca por información contenida en feeds Atom/RSS (AtomService).

Requerimientos
==============
- PHP >= 5.2
- CURL (extension para PHP)

Como se usa?
============

Al instanciar el objeto debes especificar los datos necesarios.

    <?php
        require('directorio/a/SimpleLifestream.php');
        $config = array('NombreServicio' => array('username' => 'nombre-usuario-del-servicio'),
                        'Youtube' => array('username' => 'nombre-usuario-youtube'),
                        'Twitter' => array('username' => 'nombre-usuario-twitter'),
                        'Github' => array('username' => 'nombre-usuario-Github'),
                        'FacebookPages' => array('username' => 'id-página-facebook'));

        $lifestream = new SimpleLifestream($config);

        $output = $lifestream->getLifestream();

        if ($lifestream->hasErrors())
        {
            var_dump($lifestream->getErrors());
            die();
        }
        else
            var_dump($output);
    ?>

O puedes especificar los servicios individualmente, incluso puedes especificar varias cuentas  de un mismo servicio.

    <?php
        require('directorio/a/SimpleLifestream.php');
        $lifestream = new SimpleLifestream();
        $lifestream->loadService('Twitter', array('username' => 'nombre-usuario-twitter-uno'));
        $lifestream->loadService('Twitter', array('username' => 'nombre-usuario-twitter-dos'));
        $lifestream->loadService('Youtube', array('username' => 'nombre-usuario-youtube'));

        $output = $lifestream->getLifestream();
        var_dump($output);
    ?>

El método getLifestream() acepta como parámetro un número entero para delimitar el resultado de las acciones más recientes.

    <?php
        $output = $lifestream->getLifestream(10);
        var_dump($output);
        // Muestra las 10 acciónes más recientes.
    ?>

Si quieres ver más ejemplos de uso puedes ir al directorio Tests y ver el contenido de TestSimpleLifestream.php
o incluso puedes ver el código fuente que esta "decentemente" documentado.

Licencia
========
MIT 
El Archivo LICENSE contiene la licencia completa!

Autor
=====
Michael Pratt

[Página Personal](http://www.michael-pratt.com)
