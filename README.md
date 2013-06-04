The Simple Life(stream)
=======================
[![Build Status](https://secure.travis-ci.org/mpratt/The-Simple-LifeStream.png?branch=master)](http://travis-ci.org/mpratt/The-Simple-LifeStream)

Is a very simple and flexible library for your life-streaming purposes. It supports a bunch of third party services
and makes it easy for you to display all that information in one single place.

The sweet thing about this library is that it only returns an array with all the important data (date, html, etc).
This empowers you to play with that information and display that however you like. A couple of formatters are also available
and you can use them to ouput data in any way you like, see the examples below for more information.

In order to have a decent performance and avoid making too many requests to other sites the library uses internally a
Cache System based on files (file cache). The duration of each cache is 10 minutes by default, however you can modify
that behaviour easily.

The name of this library is inspired by that cheap reality show with Paris Hilton and Nicole Ritchie.

Supported Sites
===============
- Youtube
    - Finds all the videos added to the favorite playlist.
- Twitter
    - If the account can be accessed publicly, the library finds the latest tweets.
- StackOverflow
    - Finds the recent comments you have done.
    - Finds answers and questions you have written.
    - Reports questions that you have marked as answered.
    - Finds badges won.
- Github
    - Finds create and push events on a repo.
    - Finds create and push events on a gist.
    - Finds starred repos.
    - Finds followed users.
- Reddit
    - Finds links submitted.
    - Finds Comments on links.
- FacebookPages (Remember that a Facebook Page is different from a profile Page.)
    - Latest posts on a Page.
- Atom/RSS Feeds
    - Finds the latest titles/entries on a RSS/Atom Feed.

I plan on giving support to other websites on the future, but for now this are the only ones that are supported.
If you have any suggestions, you can use the issues tracker or you can contact me on my [Website](http://www.michael-pratt.com).

Requirements
============
- PHP >= 5.3
- Curl or the `allow_url_fopen` directive enabled on the php.ini

Installation
============

### Install with Composer
If you're using [Composer](https://github.com/composer/composer) to manage
dependencies, you can add this library with by adding the following lines
in your composer.json file.

        "require": {
            "mpratt/simple-lifestream": ">=3.0"
        }

After that you only need to run `composer.phar install`

### Standalone Installation (without Composer)
Download/clone this repository, place the `Lib/SimpleLifestream` directory on your project vendor directory.
Now you can either include the `Autoloader.php` file

    require 'path/to/SimpleLifestream/Autoload.php';
    $lifestream = new \SimpleLifestream\SimpleLifestream($services);

Or you could use an autoloader class that is [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compatible, and register
this library. Something around this lines:

    $loader->registerNamespace('SimpleLifestream', 'path/to/vendor/SimpleLifestream');

Basic Usage
===========

You can start by passing the service name and resource (username or url depending on which service you are
going to use) on construction.
```php
    <?php
        require 'your-autoloader.php';
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Reddit' => 'mpratt',
                                                                   'Github' => 'mpratt'));

        $stream = $lifestream->getLifestream();
        foreach ($stream as $s)
        {
            echo $s['html'];
        }
    ?>
```

Or if you prefer you can define each service individually. By using this method you can
set different usernames for a single Service.
```php
    <?php
        require('SimpleLifestream.php');

        $lifestream = new \SimpleLifestream\SimpleLifestream();
        $lifestream->loadService('Twitter', 'parishilton');
        $lifestream->loadService('Twitter', 'ThatKevinSmith');
        $lifestream->loadService('Twitter', 'YourOtherUserName');
        $lifestream->loadService('FacebookPages', '27469195051');
        $lifestream->loadService('Youtube', 'ERB');
        $lifestream->loadService('StackOverflow', '430087');
        $lifestream->loadService('Feed', 'http://www.smodcast.com/feed/');

        $stream = $lifestream->getLifestream();
        echo '<ul>';
        foreach ($stream as $s)
        {
            echo '<li>' . $s['html'] . '</li>';
        }
        echo '</ul>';
    ?>
```

The `getLifestream()` method accepts a number, which can be used to limit the latest information you want to get.
```php
    <?php
        $stream = $lifestream->getLifestream(10);
        echo count($stream); // 10
    ?>
```

You can check for errors with the `hasErrors()` and `getErrors()` methods. There is also a `getLastError()` method available.
```php
    <?php
        $stream = $lifestream->getLifestream();
        if ($lifestream->hasErrors())
        {
            var_dump($lifestream->getErrors());

            echo $lifestream->getLastError();
        }
    ?>
```

The `SimpleLifestream` constructor, accepts a second parameter, an array with configuration directives
that you can use to overwrite the behaviour of the library
```php
    <?php
        $config = array(
            'lang' => new \SimpleLifestream\Languages\Spanish(),
            'cache_dir' => '/path/to/new/cache/dir',
            'cache_ttl' => (60*20), // Modify the time to live
            'timeout' => 5, // A custom timeout, for the http requests
            'user_agent' => 'My Custom UserAgent For Http Requests',
        );

        $services = array('Github' => 'mpratt');

        $lifestream = new \SimpleLifestream\SimpleLifestream($services, $config);
    ?>
```

This library also has support for spanish output. You can even write your own translation object if you like.
```php
    <?php
        $config = array('lang' => new \SimpleLifestream\Languages\Spanish());
        $services = array('Twitter' => 'parishilton');

        $lifestream = new \SimpleLifestream\SimpleLifestream($services, $config);
        $stream = $lifestream->getLifestream(10);
        var_dump($stream);
    ?>
```

Are there any event types you want to ignore? I've got your back!
```php
    <?php
        $lifestream->ignoreType('starred', 'Github'); // Ignore Github Starred Repos
        $lifestream->ignoreType('favorited'); // Ignore all favorited actions
        $stream = $lifestream->getLifestream();
    ?>
```

You want to specify another directory for the cache engine?
```php
    <?php
        $config = array('cache_dir' => '/path/to/your/dir');
        $services = array('Twitter' => 'parishilton');

        $lifestream = new \SimpleLifestream\SimpleLifestream($services, $config);
        $stream = $lifestream->getLifestream(10);
        var_dump($stream);
    ?>
```

Or if you like to disable Caching
```php
    <?php
        $config = array('cache' => false);
        $services = array('Twitter' => 'parishilton');

        $lifestream = new \SimpleLifestream\SimpleLifestream($services, $config);
    ?>
```

Or perhaps make the cache last longer?
```php
    <?php
        $config = array('cache_ttl' => (60*30)); // 30 minutes
        $services = array('Twitter' => 'parishilton');

        $lifestream = new \SimpleLifestream\SimpleLifestream($services, $config);
    ?>
```

If you want to see more examples of how to use this library take a peak into the Tests directory and view the files.
Otherwise inspect the source code of the library, I would say that it has a "decent" english documentation and it should be easy to follow.
The Test Coverage is also fairly decent.

Sample Output
=============

```php
    <?php

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Reddit' => 'mpratt',
                                                                   'Github' => 'mpratt'));

        $stream = $lifestream->getLifestream(3);
        var_dump($stream);

        array(3) {
            [0]=>
              array(9) {
                    ["service"]=> string(6) "github"
                    ["type"]=> string(9) "pushEvent"
                    ["resource"]=> string(6) "mpratt"
                    ["url"]=> string(42) "https://github.com/mpratt/Bolido-Framework"
                    ["text"]=> string(16) "Bolido-Framework"
                    ["stamp"]=> int(1348070514)
                    ["date"]=> string(xx) Y-m-d H:i:s
                    ["link"]=> string(73) "<a href="https://github.com/mpratt/Bolido-Framework">Bolido-Framework</a>"
                    ["html"]=> string(97) "pushed a new commit to <a href="https://github.com/mpratt/Bolido-Framework">Bolido-Framework</a>."
              }
            [1]=>
              array(9) {
                ["service"]=> string(6) "reddit"
                ["type"]=> string(9) "commented"
                ["resource"]=> string(6) "mpratt"
                ["url"]=> string(52) "http://www.reddit.com/r/aww/comments/103fz6/#c6a9nqy"
                ["text"]=> string(41) "Went for a hike and found some adorable. "
                ["stamp"]=> int(1348027287)
                ["date"]=> string(xx) Y-m-d H:i:s
                ["link"]=> string(108) "<a href="http://www.reddit.com/r/aww/comments/103fz6/#c6a9nqy">Went for a hike and found some adorable. </a>"
                ["html"]=> string(124) "commented on "<a href="http://www.reddit.com/r/aww/comments/103fz6/#c6a9nqy">Went for a hike and found some adorable. </a>"."
              }
            [2]=>
              array(9) {
                ["service"]=> string(6) "reddit"
                ["type"]=> string(9) "commented"
                ["resource"]=> string(6) "mpratt"
                ["url"]=> string(51) "http://www.reddit.com/r/PHP/comments/t662j/#c4khrop"
                ["text"]=> string(21) "Parsing Youtube links"
                ["stamp"]=> int(1336249690)
                ["date"]=> string(xx) Y-m-d H:i:s
                ["link"]=> string(87) "<a href="http://www.reddit.com/r/PHP/comments/t662j/#c4khrop">Parsing Youtube links</a>"
                ["html"]=> string(103) "commented on "<a href="http://www.reddit.com/r/PHP/comments/t662j/#c4khrop">Parsing Youtube links</a>"."
              }
        }
    ?>
```

License
=======
MIT
For the full copyright and license information, please view the LICENSE file.

Author
=====

Michael Pratt
[Personal Website](http://www.michael-pratt.com)
