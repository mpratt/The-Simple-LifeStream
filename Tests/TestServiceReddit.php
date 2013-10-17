<?php
/**
 * TestServiceReddit.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceReddit extends TestService
{
    protected $validTypes = array(
        'commented',
        'posted'
    );

    public function testRealRequest()
    {
        $stream = $this->getStream('Reddit', 'mpratt');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Reddit', $response, array('subreddit'));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRequestFail()
    {
        $stream = $this->getStream('Reddit', 'unknown-reddit-user-that-doesnt-exists-as-of-now');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testRedditSample1()
    {
        $stream = $this->getStream('Reddit', 'dummySample1', 'dafaqau-2013-10-15.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Reddit', $response, array('subreddit'));

        // It should be 100, but two titles had the string '0', evaluating them as false
        $this->assertEquals(98, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRedditSample2()
    {
        $stream = $this->getStream('Reddit', 'dummySample2', 'hellolizzie-2013-10-15.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Reddit', $response, array('subreddit'));
        $this->assertEquals(100, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRedditSample3()
    {
        $stream = $this->getStream('Reddit', 'dummySample3', 'lepotaters-2013-10-15.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Reddit', $response, array('subreddit'));
        $this->assertEquals(80, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Reddit', 'dummyInvalidResourceNotJson', 'this is not a json response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $invalidResponse = json_encode(array('entries', 'bentries'));
        $stream = $this->getStream('Reddit', 'dummyInvalidResourceNotValidJSON', $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>
