<?php
/**
 * TestTwitter.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestTwitter extends PHPUnit_Framework_TestCase
{
    protected $knownTypes = array('tweeted');

    /**
     * Test a real request
     */
    public function testRealRequest()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'thatkevinsmith'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'unknown-user-in-twitter-yes'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
    }

    /**
     * Test that the service returns something good
     */
    public function testService()
    {
        $fb = new TwitterMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Twitter-kevin.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService2()
    {
        $fb = new TwitterMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Twitter-paris.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $fb = new TwitterMock();
        $fb->setResource('testResrouce');
        $fb->reply = 'This is not a json string';
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        // A Travis Workaround for PHP 5.3
        if (version_compare(PHP_VERSION, '5.4') <= 0)
        {
            $this->markTestSkipped('Travis Workaround');
            return ;
        }

        $this->setExpectedException('Exception');

        $fb = new TwitterMock();
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

        $fb = new TwitterMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Twitter-private.json');
        $fb->getApiData();
    }
}
?>
