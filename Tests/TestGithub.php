<?php
/**
 * TestGithub.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestGithub extends PHPUnit_Framework_TestCase
{
    protected $knownTypes = array(
        'pushEvent',
        'createEvent',
        'createTag',
        'createGist',
        'updateGist',
        'starred',
        'followed'
    );

    /**
     * Test a real request
     */
    public function testRealRequest()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Github' => 'mpratt'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertTrue(!empty($output));
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Github' => 'unknown-user-in-github-yes'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $error = $lifestream->getLastError();
        $this->assertTrue($lifestream->hasErrors());
        $this->assertTrue(!empty($error));
        $this->assertTrue(empty($output));
    }

    /**
     * Test that the service returns something good
     */
    public function testService()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Github/1.json'));
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService2()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Github/2.json'));
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService3()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Github/3.json'));
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService4()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Github/4.json'));
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService5()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Github/5.json'));
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp(json_encode(array('entries', 'bentries')));
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $output = $fb->getApiData();

        $this->assertTrue(empty($output));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp(null);
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer3()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp('This is not a json string');
        $fb = new \SimpleLifestream\Services\Github($http, 'testResource');
        $fb->getApiData();
    }
}
?>
