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
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'thatkevinsmith'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertTrue(!empty($output));
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'unknown-user-in-twitter-yes'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
        $this->assertTrue(empty($output));
    }

    /**
     * Test that the service returns something good
     */
    public function testService()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Twitter/1.json'));
        $fb = new \SimpleLifestream\Services\Twitter($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService2()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Twitter/2.json'));
        $fb = new \SimpleLifestream\Services\Twitter($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp('This is not a json string');
        $fb = new \SimpleLifestream\Services\Twitter($http, 'testResource');
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp(json_encode(array('entries', 'bentries')));
        $fb = new \SimpleLifestream\Services\Twitter($http, 'testResource');
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer3()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Twitter/3.json'));
        $fb = new \SimpleLifestream\Services\Twitter($http, 'testResource');
        $fb->getApiData();
    }
}
?>
