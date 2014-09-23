<?php
/**
 * TestServiceStackExchange.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceStackExchange extends TestService
{
    protected $validTypes = array(
        'commented',
        'answered',
        'badge',
        'accepted',
        'asked',
    );

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        $stream = $this->getStream('StackOverflow', '430087');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('StackOverflow', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testFeedRequestFail()
    {
        $stream = $this->getStream('StackOverflow', 'unknown-or-invalid-stackexchange-user');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testService()
    {
        $stream = $this->getStream('StackOverflow', 'dummySample1', 'DD-2013-10-16.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('StackOverflow', $response);
        $this->assertEquals(28, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService2()
    {
        $stream = $this->getStream('StackOverflow', 'dummySample2', 'VonC-2013-10-16.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('StackOverflow', $response);
        $this->assertEquals(27, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testCallback()
    {
        $stream = $this->getStream('StackOverflow', 'dummySample2', 'VonC-2013-10-16.json');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => str_replace(' ', '', $v['title'])
            );
        });

        $response = $stream->getResponse();
        $this->assertEquals(27, count($response));
        $this->checkResponseIntegrity('StackOverflow', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('StackOverflow', 'dummyInvalidResourceNotJson', 'this is not a json response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $invalidResponse = json_encode(array('entries', 'bentries'));
        $stream = $this->getStream('StackExchange', 'dummyInvalidResourceNotValidJSON', $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>
