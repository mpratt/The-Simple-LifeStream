<?php
/**
 * TestServiceGithub.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceGithub extends TestService
{
    protected $validTypes = array(
        'repo-released',
        'repo-created',
        'repo-pushed',
        'repo-pull-opened',
        'repo-issue-created',
        'starred',
        'followed',
    );

    public function testRealRequest()
    {
        $stream = $this->getStream('Github', 'mpratt');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testFeedRequestFail()
    {
        $stream = $this->getStream('Github', 'unknown-or-invalid-github-user---');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testService()
    {
        $stream = $this->getStream('Github', 'dummySample1', 'symfony-2013-10-16.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $this->assertEquals(22, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService2()
    {
        $stream = $this->getStream('Github', 'dummySample2', 'alganet-2013-10-16.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $this->assertEquals(25, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService3()
    {
        $stream = $this->getStream('Github', 'dummySample3', 'events-2013-10-16.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $this->assertEquals(26, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Github', 'dummyInvalidResourceNotJson', 'this is not a json response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>
