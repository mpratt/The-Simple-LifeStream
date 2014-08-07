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

    /**
     * This needs more execution time (test with fopen)
     * @large
     */
    public function testRealRequest()
    {
        if (!is_file(__DIR__ . '/AuthCredentials.php'))
        {
            $this->markTestSkipped('No twitter Credentials Found');
            return ;
        }

        if (!ini_get('allow_url_fopen'))
        {
            $this->markTestIncomplete('Could not test twitter with file_get_contents, allow_url_fopen is closed');
            return ;
        }

        require __DIR__ . '/AuthCredentials.php';
        $data = array_merge(array('resource' => 'HablarMierda'), $twitterOauth);

        $stream = $this->getStream('Twitter', $data, null, array('prefer_curl' => false));
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Twitter', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }


    /**
     * This needs more execution time .. (Test with curl)
     * @large
     */
    public function testRealRequest1()
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
        if (version_compare(PHP_VERSION, '5.4.0', '<') || defined('HHVM_VERSION'))
        {
            $this->markTestSkipped('Weird Bug on travis version 5.3 and hhvm');
            return ;
        }

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
}
?>
