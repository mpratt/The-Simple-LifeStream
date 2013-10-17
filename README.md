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
    - Finds The latests Tweets. Important! You have to [register an app](http://dev.twitter.com/apps) in order to do this.
      More information in the `Stream Configuration` Part.
- StackExchange/StackOverflow
    - Finds the recent comments on questions.
    - Finds answers and questions you have written.
    - Reports questions that you have marked as answered.
    - Finds awarded badges.
- Github
    - Finds create, push and pull requests events on a repo.
    - Finds starred repos.
    - Finds followed users.
    - Finds Issues created.
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
- Curl or `allow_url_fopen` must be enabled

Installation
============

### Install with Composer

If you're using [Composer](https://github.com/composer/composer) to manage
dependencies, you can use this library by creating a composer.json and adding this:

    {
        "require": {
            "mpratt/simple-lifestream": "~4.0"
        }
    }

Save it and run `composer.phar install`

### Standalone Installation (without Composer)

Download the latest release or clone this repository, place the `Lib/SimpleLifestream` directory on your project. Afterwards, you only need to include
the Autoload.php file.

```php
    require '/path/to/SimplreLifestream/Autoload.php';
    $lifestream = new \SimpleLifestream\SimpleLifestream();
```

Or if you already have PSR-0 complaint autoloader, you just need to register Embera

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
        'date_format' => 'Y-m-d H:i', // Date format
        'link_format' => '<a href="{url}">{text}</a>', // Link template
        'language' => 'English', // Output language
        'cache_ttl' => (60*10), // Duration of the cache in seconds
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

Or you can restrict the action to a particulas stream provider

```php
    // Tell the library to Ignore all starred actions/types only for the
    // Github Provider
    $lifestream->ignore('favorited', 'Github');

    $data = $lifestream->getLifestream();
```

### Output Formatting

Lets talk about output formatters. There are a couple of formatters that can help you
display the data in different ways. The first one is the `HtmlList` Formatter. This is
how it goes:

```php
    <?php
        $services = array('Youtube' => 'mychannel');
        $lifestream = new \SimpleLifestream\SimpleLifestream($services);
        $lifestream = new \SimpleLifestream\Formatters\HtmlList($lifestream);
        echo $lifestream->getLifestream(4);

        /* This prints something round this lines:
           <ul class="simplelifestream">
            <li class="servicename"><a href="...">text 1</a></li>
            <li class="servicename"><a href="...">text 2</a></li>
            <li class="servicename"><a href="...">text 3</a></li>
            <li class="servicename"><a href="...">text 4</a></li>
           </ul>
        */
    ?>
```

The other formatter is a little more flexible, you can use it define your own template
and with the help of some placeholders, you can interpolate the data fetched by the library.

```php
    <?php
        $services = array('Youtube' => 'mychannel');
        $lifestream = new \SimpleLifestream\SimpleLifestream($services);
        $lifestream = new \SimpleLifestream\Formatters\Template($lifestream);
        $lifestream->setTemplate('<div class="{service}">{text}{link}</div>');
        echo $lifestream->getLifestream();

        /* This prints something round this lines:
            <div class="servicename">a text <a href="..">a link</a></div>
            <div class="servicename">another text <a href="..">a link</a></div>
            <div class="servicename">and more text <a href="..">a link</a></div>
        */
    ?>
```

If you want to see more examples of how to use this library take a peak inside the Tests directory and view the files.
Otherwise inspect the source code of the library, I would say that it has a "decent" english documentation and it should be easy to follow.
The Test Coverage is also fairly decent.

Stream Configuration
====================

The `\SimpleLifestream\Stream` object needs two parameters. The first one is a string containing the name of the Provider.
When an Invalid Provider is given, an Exception is thrown.

The second argument can be a string giving the relevant resource/username or an array with important configuration options.
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

That being said, the Twitter service requires additional information in order to retrieve the tweets. Remember that you have to  [register an app](http://dev.twitter.com/apps)
in order to use the Twitter Provider.

```php
    $streams = array(
        new \SimpleLifestream\Stream('twitter', array(
            'consumer_key'    => 'your consumer key',
            'consumer_secret' => 'your consumer secret',
            'token'           => 'you access token',
            'token_secret'    => 'your access token secret',
            'resource' => 'your username',
        ))
    );

    $lifestream = new \SimpleLifestream\SimpleLifestream();
    $output = $lifestream->loadStreams($streams)->getLifestream();
    print_r($output);
```

You can use this technique on a few providers to modify their behaviour in some ways. For example the StackExchange Provider
gives you access to all the sites inside the StackExchange web ring, not just stackOverflow. Lets say for example we want
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


License
=======
MIT
For the full copyright and license information, please view the LICENSE file.

Author
=====

Michael Pratt
[Personal Website](http://www.michael-pratt.com)
