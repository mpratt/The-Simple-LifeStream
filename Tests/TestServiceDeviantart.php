<?php
/**
 * TestServiceDeviantart.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceDeviantart extends TestService
{
    protected $validTypes = array('posted');

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        $stream = $this->getStream('Deviantart', 'yuumei');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Deviantart', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRequestFail()
    {
        $stream = $this->getStream('Deviantart', 'an-invalid-or-unknown-user-here');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testSample1()
    {
        $stream = $this->getStream('Deviantart', 'dummySample1', 'twisted-wind-2013-10-17.xml');
        $response = $stream->getResponse();

        $this->assertEquals(60, count($response));
        $this->checkResponseIntegrity('Deviantart', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testSample2()
    {
        $stream = $this->getStream('Deviantart', 'dummySample2', 'jon-lock-2013-10-17.xml');
        $response = $stream->getResponse();

        $this->assertEquals(60, count($response));
        $this->checkResponseIntegrity('Deviantart', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
}
?>
