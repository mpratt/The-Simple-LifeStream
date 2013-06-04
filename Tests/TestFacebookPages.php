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
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('FacebookPages' => '27469195051'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertTrue(!empty($output));
    }

    /**
     * Test Facebook Pages Doing a real Request
     * to a unknown/invalid facebook page
     */
    public function testFacebookPagesRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('FacebookPages' => 'sdfsdfoisdfh08h4t0284th0ewoubfosdbjk'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
        $this->assertTrue(empty($output));
    }

    /**
     * Test that the service returns something good
     */
    public function testSample1()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/FacebookPages/1.json'));
        $fb = new \SimpleLifestream\Services\FacebookPages($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertEquals(26, count($result));
        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testSample2()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/FacebookPages/2.json'));
        $fb = new \SimpleLifestream\Services\FacebookPages($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertEquals(3, count($result));
        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp('This is not a json string');
        $fb = new \SimpleLifestream\Services\FacebookPages($http, 'testResource');
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp(json_encode(array('hello' => 'test', 'world' => array('universe', 'answer'))));
        $fb = new \SimpleLifestream\Services\FacebookPages($http, 'testResource');
        $fb->getApiData();
    }
}
?>
