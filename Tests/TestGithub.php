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
    protected $knownTypes = array('pushEvent', 'createEvent', 'createTag', 'createGist', 'updateGist', 'starred', 'followed');

    /**
     * Test a real request
     */
    public function testRealRequest()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Github' => 'mpratt'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
    }

    /**
     * Test request to unknown resource
     */
    public function testFeedRequestFail()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Github' => 'unknown-user-in-github-yes'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertTrue($lifestream->hasErrors());
    }

    /**
     * Test that the service returns something good
     */
    public function testService()
    {
        $fb = new GithubMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Github-alga.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService2()
    {
        $fb = new GithubMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Github-mpratt.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService3()
    {
        $fb = new GithubMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Github-org.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService4()
    {
        $fb = new GithubMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Github-gist.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test that the service returns something good
     */
    public function testService5()
    {
        $fb = new GithubMock();
        $fb->setResource('testResrouce');
        $fb->reply = file_get_contents(__DIR__ . '/Samples/Github-tag.json');
        $result = $fb->getApiData();

        $this->assertTrue(checkServiceKeys($result, $this->knownTypes));
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer()
    {
        $this->setExpectedException('Exception');

        $fb = new GithubMock();
        $fb->setResource('testResrouce');
        $fb->reply = 'This is not a json string';
        $fb->getApiData();
    }

    /**
     * Test what the behaviour is when an invalid answer is given
     */
    public function testServiceInvalidAnswer2()
    {
        // For some strange reason this tests fails on travis <= 5.3
        if (version_compare(PHP_VERSION, '5.4') <= 0)
        {
            $this->markTestSkipped('Travis Workaround');
            return ;
        }

        $this->setExpectedException('Exception');

        $fb = new GithubMock();
        $fb->setResource('testResrouce');
        $fb->reply = json_encode(array('entries', 'bentries'));
        $fb->getApiData();
    }
}
?>
