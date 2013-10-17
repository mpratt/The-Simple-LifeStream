The Simple Life(stream)
=======================
[![Build Status](https://secure.travis-ci.org/mpratt/The-Simple-LifeStream.png?branch=master)](http://travis-ci.org/mpratt/The-Simple-LifeStream) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/mpratt/The-Simple-LifeStream/badges/quality-score.png?s=5ce8505d3304732575b54e37df6057333645040c)](https://scrutinizer-ci.com/g/mpratt/The-Simple-LifeStream/) [![Code Coverage](https://scrutinizer-ci.com/g/mpratt/The-Simple-LifeStream/badges/coverage.png?s=d3f6c51de6584f1ae196cb609bfaacd54c2e34be)](https://scrutinizer-ci.com/g/mpratt/The-Simple-LifeStream/) [![Latest Stable Version](https://poser.pugx.org/mpratt/simple-lifestream/version.png)](https://packagist.org/packages/mpratt/simple-lifestream) [![Total Downloads](https://poser.pugx.org/mpratt/simple-lifestream/downloads.png)](https://packagist.org/packages/mpratt/simple-lifestream)

A very simple and flexible library for your life-streaming purposes. It supports a bunch of third party providers
and makes it easy for you to display all that information in one single place.

The sweet thing about this library is that it only returns an array with all the important data (date, html, etc).
This empowers you to play with that information and display it however you like. A couple of formatters are also available
and you can use them to ouput data in any way you like, see the examples below for more information.

In order to have a decent performance and avoid making too many requests to other sites the library uses internally a
Cache System based on files (file cache). The duration of each cache is 10 minutes by default, however you can modify
that behaviour easily.

The name of this library is inspired by that old cheap reality show with Paris Hilton and Nicole Ritchie.

Supported Sites
===============

- FacebookPages
- Atom/RSS Feeds
- Github
- Reddit
- StackExchange/StackOverflow
- Twitter (Important! You have to [register an app](http://dev.twitter.com/apps) first)
- Youtube

For a more detailed information about each provider read the [STREAMS.md](https://github.com/mpratt/The-Simple-LifeStream/blob/master/STREAMS.md) file.

Requirements
============

- PHP >= 5.3
- Curl or `allow_url_fopen` must be enabled

Installation
============

### Install with Composer

If you're using [Composer](https://github.com/composer/composer) to manage your
dependencies, you can use this library by creating a composer.json and adding this:

    {
        "require": {
            "mpratt/simple-lifestream": "~4.0"
        }
    }

Save it and run `composer.phar install`

### Standalone Installation (without Composer)

Download the latest release or clone this repository, place the `Lib/SimpleLifestream` directory on your project. Afterwards, you only need to include
the `Autoload.php` file.

```php
    require '/path/to/SimplreLifestream/Autoload.php';
    $lifestream = new \SimpleLifestream\SimpleLifestream();
```

Or if you already have PSR-0 complaint autoloader, you just need to register the library

```php
    $loader->registerNamespace('SimpleLifestream', 'path/to/SimpleLifestream');
```

Basic Usage
===========

Create an array with valid stream providers and pass it to the `SimpleLifestream` object.

```php
    $streams = array(
        new \SimpleLifestream\Stream('Reddit', 'mpratt'),
        new \SimpleLifestream\Stream('Github', 'mpratt'),
        new \SimpleLifestream\Stream('Youtube', 'ERB'),
        new \SimpleLifestream\Stream('StackOverflow', '430087'),
        new \SimpleLifestream\Stream('FacebookPages', '27469195051'),
        new \SimpleLifestream\Stream('Feed', 'http://www.michael-pratt/blog/rss/'),
    );

    $lifestream = new \SimpleLifestream\SimpleLifestream();
    $lifestream->loadStreams($streams);

    $data = $lifestream->getLifestream();
    foreach ($data as $d)
    {
        echo $d['html'];
    }
```

The `getLifestream(int 0)` method accepts a number, which can be used to limit the latest information you want to get.

```php
    $data = $lifestream->getLifestream(10);
    echo count($data); // 10
```

### Configuration Directives

The `SimpleLifestream` constructor, accepts an array with configuration directives
that you can use to modify some parts of the library.

```php
    $config = array(
        'date_format' => 'Y-m-d H:i', // Date format returned on by the streams
        'link_format' => '<a href="{url}">{text}</a>', // Link template used by the streams
        'language' => 'English', // The Output language
        'cache_ttl' => (60*10), // Duration of the cache in seconds
        'cache_dir' => '/path/bla/bla', // Optional place where the cache is going to be stored
    );

    $lifestream = new \SimpleLifestream\SimpleLifestream($config);
```

For Example, this library has support for English and Spanish languages. If you want the
output to be in spanish, you just need to write:

```php
    $config = array(
        'language' => 'Spanish',
    );

    $streams = array(
        new \SimpleLifestream\Stream('Reddit', 'mpratt'),
        new \SimpleLifestream\Stream('Github', 'mpratt'),
    );

    $lifestream = new \SimpleLifestream\SimpleLifestream($config);
    $data = $lifestream->loadStreams($streams)->getLifestream();

    foreach ($data as $d)
        echo $d['html'];
```

Stream Configuration
====================

The `\SimpleLifestream\Stream` object requires two parameters. The first one is a string containing the name of the Provider.
When an Invalid Provider is given, an `InvalidArgumentException` is thrown.

The second argument can be a string with the relevant resource/url/username or an array with important configuration options.
The regular way of registring a stream is:

```php
    $streams = array(
        new \SimpleLifestream\Stream('Github', 'mpratt'),
        new \SimpleLifestream\Stream('Youtube', 'ERB'),
    );
```

Or use an associative array with the `resource` key:

```php
    $streams = array(
        new \SimpleLifestream\Stream('Github', array('resource' => 'mpratt')),
        new \SimpleLifestream\Stream('Youtube', array('resource' => 'ERB')),
    );
```

The `resource` key is used internally and is interpreted as the relevant username/url/userid needed for
the current provider.

That being said, some streams require additional information in order to function. For example the `Twitter` provider.
Remember that you have to  [register an app](http://dev.twitter.com/apps) in order to use it and retrieve your latest tweets:

```php
    $streams = array(
        new \SimpleLifestream\Stream('twitter', array(
            'consumer_key'    => 'your consumer key',
            'consumer_secret' => 'your consumer secret',
            'access_token' => 'you access token',
            'access_token_secret' => 'your access token secret',
            'resource' => 'your twitter username',
        ))
    );

    $lifestream = new \SimpleLifestream\SimpleLifestream();
    $output = $lifestream->loadStreams($streams)->getLifestream();
    print_r($output);
```

You can use this technique on a few providers to modify their behaviour in some ways. For example the `StackExchange` Provider
gives you access to all the sites inside the **StackExchange** web ring, not just stackOverflow. Lets say for example we want
to get the data from a user in `http://programmers.stackexchange.com`.

```php
    $streams = array(
        new \SimpleLifestream\Stream('StackExchange', array(
            'site' => 'programmers',
            'resource' => '430087',
        ))
    );

    $lifestream = new \SimpleLifestream\SimpleLifestream();
    $output = $lifestream->loadStreams($streams)->getLifestream();
    print_r($output);
```

For More Information about streams and their individual configuration options read the [STREAMS.md](https://github.com/mpratt/The-Simple-LifeStream/blob/master/STREAMS.md) file.

Advanced Usage
==============

### Error Checking

There are 3 methods for error checking `bool hasErrors()`, `array getErrors()` and `string getLastError()`

```php
    $data = $lifestream->getLifestream();
    if ($lifestream->hasErrors())
        echo $lifestream->getLastError();

    if ($lifestream->hasErrors())
        var_dump($lifestream->getErrors());
```

### Ignoring Actions/Types

As you can see, some services detect multiple actions, but in some cases you might not want to have
all that information. You can ignore it if you want by using the `ignore()` method.

```php
    // Tell the library to Ignore all favorited actions/types
    $lifestream->ignore('favorited');

    $data = $lifestream->getLifestream();
```

Or you can restrict the action to a particular stream provider

```php
    // Tell the library to Ignore all starred actions/types only from the Github Provider
    $lifestream->ignore('favorited', 'Github');

    $data = $lifestream->getLifestream();
```

### Output Formatting

Lets talk about output formatters. There are 2 formatters (`HtmlList` and `Template`) that can help you
display the data in different ways.

In order to use them, you have to apply the decorator pattern. When doing this, the `getLifestream()` method gets
transformed and instead of returning an array with information, it returns a string with the requested data inside
a template.

Lets have a look at the `HtmlList` decoration:

```php
    <?php
        $config = array();

        $streams = array(
            new \SimpleLifestream\Stream('Reddit', 'mpratt')
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream($config);
        $lifestream = new \SimpleLifestream\Formatters\HtmlList($lifestream);
        $lifestream->loadStreams($streams);
        echo $lifestream->getLifestream(4);

        /* This prints something around this lines:
           <ul class="simplelifestream">
            <li class="servicename">Y-m-d H:i - <a href="...">text 1</a></li>
            <li class="servicename">Y-m-d H:i - <a href="...">text 2</a></li>
            <li class="servicename">Y-m-d H:i - <a href="...">text 3</a></li>
            <li class="servicename">Y-m-d H:i - <a href="...">text 4</a></li>
           </ul>
        */
    ?>
```

The other decoration is named `Template` and is a little more flexible, you can use it define your own templates
and with the help of some placeholders, you can interpolate the data fetched by the library.

```php
    <?php
        $lifestream = new \SimpleLifestream\Formatters\Template(new \SimpleLifestream\SimpleLifestream());
        $lifestream->setTemplate('<div class="{service}">{text} {link}</div>');
        echo $lifestream->loadStreams($streams)->getLifestream();

        /* This prints something round this lines:
            <div class="servicename">a text <a href="..">a link</a></div>
            <div class="servicename">another text <a href="..">a link</a></div>
            <div class="servicename">and more text <a href="..">a link</a></div>
        */
    ?>
```

If you want to see more examples of how to use this library take a peak inside the `Tests` directory and view the files.
Otherwise inspect the source code of the library, I would say that it has a "decent" english documentation and it should be easy to follow.

License
=======
MIT
For the full copyright and license information, please view the LICENSE file.

Author
=====

Michael Pratt
[Personal Website](http://www.michael-pratt.com)
