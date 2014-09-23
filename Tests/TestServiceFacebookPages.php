<?php
/**
 * TestServiceFacebookPages.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceFacebookPages extends TestService
{
    /** inline {@inheritdoc} */
    protected $validTypes = array('link');

    /**
     * This needs more execution time ..
     * @large
     */
    public function testFacebookPagesRequest()
    {
        $stream = $this->getStream('FacebookPages', '27469195051');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('FacebookPages', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testFacebookPagesRequestFail()
    {
        $stream = $this->getStream('FacebookPages', 'an-invalid-id-here');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testSample1()
    {
        $stream = $this->getStream('FacebookPages', 'Twinkies', 'Twinkies-2013-10-15.json');
        $response = $stream->getResponse();

        $this->assertEquals(5, count($response));
        $this->checkResponseIntegrity('FacebookPages', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testTruncate()
    {
        $data = array(
            'resource' => 'Twinkies',
            'content_length' => 1,
            'content_delimiter' => '-',
        );

        $stream = $this->getStream('FacebookPages', $data, 'Twinkies-2013-10-15.json');
        $response = $stream->getResponse();

        foreach ($response as $r) {
            $this->assertTrue(isset($r['text']));
            $this->assertTrue(strlen($r['text']) === 2); // length + delimiter
            $this->assertTrue((bool) preg_match('~-$~', $r['text']));
        }
    }

    public function testSample2()
    {
        $stream = $this->getStream('FacebookPages', 'CocaCola', 'CocaCola-2013-01-24.json');
        $response = $stream->getResponse();

        $this->assertEquals(26, count($response));
        $this->checkResponseIntegrity('FacebookPages', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('FacebookPages', 'dummyInvalidResourceNotJson', 'not a json response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $invalidResponse = json_encode(array('hello' => 'test', 'world' => array('universe', 'answer')));
        $stream = $this->getStream('FacebookPages', 'dummyInvalidResourceNotValidJSON', $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testCallback()
    {
        $stream = $this->getStream('FacebookPages', 'CocaCola', 'CocaCola-2013-01-24.json');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => 'custom-title-' . str_replace(' ', '', $v['title'])
            );
        });

        $response = $stream->getResponse();
        $this->checkResponseIntegrity('FacebookPages', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));
        $this->assertEquals(26, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
}
?>
