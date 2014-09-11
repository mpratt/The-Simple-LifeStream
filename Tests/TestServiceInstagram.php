<?php
/**
 * TestServiceInstagram.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceInstagram extends TestService
{
    protected $validTypes = array(
        'video',
        'image'
    );

    protected $mockData = array(
        'client_id' => 'abcdef123456789',
        'resource' => '1234',
        'count' => 20
    );

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        if (!is_file(__DIR__ . '/AuthCredentials.php'))
        {
            $this->markTestSkipped('No instagram Credentials Found');
            return ;
        }
        require __DIR__ . '/AuthCredentials.php';

        $stream = $this->getStream('Instagram', ['resource'=>'41864127','client_id'=>$instagramClientId]);
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Instagram', $response, array('username', 'thumbnail'));
        $this->assertEquals(15, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }


    public function testService()
    {
        $stream = $this->getStream('Instagram', $this->mockData, 'AnnaFaithXoXo-2014-09-10.json');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Instagram', $response, array('username', 'thumbnail'));
        $this->assertEquals(20, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidUser()
    {
        $stream = $this->getStream('Instagram', $this->mockData, 'InvalidUser-2014-09-10.json');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidClientId()
    {
        $stream = $this->getStream('Instagram', $this->mockData, 'InvalidClientId-2014-09-10.json');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Instagram', $this->mockData, 'this is not a json response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $invalidResponse = json_encode(array('entries', 'bentries'));
        $stream = $this->getStream('Instagram', $this->mockData, $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }
}
?>
