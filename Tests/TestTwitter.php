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
    protected $MockData = array(
            'consumer_key'    => 'Mock',
            'consumer_secret' => 'Mock',
            'token'           => 'Mock',
            'token_secret'    => 'Mock',
            'user' => 'Mock'
        );

    /**
     * Test a real request
     */
    public function testRealRequest()
    {
        if (!is_file(__DIR__ . '/TwitterCredentials.php'))
        {
            $this->markTestSkipped('No twitter Credentials Found');
            return ;
        }

        require __DIR__ . '/TwitterCredentials.php';
        $data = array_merge(array('user' => 'HablarMierda'), $oauth);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertTrue(!empty($output));
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $data = array(
            'consumer_key'    => '',
            'consumer_secret' => '',
            'token'           => '',
            'token_secret'    => '',
            'user' => 'Unknown'
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data), array('cache' => false));
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
        $fb = new \SimpleLifestream\Services\Twitter($http, $this->MockData);
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService2()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Twitter/2.json'));
        $fb = new \SimpleLifestream\Services\Twitter($http, $this->MockData);
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testInvalidInput()
    {
        $this->setExpectedException('InvalidArgumentException');

        $http = new MockHttp('This is not a json string');
        $fb = new \SimpleLifestream\Services\Twitter($http, 'not an array');
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp('This is not a json string');
        $fb = new \SimpleLifestream\Services\Twitter($http, $this->MockData);
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp(null);
        $fb = new \SimpleLifestream\Services\Twitter($http, $this->MockData);
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer3()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Twitter/3.json'));
        $fb = new \SimpleLifestream\Services\Twitter($http, $this->MockData);
        $fb->getApiData();
    }
}
?>
