<?php
/**
 * TestServiceDelicious.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceDelicious extends TestService
{
    protected $validTypes = array(
        'bookmarked',
    );

    public function testRealRequest()
    {
        $stream = $this->getStream('Delicious', 'rkmurali');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Delicious', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testFeedRequestFail()
    {
        $stream = $this->getStream('Delicious', 'unknown-or-invalid-delicious-user---');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testService()
    {
        $stream = $this->getStream('Delicious', 'dummySample1', 'andrei-2013-10-17.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Delicious', $response);

        $this->assertEquals(10, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService2()
    {
        $stream = $this->getStream('Delicious', 'dummySample2', 'ashleynolan-2013-10-17.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Delicious', $response);

        $this->assertEquals(10, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Delicious', 'dummyInvalidResourceNotJson', 'this is not a json response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>
