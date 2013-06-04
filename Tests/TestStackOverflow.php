<?php
/**
 * TestStackOverflow.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestStackOverflow extends PHPUnit_Framework_TestCase
{
    protected $knownTypes = array(
        'badgeWon',
        'commented',
        'accepted',
        'Answer',
        'asked',
        'answered',
    );

    /**
     * Test a real request
     */
    public function testRealRequest()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('StackOverflow' => '430087'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertTrue(!empty($output));
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('StackOverflow' => 'unknown-so-user'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
        $this->assertTrue(empty($output));
    }

    /**
     * Test that the service returns something good
     */
    public function testService()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/StackOverflow/1.json'));
        $fb = new \SimpleLifestream\Services\StackOverflow($http, 'testResource');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($this, $result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService2()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/StackOverflow/2.json'));
        $fb = new \SimpleLifestream\Services\StackOverflow($http, 'testResource');
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
        $fb = new \SimpleLifestream\Services\StackOverflow($http, 'testResource');
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        $this->setExpectedException('Exception');

        $http = new MockHttp('This is not a json string');
        $fb = new \SimpleLifestream\Services\StackOverflow($http, 'testResource');
        $fb->getApiData();
    }
}
?>
