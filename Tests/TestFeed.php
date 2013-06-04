<?php
/**
 * TestFeed.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestFeed extends PHPUnit_Framework_TestCase
{
    protected $knownTypes = array('posted');

    /**
     * Test the dependencies needed to run this service provider
     */
    public function testFeedDependencies()
    {
        $this->assertTrue(function_exists('simplexml_load_string'));
    }

    /**
     * Test RSS feed
     */
    public function testRealRequestRSS()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://smodcast.com/feed/'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertTrue(!empty($output));
    }

    /**
     * Test Atom feed
     */
    public function testRealRequestAtom()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://feeds.feedburner.com/GDBcode'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertTrue(!empty($output));
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://192.168.0.40/invalid-resource/'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
        $this->assertTrue(empty($output));
    }

    /**
     * Test that the service returns something good
     */
    public function testRSSService()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Feed/RSS.xml'));
        $fb = new \SimpleLifestream\Services\Feed($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertEquals(8, count($result));
        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testAtomService()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Feed/Atom.xml'));
        $fb = new \SimpleLifestream\Services\Feed($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertEquals(50, count($result));
        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp('This is not a xml string');
        $fb = new \SimpleLifestream\Services\Feed($http, 'testResource');
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp('<?xml version="1.0" encoding="utf-8"?><house><item><table name="hi">hola</table></item></house>');
        $fb = new \SimpleLifestream\Services\Feed($http, 'testResource');
        $fb->getApiData();
    }
}
?>
