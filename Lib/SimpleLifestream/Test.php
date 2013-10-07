<?php
error_reporting(E_ALL);
date_default_timezone_set('UTC');
require 'Autoload.php';

$streams = array(
    new \SimpleLifestream\Stream('twitter', array(
        'consumer_key'    => 'FggmEZeBJqtQyQjLgpkslg',
        'consumer_secret' => 'Au3hFdnC15D5JEWya3hysvu8sBLZcV4OkpGMJadvI',
        'token'           => '57087253-lctDbedsc02wtpruEEnQhTB9r8buDbZDF4C2Bh2Zh',
        'token_secret'    => 'IbX3uPd0WlQO10GyyAaZIho23ZVpZ28bXjfAtfnGAg',
        'resource' => 'HablarMierda',
    ))
);

$lifestream = new \SimpleLifestream\SimpleLifestream(array('cache_ttl' => 1));
$output = $lifestream->loadStreams($streams)->getLifestream(1);
print_r($output);
echo $lifestream->getLastError();
