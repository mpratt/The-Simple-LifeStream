The Simple Life(stream)
=======================
Is a very simple library for your life-streaming purposes. It supports a bunch of third party services
and makes it easy for you to display all that information in one single place.

The sweet thing about this library is that it only returns an array with all the important data (date and html).
This empowers you to play with that information and display that however you like.

In order to have a decent performance, and avoid making too many requests to other sites
the library uses internally a Cache System based on files (file cache). The duration of each cache is 10 minutes by
default, however you can modifiy that behaviour easily.

Supported Sites
=================
- Youtube
    - Searchs for all the videos added to the favorite playlist.
- Twitter
    - If the account can be accessed publicly, the library fetches for all the latest tweets.
- StackOverflow
    - Fetches recent comments.
    - Fetches Answers and questions.
    - Fetces badges.
- Github
    - Searchs for Create and Push events on a Repo.
    - Searchs for Create and Push events on a Gist.
- Reddit
    - Links submitted.
    - Comments on links.
    - You can also display your saved links. Look at Tests/TestSimpleLifestream.php to se how.
- FacebookPages
    - Latest posts on a Page.
    - Remember that a Facebook Page is different than a profile Page.
- Atom/RSS Feeds
    - Fetches for the latest titles on a RSS/Atom Feed.

Requirements
==============
- PHP >= 5.2
- CURL

How To use it?
==============

You can start by passing all the important data on construction.

    <?php
        require('......./SimpleLifestream.php');
        $config = array('ServiceName' => array('username' => 'user-name'),
                        'Youtube' => array('username' => 'user-name-youtube'),
                        'Twitter' => array('username' => 'user-name-twitter'),
                        'Github' => array('username' => 'user-name-Github'),
                        'FacebookPages' => array('username' => 'facebook-page-id'));

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

Or if you prefer you can call each service individually

    <?php
        require('................../SimpleLifestream.php');
        $lifestream = new SimpleLifestream();
        $lifestream->loadService('Twitter', array('username' => 'twitter-username'));
        $lifestream->loadService('Twitter', array('username' => 'another-twitter-username'));
        $lifestream->loadService('Youtube', array('username' => 'youtube-username'));

        $output = $lifestream->getLifestream();
        var_dump($output);
    ?>

The **getLifestream()** method accepts a number, in that way you can limit the last information you want to get.

    <?php
        $output = $lifestream->getLifestream(10);
        var_dump($output);
        // Display the last 10 recent events.
    ?>

If you want to see more examples of how to use this library take a peek into the Tests directory and view the
TestSimpleLifestream.php file. Otherwise peek the source code of the library, I would say that it has a "decent"
documentation.

License
=======
MIT
For the full copyright and license information, please view the LICENSE file.

Autor
=====
Michael Pratt

[Página Personal](http://www.michael-pratt.com)
