<?php
/**
 * TestFacebookPages.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestFacebookPages extends PHPUnit_Framework_TestCase
{
    protected $knownTypes = array('link');

    /**
     * Test Facebook Pages Doing a real Request
     */
    public function testFacebookPagesRequest()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('FacebookPages' => '27469195051'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
    }

    /**
     * Test Facebook Pages Doing a real Request
     * to a unknown/invalid facebook page
     */
    public function testFacebookPagesRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('FacebookPages' => 'sdfsdfoisdfh08h4t0284th0ewoubfosdbjk'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
    }

    /**
     * Test that the service returns something good
     */
    public function testCocaColaService()
    {
        $fb = new FacebookPagesMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/FacebookPages-coca-cola.json');
        $result = $fb->getApiData();

        $this->assertEquals(26, count($result));
        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testTwinkies()
    {
        $fb = new FacebookPagesMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/FacebookPages-Twinkies.json');
        $result = $fb->getApiData();

        $this->assertEquals(3, count($result));
        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $fb = new FacebookPagesMock();
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

        $fb = new FacebookPagesMock();
        $fb->setResource('testResrouce');
        $fb->reply = json_encode(array('hello' => 'test', 'world' => array('universe', 'answer')));
        $fb->getApiData();
    }
}
?>
