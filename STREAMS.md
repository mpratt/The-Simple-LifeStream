Supported Sites
===============
More detailed information about each stream and instantiation instructions, are found
on this file.

### DailyMotion
Finds uploaded videos.
Requires: The User name

```php
    new \SimpleLifestream\Stream('DailyMotion', 'Movieline');
```

### Delicious
Finds bookmarked urls
Requires: The User name

```php
    new \SimpleLifestream\Stream('Delicious', 'andrei.z');
```

### Deviantart
Finds posted content
Requires: The User name

```php
    new \SimpleLifestream\Stream('Deviantart', 'jon-lock');
```

### Dribble
Finds posted content
Requires: The User name

```php
    new \SimpleLifestream\Stream('Dribble', 'focuslab');
```

### FacebookPages
Remember that a Facebook Page is different from a profile Page.
The page id is required, if you dont know how to find it, look at [this stackoverflow question](http://stackoverflow.com/questions/3130433/get-facebook-fan-page-id).

```php
    new \SimpleLifestream\Stream('FacebookPages', '140262999339534');
```

### Atom/RSS Feeds
Finds the latest titles/entries on a RSS/Atom Feed.
Requires: The url to the xml resource

```php
    new \SimpleLifestream\Stream('Feed', 'http://feeds.eltiempo.com/eltiempo/titulares');
```

### Github
Finds create, push and pull requests events on a repo. Finds starred repos, folled users and issues created.
Requires: The github username.

```php
    new \SimpleLifestream\Stream('Github', 'mpratt');
```

### Reddit
Finds links submitted and comments on threads.
Requires: The Reddit username.

```php
    new \SimpleLifestream\Stream('Reddit', 'mpratt');
```
This stream also returns this key:
    - subreddit (name of the subreddit where the acion happened)

### StackExchange/StackOverflow
Finds the recent comments on questions, answers and questions you have written, Reports questions that you have marked as answered.
and finds awarded badges.
Requires: The User Id. If you dont know how to find it, read [this stackexchange thread](http://meta.stackoverflow.com/questions/98771/what-is-my-user-id).

```php
    new \SimpleLifestream\Stream('StackExchange', '430087');
    // OR
    new \SimpleLifestream\Stream('StackOverflow', '430087');
```

This stream also supports other stack exchange sites, by giving an array with the `site` name
and the respective user id.

```php
    new \SimpleLifestream\Stream('StackExchange', array(
        'site' => 'programmers',
        'resource' => '1204',
    ));
```

### Twitter
Important! You have to [register an app](http://dev.twitter.com/apps) first.
Finds The latests Tweets.
Requires: The username and oauth tokens/data from the created app.

```php
    new \SimpleLifestream\Stream('twitter', array(
        'consumer_key'    => 'the consumer key',
        'consumer_secret' => 'the consumer secret',
        'access_token' => 'you access token',
        'access_token_secret' => 'the access token secret',
        'resource' => 'the twitter username',
    ));
```

### Youtube
Finds all the videos added to the favorite playlist.
Requires: The Youtube Username or user Id.

```php
    new \SimpleLifestream\Stream('Youtube', 'mtppratt');
```

This stream also returns this keys:
    - username (The current user name)
    - thumbnail (Thumbnail for the video)
