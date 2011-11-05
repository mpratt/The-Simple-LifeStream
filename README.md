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
    - Busca por medallas que hayas recibido
- Github
    - Busca por proyectos iniciados.
    - Busca por proyectos actualizados (push).

Requerimientos
==============
- PHP >= 5.2
- CURL (extension para PHP)

Como se usa?
============
Antes de iniciar, debes escribir los datos necesarios en el archivo config.ini y luego puedes hacer lo siguiente:

    <?php
        require('directorio/a/SimpleLifestream.php');

        try {
			$lifestream = new SimpleLifestream();
			$output = $lifestream->getLifestream();
			var_dump($output);
		} catch (Exception $e) { echo 'Un error ha ocurrido!'; }
    ?>

O tambien puedes especificar los datos al instanciar el objeto SimpleLifestream.

	<?php
        require('directorio/a/SimpleLifestream.php');

        try {

			$config = array('NombreServicio' => array('username' => 'nombre-usuario-del-servicio'),
							'Youtube' => array('username' => 'nombre-usuario-youtube'),
							'Twitter' => array('username' => 'nombre-usuario-twitter'),
							'Github' => array('username' => 'nombre-usuario-Github'));

			$lifestream = new SimpleLifestream($config);
			$output = $lifestream->getLifestream();
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