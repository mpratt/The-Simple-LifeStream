The Simple Life(stream)
=======================
Is a very simple and flexible library for your life-streaming purposes. It supports a bunch of third party services
and makes it easy for you to display all that information in one single place.

The sweet thing about this library is that it only returns an array with all the important data (date and html).
This empowers you to play with that information and display that however you like.

In order to have a decent performance and avoid making too many requests to other sites the library uses internally a
Cache System based on files (file cache). The duration of each cache is 10 minutes by default, however you can modifiy
that behaviour easily, or you can even implement your own cache engine if you like.

The name of this library is inspired by that cheap reality show with Paris Hilton and Nicole Ritchie. Why? Because this
piece of software is sexy and no matter how dumb you are, its meant to be easy to use.

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
- CURL (It is needed to make http requests, however you can implement your own wrapper with "file_get_contents" for example).

Basic Usage
===========

You can start by passing the service name and resources on construction.
```php
    <?php
        require('SimpleLifestream.php');
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Reddit' => 'mpratt',
                                                                   'Github' => 'mpratt'));

        $stream = $lifestream->getLifestream();
        echo '<ul>';
        foreach ($stream as $s)
        {
            echo '<li>' . $s['html'] . '</li>';
        }
        echo '</ul>';
    ?>
```
Or if you prefer you can define each service individually.
```php
    <?php
        require('SimpleLifestream.php');

        $lifestream = new \SimpleLifestream\SimpleLifestream();
        $lifestream->loadService('Twitter', 'parishilton');
        $lifestream->loadService('Twitter', 'ThatKevinSmith');
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
The **getLifestream()** method accepts a number, in that way you can limit the latest information you want to get.
```php
    <?php
        $stream = $lifestream->getLifestream(10);
        echo count($stream); // 10
    ?>
```
You can check for errors with the **hasErrors()** and **getErrors()** methods.
```php
    <?php
        $stream = $lifestream->getLifestream();
        if ($lifestream->hasErrors())
            var_dump($lifestream->getErrors());
    ?>
```
This library also has support for spanish output. You can even write your own translation object if you like
and pass it to the **setLanguage()** method.
```php
    <?php
        $lifestream->setLanguage('Spanish');
        $stream = $lifestream->getLifestream(10);
        var_dump($stream);
    ?>
```
Are there any event types you want to ignore? I got your back!
```php
    <?php
        $lifestream->ignoreType('starred'); // Ignore Github Starred Repos
        $stream = $lifestream->getLifestream();
    ?>
```
You want to use another cache engine? Or disable the cache alltogether?
```php
    <?php
        $lifestream->setCacheEngine(new MyCacheObject()); // Your object must implement the \SimpleLifestream\Interfaces\ICache interface.
        $lifestream->setCacheEngine(null); // Passing null, disables the cache capabilities of the library.
        $stream = $lifestream->getLifestream(40);
    ?>
```
And finally, the **date** key gives by default the timestamp of each event. You can change the format of that date by
using the **setDateFormat()** method
```php
    <?php
        $lifestream->setDateformat('Y-m-d');
        $stream = $lifestream->getLifestream(30);
        var_dump($stream);
    ?>
```
If you want to see more examples of how to use this library take a peek into the Tests directory and view the **TestSimpleLifestream.php** file.
Otherwise inspect the source code of the library, I would say that it has a "decent" english documentation and it should be easy to follow.

Sample Output
=============

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
                    ["date"]=> int(1348070514)
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
                ["date"]=> int(1348027287)
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
                ["date"]=> int(1336249690)
                ["link"]=> string(87) "<a href="http://www.reddit.com/r/PHP/comments/t662j/#c4khrop">Parsing Youtube links</a>"
                ["html"]=> string(103) "commented on "<a href="http://www.reddit.com/r/PHP/comments/t662j/#c4khrop">Parsing Youtube links</a>"."
              }
        }
    ?>

License
=======
MIT
For the full copyright and license information, please view the LICENSE file.

Author
=====

Michael Pratt
[Personal Website](http://www.michael-pratt.com)
