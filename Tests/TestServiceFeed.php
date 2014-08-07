<?php
/**
 * TestServiceFeed.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceFeed extends TestService
{
    protected $validTypes = array('posted');

    public function setUp()
    {
        if (!function_exists('simplexml_load_string'))
            $this->markTestSkipped('SimpleXml Is needed for the Feed Provider');
    }

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequestRSS()
    {
        $stream = $this->getStream('Feed', 'http://smodcast.com/feed/');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Feed', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequestAtom()
    {
        $stream = $this->getStream('Feed', 'http://feeds.feedburner.com/GDBcode');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Feed', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    /**
     * This needs more execution time ..
     * @large
     */
    public function testFeedRequestFail()
    {
        $stream = $this->getStream('Feed', 'http://httpbin.org/user-agent');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testRSSSample()
    {
        $stream = $this->getStream('Feed', 'dummyRSSResource', 'RSS.xml');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Feed', $response);
        $this->assertEquals(8, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testAtomSample()
    {
        $stream = $this->getStream('Feed', 'dummyAtomResource', 'Atom.xml');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Feed', $response);
        $this->assertEquals(50, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('Feed', 'dummyInvalidResourceNotXML', 'not a xml string');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $string = '<?xml version="1.0" encoding="utf-8"?><house><item><table name="hi">hola</table></item></house>';
        $stream = $this->getStream('Feed', 'dummyInvalidResourceNotValidXML', $string);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testModifyType()
    {
        $config = array(
            'resource' => 'dummyResourceModifyType',
            'type' => 'other-type'
        );

        $this->validTypes = array('other-type');

        $stream = $this->getStream('Feed', $config, 'RSS.xml');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Feed', $response);
        $this->assertEquals(8, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
}
?>
