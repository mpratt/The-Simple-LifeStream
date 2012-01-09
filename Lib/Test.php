<?php
/**
 * Test.php
 * This file is for testing purposes
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

if (function_exists('date_default_timezone_set'))
    date_default_timezone_set('America/Bogota');

function SimpleLifestreamOutput($m, $die = false) { echo "${m} \n"; if ($die) die('Fatal Error!'); }

require(dirname(__FILE__) . '/SimpleLifestream.php');
SimpleLifestreamOutput('Testing The Simple Life(stream) Library');

if (!function_exists('curl_init'))
    SimpleLifestreamOutput('Need to have CURL',true);

if (!function_exists('json_decode'))
    SimpleLifestreamOutput('Need to have JSON', true);

if (!function_exists('utf8_decode'))
    SimpleLifestreamOutput('Need to have UTF8 support', true);

if (!function_exists('simplexml_load_string'))
    SimpleLifestreamOutput('Need to have SimpleXML support', true);

$testArray = array('Github'  => array('Github' => array('username' => 'mpratt')),
                   'Twitter' => array('Twitter' => array('username' => 'parishilton')),
                   'Youtube' => array('Youtube' => array('username' => 'mtppratt')),
                   'StackOverflow' => array('StackOverflow' => array('username' => '430087')),
                   'FacebookPages' => array('FacebookPages' => array('username' => '27469195051')),
                   'RSS Feed Test'  => array('Atom' => array('url' => 'http://www.michael-pratt.com/blog/rss/')),
                   'ATOM Feed Test' => array('Atom' => array('url' => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom')));


SimpleLifestreamOutput(' ');
foreach($testArray as $title => $config)
{
    SimpleLifestreamOutput('Testing ' . $title);

    $lifestream = new SimpleLifestream($config);
    $output = $lifestream->getLifestream();

    if ($lifestream->hasErrors())
    {
        foreach ($lifestream->getErrors() as $e)
            SimpleLifestreamOutput('****  - ' . $e);

        die('*** Test Failed ***');
    }
    else if (!is_array($output))
        SimpleLifestreamOutput('**** Wrong format returned', true);
    else if (empty($output))
    {
        SimpleLifestreamOutput('** Warning: The output is empty -- skiping test!');
        continue ;
    }
    else if (empty($output['0']['service']) || $output['0']['service'] != strtolower($title))
        SimpleLifestreamOutput('** Warning: Wrong Servicename | Gotten: "' . $output['0']['service'] . '" | Expected: "' . strtolower($title) . '"');

    SimpleLifestreamOutput('Validating ' . $title . ' output');
    foreach ($output as $k => $o)
    {
        if (empty($o['html']) || !is_string($o['html']))
            SimpleLifestreamOutput('**** Html key number ' . $k . ' is in the wrong format', true);
        else if (empty($o['date']) || !is_numeric($o['date']) || $o['date'] < 0 || strlen($o['date']) < 10)
            SimpleLifestreamOutput('**** Date key number ' . $k . ' is in the wrong format', true);
        else if (count($o) != count($o, 1))
            SimpleLifestreamOutput('** Warning: Multidimensional array returned');
    }

    unset($lifestream);
    SimpleLifestreamOutput($title . ' Test Passed!');
    SimpleLifestreamOutput(' ');
}

?>