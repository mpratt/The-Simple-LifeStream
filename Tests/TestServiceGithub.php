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
        'repo-created-branch',
        'repo-pushed',
        'repo-pull-opened',
        'repo-pull-closed',
        'repo-pull-reopened',
        'repo-issue-created', // deprecated
        'repo-issue-opened',
        'repo-issue-closed',
        'repo-issue-commented',
        'repo-fork-created',
        'starred',
        'followed',
    );

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        $stream = $this->getStream('Github', 'tarruda');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    /**
     * This needs more execution time ..
     * @large
     */
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

        $this->assertEquals(29, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService2()
    {
        $stream = $this->getStream('Github', 'dummySample2', 'alganet-2013-10-16.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $this->assertEquals(27, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }


    public function testService3()
    {
        $stream = $this->getStream('Github', 'dummySample3', 'events-2013-10-16.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $this->assertEquals(27, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService4()
    {
        $stream = $this->getStream('Github', 'dummySample4', 'robbytaylor-2014-03-21.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $this->assertEquals(28, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService5()
    {
        $stream = $this->getStream('Github', 'dummySample5', 'mpratt-2014-03-21.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Github', $response);

        $this->assertEquals(28, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testCallback()
    {
        $stream = $this->getStream('Github', 'dummySample5', 'mpratt-2014-03-21.json');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => str_replace(' ', '', $v['type'])
            );
        });

        $response = $stream->getResponse();
        $this->assertEquals(28, count($response));
        $this->checkResponseIntegrity('Github', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));

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
