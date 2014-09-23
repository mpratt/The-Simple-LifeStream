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
        'took-picture',
        'uploaded-video'
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
        if (!is_file(__DIR__ . '/AuthCredentials.php')) {
            $this->markTestSkipped('No instagram Credentials Found');
            return ;
        }

        $auth = require __DIR__ . '/AuthCredentials.php';
        if (!isset($auth['instagram'])) {
            $this->markTestSkipped('No instagram Credentials Found');
            return ;
        }

        $stream = $this->getStream('Instagram', array('resource'=>'41864127','client_id'=> $auth['instagram']));
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

    public function testCallback()
    {
        $stream = $this->getStream('Instagram', $this->mockData, 'AnnaFaithXoXo-2014-09-10.json');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => str_replace('_', '', $v['id'])
            );
        });

        $response = $stream->getResponse();
        $this->assertEquals(20, count($response));
        $this->checkResponseIntegrity('Instagram', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], '_') === false));

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
