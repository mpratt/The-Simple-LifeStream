<?php
/**
 * Autoload.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

date_default_timezone_set('UTC');

if (file_exists(__DIR__ . '/../vendor/autoload.php'))
    require __DIR__ . '/../vendor/autoload.php';
else
    require __DIR__ . '/../Lib/SimpleLifestream/Autoload.php';

/**
 * Checks that service provider returns consistent data
 *
 * @param object $testObject
 * @param array $result
 * @param array $types
 * @param array $additional
 * @return bool
 *
 * @throws InvalidArgumentException when an inconsistency is found.
 */
function checkServiceKeys($test, array $result, array $types, array $additional = array())
{
    $services = array('facebookpages',
                      'feed',
                      'github',
                      'reddit',
                      'stackoverflow',
                      'twitter',
                      'youtube');

    foreach ($result as $r)
    {
        $test->assertTrue(in_array($r['service'], $services), 'Unknown Service ' . $r['service']);
        $test->assertTrue(in_array($r['type'], $types), 'Unknown Type ' . $r['type']);
        $test->assertTrue(!empty($r['resource']), 'The Resource Key shouldnt be empty');
        $test->assertTrue(is_numeric($r['stamp']), 'The stamp seems to be invalid ' . $r['stamp']);
        $test->assertTrue(strlen($r['stamp']) >= 10, 'The stamp seems to be invalid ' . $r['stamp']);
        $test->assertTrue(!empty($r['text']), 'The Text key shouldnt be empty');

        $url = parse_url($r['url']);
        $test->assertTrue(!empty($url['host']), 'The Url seems to be invalid ' . $r['url']);

        if (!empty($additional))
        {
            $test->assertTrue(is_array($r['additional']), 'The additional key should be an array');

            foreach ($additional as $a)
                $test->assertTrue(!empty($r['additional'][$a]), 'The Additional, key should have an array with a key named ' . $a);
        }
    }

    return true;
}

/**
 * Testing Mocks
 */
class SimpleLifestreamMock extends \SimpleLifestream\SimpleLifestream { public $services = array(); }

class CacheMock extends \SimpleLifestream\Cache\File
{
    public function __construct(array $config = array()){ $this->config = $config; }
    public function store($key, $data) { unset($key, $data); return false; }
    public function read($key) { unset($key); return false; }
}

class MockHttp extends \SimpleLifestream\HttpRequest
{
    protected $reply;
    public function __construct($reply) { $this->reply = $reply; }
    public function fetch($url) { unset($url); return $this->reply; }
}
?>
