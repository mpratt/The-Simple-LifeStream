<?php
/**
 * TestServicePinboard.php
 *
 * @author  Vic Garcia <vic.garcia@outlook.com>
 * @link    http://vicg4rcia.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServicePinboard extends TestService
{
    protected $validTypes = array(
        'bookmarked',
    );

    /*
    public function testRealRequest()
    {
        $stream = $this->getStream('Pinboard', '');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Pinboard', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
    */

    public function testRequestFail()
    {
        $stream = $this->getStream('Pinboard', 'unknown-or-invalid-pinboard-user');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    /*
    public function testService()
    {
        $stream = $this->getStream('Pinboard', 'dummySample1', 'vicgarcia-2014-09-16');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Pinboard', $response);

        $this->assertEquals(10, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
    */

    /*
    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Pinboard', 'dummyInvalidResourceNotJson', 'this is not a response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
    */
}
?>

