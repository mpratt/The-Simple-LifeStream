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
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://www.michael-pratt.com/blog/rss/'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
    }

    /**
     * Test Atom feed
     */
    public function testRealRequestAtom()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://www.michael-pratt/invalid-resource/'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
    }

    /**
     * Test that the service returns something good
     */
    public function testRSSService()
    {
        $fb = new FeedMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Feed-RSS.xml');
        $result = $fb->getApiData();

        $this->assertEquals(8, count($result));
        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testAtomService()
    {
        $fb = new FeedMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Feed-Atom.xml');
        $result = $fb->getApiData();

        $this->assertEquals(50, count($result));
        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $fb = new FeedMock();
        $fb->setResource('testResrouce');
        $fb->reply = 'This is not a xml string';
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $fb = new FeedMock();
        $fb->setResource('testResrouce');
        $fb->reply = '<?xml version="1.0" encoding="utf-8"?><house><item><table name="hi">hola</table></item></house>';
        $fb->getApiData();
    }
}
?>
