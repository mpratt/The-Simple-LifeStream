<?php
/**
 * TestDribble.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestDribble extends TestService
{
    protected $validTypes = array('posted');

    /**
     * This needs more execution time ..
     * @large
     */
    public function testRealRequest()
    {
        $stream = $this->getStream('Dribble', 'Creativedash');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('Dribble', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testRequestFail()
    {
        $stream = $this->getStream('Dribble', 'an-invalid-or-unknown-user-here');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testSample1()
    {
        $stream = $this->getStream('Dribble', 'dummySample1', 'tron-2013-10-17.json');
        $response = $stream->getResponse();

        $this->assertEquals(15, count($response));
        $this->checkResponseIntegrity('Dribble', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testSample2()
    {
        $stream = $this->getStream('Dribble', 'dummySample2', 'deefile-2013-10-17.json');
        $response = $stream->getResponse();

        $this->assertEquals(15, count($response));
        $this->checkResponseIntegrity('Dribble', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testCallback()
    {
        $stream = $this->getStream('Dribble', 'dummySample1', 'tron-2013-10-17.json');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => str_replace(' ', '', $v['title'])
            );
        });

        $response = $stream->getResponse();
        $this->assertEquals(15, count($response));
        $this->checkResponseIntegrity('Dribble', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
}
?>
