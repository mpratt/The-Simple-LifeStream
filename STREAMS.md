Supported Sites
===============

More detailed information about each stream and instantiation instructions, are found
on this file.

### DailyMotion

- **Requires**: The user name.
- **Finds**:
    - Uploaded videos

```php
    new \SimpleLifestream\Stream('DailyMotion', 'Movieline');
```

### Delicious

- **Requires**: The user name.
- **Finds**:
    - Bookmarked URLs

```php
    new \SimpleLifestream\Stream('Delicious', 'andrei.z');
```

### Deviantart

- **Requires**: The user name.
- **Finds**:
    - Posted Content

```php
    new \SimpleLifestream\Stream('Deviantart', 'jon-lock');
```

### Dribble

- **Requires**: The user name.
- **Finds**:
    - Posted Content

```php
    new \SimpleLifestream\Stream('Dribble', 'focuslab');
```

### FacebookPages

**Remember:** A Facebook Page is different from a profile Page.
- **Requires**: The page id. If you dont know how to find it, look at [this stackoverflow question](http://stackoverflow.com/questions/3130433/get-facebook-fan-page-id)
- **Finds**:
    - Posted Content

```php
    new \SimpleLifestream\Stream('FacebookPages', '140262999339534');
```

When the text content is longer than 80 chars, the FacebookPages provider, truncates the result. You can change the behaviour quite easily

```php
    new \SimpleLifestream\Stream('FacebookPages', array(
       'resource' => '140262999339534',
       'content_length' => 200, // The width of the desired trim
       'content_delimiter' => '...' // String that should be added at the end of the string
    ));
```

### Atom/RSS Feeds

- **Requires**: The URL to the XML resource
- **Finds**:
    - Latest titles/entries on a RSS/Atom Feed.

```php
    new \SimpleLifestream\Stream('Feed', 'http://feeds.eltiempo.com/eltiempo/titulares');
```

This Stream provider can be very versatile. It can be used to fetch posts from Wordpress blogs or Posterous, Flickr photostreams,
LastFm loved tracks or recently played, saved threads from Reddit, liked or uploaded Vimeo videos, and so on.

Basically, if a site has RSS/Atom Feeds available for user actions, you can use this Provider.

This Provider has the option to change its type, so that the string can be translated differently. Its also posible to change the returned service.
Lets grab recently played songs on a lastFM profile, for example

```php
    new \SimpleLifestream\Stream('Feed', array(
        'resource' => 'http://ws.audioscrobbler.com/1.0/user/jhoness_/recenttracks.rss',
        'type' => 'listened', // Optional - It can be used to change the translation of this action
        'service' => 'lastfm', // Optional - It can be used to emulate a new provider
    ));
```

Translation strings can be found at `SimpleLifestream/Languages/*`

### Github

- **Requires**: The Github user name
- **Finds**:
    - Create events (on repositories)
    - Push events (on repositories)
    - Pull requests (on repositories)
    - Forked events (On repositories)
    - Issue interactions
    - Starred Repos
    - Followed Users

```php
    new \SimpleLifestream\Stream('Github', 'mpratt');
```

### Instagram

- **Important**: You _must_ [register an app](http://instagram.com/developer/) first.
- **Requires**: The [user id](http://stackoverflow.com/questions/11796349/instagram-how-to-get-my-user-id-from-username) and the client_id
- **Finds**:
    - Uploaded Images
    - Uploaded Videos

```php
    new \SimpleLifestream\Stream('Instagram', array(
        'resource' => '12312',
        'client_id' => 'abcdef123456789',
        'count' => 20
    ));
```
This stream also returns the following keys:

    - username (The current username)
    - thumbnail (Thumbnail for the resource)

### GimmeBar

- **Requires**: The user name.
- **Finds**:
    - Bookmarked content

```php
    new \SimpleLifestream\Stream('GimmeBar', 'funkatron');
```

### Reddit

- **Requires**: The user name.
- **Finds**:
    - Links/Threads submitted
    - Comments

```php
    new \SimpleLifestream\Stream('Reddit', 'mpratt');
```
This stream also returns this key:
    - subreddit (name of the subreddit where the acion happened)

### StackExchange/StackOverflow

- **Requires**: The user id.  If you dont know how to find it, read [this stackexchange thread](http://meta.stackoverflow.com/questions/98771/what-is-my-user-id).
- **Finds**:
    - Questions
    - Answers (and questions marked as answered)
    - Recent comments
    - Comments
    - Awarded Badges

```php
    // For StackOverflow use
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

- **Important**: You _must_ [register an app](http://dev.twitter.com/apps) first.
- **Requires**:
    - The Twitter user name
    - Oauth tokens/data from the created APP
- **Finds**:
    - Latests Tweets

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

- **Important**: Make sure that your favorite feed is publicly available on the Youtube settings panel.
- **Requires**: The user name or user id.
- **Finds**:
    - Videos added to your `Favorites` playlist

```php
    new \SimpleLifestream\Stream('Youtube', 'mtppratt');
```

- **This stream also returns the following keys**:
    - username (The current user name)
    - thumbnail (Thumbnail for the video)

### Pinboard

- **Important**: You need to have an API Token. You can find yours on the [settings page](https://pinboard.in/settings/password)
- **Requires**: The api-token of the user.
- **Finds**:
    - Bookmarked list of the users most recent posts.

```php
    new \SimpleLifestream\Stream('Pinboard', 'your-api-token');
```
