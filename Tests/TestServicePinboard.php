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

    public function testRealRequest()
    {
        if (!is_file(__DIR__ . '/AuthCredentials.php'))
        {
            $this->markTestSkipped('No Pinboard Credentials Found');
            return ;
        }

        if (!ini_get('allow_url_fopen'))
        {
            $this->markTestIncomplete(
                'Could not test Pinboard with file_get_contents, allow_url_fopen is closed'
            );
            return ;
        }

        require __DIR__ . '/AuthCredentials.php';

        if (empty($pinboardToken))
        {
            $this->markTestIncomplete(
                'Pinboard credentials are not provided in AuthCredentials.php'
            );
            return ;
        }

        $stream = $this->getStream('Pinboard', $pinboardToken);
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Pinboard', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRequestFail()
    {
        $stream = $this->getStream('Pinboard', 'invalid-pinboard-token');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testService()
    {
        $stream = $this->getStream('Pinboard', 'dummySample', 'vicg4rcia-2014-09-17');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Pinboard', $response);

        $this->assertEquals(15, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Pinboard', 'dummyInvalidResponse', 'an invalid response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>

