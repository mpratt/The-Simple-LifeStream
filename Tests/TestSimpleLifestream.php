<?php
/**
 * TestSimpleLifestream.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestSimpleLifestream extends PHPUnit_Framework_TestCase
{
    /**
     * This needs more execution time ..
     * @large
     */
    public function testIgnoreTypes()
    {
        if (!is_file(__DIR__ . '/AuthCredentials.php'))
        {
            $this->markTestSkipped('No twitter Credentials Found');
            return ;
        }

        require __DIR__ . '/AuthCredentials.php';
        $twitterData = array_merge(array('resource' => 'HablarMierda'), $twitterOauth);

        $streams = array(
            new \SimpleLifestream\Stream('Twitter', $twitterData),
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('cache_ttl' => 1));
        $lifestream->loadStreams($streams);
        $lifestream->ignore('tweeted');

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->assertEquals(array(), $lifestream->getErrors());
        $this->assertCount(0, $output);

        $streams = array(
            new \SimpleLifestream\Stream('Twitter', $twitterData),
            new \SimpleLifestream\Stream('Youtube', 'mtppratt'),
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('cache_ttl' => 1));
        $lifestream->loadStreams($streams);
        $lifestream->ignore('tweeted');

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'favorited');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('cache_ttl' => 1));
        $lifestream->loadStreams($streams);
        $lifestream->ignore('favorited');

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'tweeted');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('cache_ttl' => 1));
        $lifestream->loadStreams($streams);
        $lifestream->ignore('favorited', 'Youtube');

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'tweeted');
    }

    /**
     * This needs more execution time ..
     * @large
     */
    public function testCustomDateFormatting()
    {
        $streams = array(
            new \SimpleLifestream\Stream('Youtube', 'mtppratt'),
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream(array(
            'cache_ttl' => 1,
            'date_format' => 'Y-m-d',
        ));

        $lifestream->loadStreams($streams);

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);

        foreach ($output as $o)
        {
            list($y, $m, $d) = explode('-', $o['date']);
            $this->assertTrue(checkdate($m, $d, $y));
        }
    }

    /**
     * This needs more execution time ..
     * @large
     */
    public function testLanguages()
    {
        $streams = array(
            new \SimpleLifestream\Stream('Youtube', 'mtppratt'),
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream(array(
            'cache_ttl' => 1,
            'language' => 'Spanish',
        ));

        $lifestream->loadStreams($streams);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertEquals($lifestream->getLastError(), '');
        $this->validateOutput($output, 'a sus favoritos.');

        $lang = new \SimpleLifestream\Languages\English();
        $this->assertEquals('{resource}', $lang->get('test-unknown-key-return'));

        $lang = new \SimpleLifestream\Languages\Spanish();
        $this->assertEquals('{resource}', $lang->get('test-unknown-key-return'));

        $lifestream = new \SimpleLifestream\SimpleLifestream(array(
            'cache_ttl' => 1,
            'language' => 'Unknown-language',
        ));

        $lifestream->loadStreams($streams);
        $output = $lifestream->getLifestream();

        $this->assertCount(1, $lifestream->getErrors());
        $this->validateOutput($output, 'favorited');
    }

    /**
     * This needs more execution time ..
     * @large
     */
    public function testLinkTemplate()
    {
        $streams = array(
            new \SimpleLifestream\Stream('Reddit', 'mpratt'),
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream(array(
            'cache_ttl' => 1,
            'link_format' => 'hello friends'
        ));

        $output = $lifestream->loadStreams($streams)->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'hello friends');
    }

    /**
     * This needs more execution time ..
     * @large
     */
    public function testLimit()
    {
        $streams = array(
            new \SimpleLifestream\Stream('Reddit', 'mpratt'),
            new \SimpleLifestream\Stream('Youtube', 'mtppratt'),
        );

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('cache_ttl' => 1));
        $lifestream->loadStreams($streams);

        $output1 = $lifestream->getLifestream(10);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(10, $output1);
        $this->validateOutput($output1);

        $output2 = $lifestream->getLifestream(1);
        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(1, $output2);
        $this->validateOutput($output2);

        $output3 = $lifestream->getLifestream(6);
        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(6, $output3);
        $this->validateOutput($output3);

        $this->assertEquals($output1[0], $output2[0]);
        $this->assertEquals($output2[0], $output3[0]);
        $this->assertEquals($output1[1], $output3[1]);
        $this->assertEquals($output1[2], $output3[2]);
        $this->assertEquals($output1[3], $output3[3]);
        $this->assertEquals($output1[4], $output3[4]);
        $this->assertEquals($output1[5], $output3[5]);
    }

    /**
     * Validates the output of a lifestream.
     *
     * @param array $output
     * @param string $htmlContains
     * @return void
     */
    protected function validateOutput(array $output, $htmlContains = '')
    {
        if (!empty($output))
        {
            foreach ($output as $v)
            {
                $this->assertArrayHasKey('service', $v);
                $this->assertArrayHasKey('type', $v);
                $this->assertArrayHasKey('resource', $v);
                $this->assertArrayHasKey('url', $v);
                $this->assertArrayHasKey('text', $v);
                $this->assertArrayHasKey('stamp', $v);
                $this->assertArrayHasKey('date', $v);
                $this->assertArrayHasKey('link', $v);
                $this->assertArrayHasKey('html', $v);
                $this->assertArrayHasKey('date_relative', $v);
                $this->assertTrue((bool) preg_match('~(ago|hace|just|ahora)~i', $v['date_relative']), 'Invalid date_relative string: ' . $v['date_relative']);

                if (!empty($htmlContains)) {
                    $this->assertContains($htmlContains, $v['html']);
                }
            }
        }
        else
            $this->fail('Test: Empty Output on validation');
    }
}
?>
