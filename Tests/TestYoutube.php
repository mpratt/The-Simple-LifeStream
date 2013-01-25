<?php
/**
 * TestYoutube.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestYoutube extends PHPUnit_Framework_TestCase
{
    protected $knownTypes = array('favorited');

    /**
     * Test a real request
     */
    public function testRealRequest()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'unknown-user-in-youtube-yes'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
    }

    /**
     * Test that the service returns something good
     */
    public function testService()
    {
        $fb = new YoutubeMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Youtube-mpratt.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService2()
    {
        $fb = new YoutubeMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Youtube-cool.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $fb = new YoutubeMock();
        $fb->setResource('testResrouce');
        $fb->reply = 'This is not a json string';
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $fb = new YoutubeMock();
        $fb->setResource('testResrouce');
        $fb->reply = json_encode(array('entries', 'bentries'));
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer3()
    {
        $this->setExpectedException('Exception');

        $fb = new YoutubeMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Youtube-private.json');
        $fb->getApiData();
    }
}
?>
