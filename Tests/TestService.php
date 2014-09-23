<?php
/**
 * TestService.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestService extends PHPUnit_Framework_TestCase
{
    /** @var array Valid types for the current service */
    protected $validTypes = array();


    public function testInvalidStream()
    {
        $this->setExpectedException('InvalidArgumentException');

        new \SimpleLifestream\Stream('unknown_service', 'resource');
    }

    /**
     * Shorthand method for stream instantiation.
     *
     * @param string $provider
     * @param mixed $resource
     * @param string $sampleFile
     * @param array $config
     * @return object
     */
    protected function getStream($provider, $resource, $sampleFile = '', $config = array())
    {
        $stream = new \SimpleLifestream\Stream($provider, $resource);
        if (!empty($sampleFile))
        {
            $file = __DIR__ . '/Samples/' . $provider . '/' . $sampleFile;
            if (is_file($file))
                $http = new MockHttp(file_get_contents($file));
            else
                $http = new MockHttp($sampleFile);
        }
        else
            $http = new \SimpleLifestream\HttpRequest($config);

        $stream->registerHttpConsumer($http);
        return $stream;
    }

    /**
     * Checks that the provider returns consistent data
     *
     * @param string $provider The Name of the Provider
     * @param array $result
     * @param array $types
     * @param array $additional
     * @return void
     *
     * @throws InvalidArgumentException when an inconsistency is found.
     */
    protected function checkResponseIntegrity($provider, array $result, array $additional = array())
    {
        if (!is_array($result)) {
            throw new \Exception('Test - ' . $provider . ': has an invalid response (not an array)');
        } else if (empty($result)) {
            throw new \Exception('Test - ' . $provider . ': has an empty response!');
        }

        foreach ($result as $r) {

            $this->assertEquals($r['service'], strtolower($provider), 'The service key must be ' . strtolower($provider));
            $this->assertTrue(in_array($r['type'], $this->validTypes), 'Unknown Type ' . $r['type']);
            $this->assertTrue(!empty($r['resource']), 'The Resource Key shouldnt be empty');
            $this->assertTrue((is_numeric($r['stamp']) && strlen($r['stamp']) >= 10), 'The stamp seems to be invalid ' . $r['stamp']);
            $this->assertTrue(!empty($r['text']), 'The Text key shouldnt be empty on type ' . $r['type']);


            $url = parse_url($r['url']);
            $this->assertTrue(!empty($url['host']), 'The Url seems to be invalid ' . $r['url']);

            foreach ($additional as $a) {
                $this->assertTrue(!empty($r[$a]), 'The response should have a key named ' . $a . ' - - ' . print_r($r, true));
            }
        }
    }
}
?>
