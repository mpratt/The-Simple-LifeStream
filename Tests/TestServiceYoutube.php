<?php
/**
 * TestServiceYoutube.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceYoutube extends TestService
{
    protected $validTypes = array(
        'favorited'
    );

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        $stream = $this->getStream('Youtube', 'mtppratt');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Youtube', $response, array('username', 'thumbnail'));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testFeedRequestFail()
    {
        $stream = $this->getStream('Youtube', 'unknown-or-invalid-youtube-user');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testService()
    {
        $stream = $this->getStream('Youtube', 'dummySample1', 'mtppratt-2013-01-24.json');
        $response = $stream->getResponse();
        $this->checkResponseIntegrity('Youtube', $response, array('username', 'thumbnail'));

        $this->assertEquals(25, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService2()
    {
        $stream = $this->getStream('Youtube', 'dummySample2', 'imthatcoolguy-2013-01-24.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Youtube', $response, array('username', 'thumbnail'));

        $this->assertEquals(25, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testCallback()
    {
        $stream = $this->getStream('Youtube', 'dummySample2', 'imthatcoolguy-2013-01-24.json');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => str_replace(' ', '', $v['video']['title'])
            );
        });

        $response = $stream->getResponse();
        $this->assertEquals(25, count($response));
        $this->checkResponseIntegrity('Youtube', $response, array('modified_title', 'username', 'thumbnail'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Youtube', 'dummyInvalidResourceNotJson', 'this is not a json response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $invalidResponse = json_encode(array('entries', 'bentries'));
        $stream = $this->getStream('Youtube', 'dummyInvalidResourceNotValidJSON', $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer3()
    {
        $stream = $this->getStream('Youtube', 'dummyNotAllowed', 'notallowed-2013-01-24.json');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>
