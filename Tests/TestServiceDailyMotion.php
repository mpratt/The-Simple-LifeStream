<?php
/**
 * TestServiceDailyMotion.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceDailyMotion extends TestService
{
    protected $validTypes = array('uploaded-video');

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        $stream = $this->getStream('DailyMotion', 'Movieline');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('DailyMotion', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRequestFail()
    {
        $stream = $this->getStream('DailyMotion', 'an-invalid-or-unknown-user-here');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testSample1()
    {
        $stream = $this->getStream('DailyMotion', 'dummySample1', 'HBO-2013-10-17.xml');
        $response = $stream->getResponse();

        $this->assertEquals(20, count($response));
        $this->checkResponseIntegrity('DailyMotion', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testSample2()
    {
        $stream = $this->getStream('DailyMotion', 'dummySample2', '3MinuteUpdate-2013-10-17.xml');
        $response = $stream->getResponse();

        $this->assertEquals(20, count($response));
        $this->checkResponseIntegrity('DailyMotion', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testCallback()
    {
        $stream = $this->getStream('DailyMotion', 'dummySample1', 'HBO-2013-10-17.xml');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => str_replace(' ', '', $v->title)
            );
        });

        $response = $stream->getResponse();
        $this->assertEquals(20, count($response));
        $this->checkResponseIntegrity('DailyMotion', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
}
?>
