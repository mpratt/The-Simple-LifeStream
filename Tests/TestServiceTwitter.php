<?php
/**
 * TestServiceTwitter.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceTwitter extends TestService
{
    protected $validTypes = array(
        'tweeted'
    );

    protected $mockData = array(
        'consumer_key' => 'axa',
        'consumer_secret' => 'zxc',
        'access_token' => 'ccc',
        'access_token_secret' => 'xxx',
        'resource' => 'mockuser',
    );

    public function testRealRequest()
    {
        if (!is_file(__DIR__ . '/AuthCredentials.php'))
        {
            $this->markTestSkipped('No twitter Credentials Found');
            return ;
        }

        require __DIR__ . '/AuthCredentials.php';
        $data = array_merge(array('resource' => 'HablarMierda'), $twitterOauth);

        $stream = $this->getStream('Twitter', $data);
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Twitter', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRequestFail()
    {
        $stream = $this->getStream('Twitter', $this->mockData);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testService()
    {
        $stream = $this->getStream('Twitter', $this->mockData, 'KevinSmith-2013-01-25.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Twitter', $response);
        $this->assertEquals(20, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService2()
    {
        $stream = $this->getStream('Twitter', $this->mockData, 'ParisHilton-2013-01-25.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Twitter', $response);
        $this->assertEquals(20, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testService3()
    {
        $stream = $this->getStream('Twitter', $this->mockData, 'NotAllowed-2013-01-25.json');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $invalidResponse = 'this is not a json string';
        $stream = $this->getStream('Twitter', $this->mockData, $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $invalidResponse = json_encode(array('entries', 'bentries'));
        $stream = $this->getStream('Twitter', $this->mockData, $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>
