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

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        if (!ini_get('allow_url_fopen'))
        {
            $this->markTestIncomplete(
                'Could not test Pinboard with file_get_contents, allow_url_fopen is closed'
            );
            return ;
        }

        if (!is_file(__DIR__ . '/AuthCredentials.php'))
        {
            $this->markTestSkipped('No Pinboard Credentials Found');
            return ;
        }

        $auth = require __DIR__ . '/AuthCredentials.php';
        if (!isset($auth['pinboard_token'])) {
            $this->markTestSkipped('No Pinboard Credentials Found');
            return ;
        }

        $stream = $this->getStream('Pinboard', $auth['pinboard_token']);
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Pinboard', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    /**
     * This needs more execution time ..
     * @large
     */
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

    public function testCallback()
    {
        $stream = $this->getStream('Pinboard', 'dummySample', 'vicg4rcia-2014-09-17');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => str_replace(' ', '', $v['description'])
            );
        });

        $response = $stream->getResponse();
        $this->assertEquals(15, count($response));
        $this->checkResponseIntegrity('Pinboard', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));

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

