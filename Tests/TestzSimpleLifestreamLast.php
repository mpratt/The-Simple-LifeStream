<?php
/**
 * TestzSimpleLifestreamLast.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestzSimpleLifestreamLast extends PHPUnit_Framework_TestCase
{
    /**
     * Test Twitter
     */
    public function testTwitter()
    {
        if (!is_file(__DIR__ . '/TwitterCredentials.php'))
        {
            $this->markTestSkipped('No twitter Credentials Found');
            return ;
        }

        require __DIR__ . '/TwitterCredentials.php';
        $data = array_merge(array('user' => 'HablarMierda'), $oauth);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Github
     */
    public function testGithub()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Github' => 'ircmaxell'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     *   Test Youtube
     */
    public function testYoutube()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'mtppratt'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test StackOverflow
     */
    public function testStackOverflow()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('StackOverflow' => '22656'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Facebook Pages
     */
    public function testFacebookPages()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('FacebookPages' => '6723083591'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Reddit
     */
    public function testReddit()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Reddit' => 'mpratt'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test RSS feed
     */
    public function testRSS()
    {
        $this->assertTrue(function_exists('simplexml_load_string'));
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://www.wradio.com.co/feed.aspx?id=INICIO'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Atom feed
     */
    public function testAtom()
    {
        $this->assertTrue(function_exists('simplexml_load_string'));
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom'), array('cache' => false));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Ignore Types
     */
    public function testIgnoreTypes()
    {
        if (!is_file(__DIR__ . '/TwitterCredentials.php'))
        {
            $this->markTestSkipped('No twitter Credentials Found');
            return ;
        }

        require __DIR__ . '/TwitterCredentials.php';
        $data = array_merge(array('user' => 'HablarMierda'), $oauth);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data), array('cache' => false));
        $lifestream->ignoreType('tweeted');

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->assertEquals(array(), $lifestream->getErrors());
        $this->assertCount(0, $output);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data,
                                                                   'Youtube' => 'mtppratt'), array('cache' => false));

        $lifestream->ignoreType('tweeted');
        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'favorited');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data,
                                                                   'Youtube' => 'mtppratt'), array('cache' => false));

        $lifestream->ignoreType('favorited');
        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'tweeted');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data,
                                                                   'Youtube' => 'mtppratt'), array('cache' => false));

        $lifestream->ignoreType('favorited', 'Youtube');
        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'tweeted');
    }

    /**
     * Test Custom Date Formatting
     */
    public function testCustomDateFormatting()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'mtppratt'), array('cache' => false));
        $lifestream->setDateFormat('Y-m-d');

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
     * Test Languages
     */
    public function testLanguages()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'mtppratt'), array('cache' => false, 'lang' => new \SimpleLifestream\Languages\Spanish()));
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->assertEquals($lifestream->getLastError(), '');
        $this->validateOutput($output, 'a sus favoritos.');

        $lang = new \SimpleLifestream\Languages\English();
        $this->assertEquals('{resource}', $lang->get('test-unknown-key-return'));
    }

    /**
     * Test Link Template
     */
    public function testLinkTemplate()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Reddit' => 'mpratt'));
        $lifestream->setLinkTemplate('Hello friends');
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'Hello friends');
    }

    /**
     * Test Limit
     */
    public function testLimit()
    {
        if (!is_file(__DIR__ . '/TwitterCredentials.php'))
        {
            $this->markTestSkipped('No twitter Credentials Found');
            return ;
        }

        require __DIR__ . '/TwitterCredentials.php';
        $data = array_merge(array('user' => 'HablarMierda'), $oauth);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => $data,
                                                                   'Youtube' => 'mtppratt') , array('cache' => false));

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
     * Test Merges
     */
    public function testMerges()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/StackOverflow/2.json'));
        $so = new \SimpleLifestream\Services\StackOverflow($http, 'testResource');

        $lifestream = new SimpleLifestreamMock();
        $lifestream->services[] = $so;

        $output = $lifestream->getLifestream();
        $this->assertEquals(count($output), 9);

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);

        $lifestream->mergeConsecutive(true);
        $output = $lifestream->getLifestream();
        $this->assertEquals(count($output), 5);

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Validates the output of a lifestream.
     *
     * @param array $output
     * @param string $htmlContains
     * @return void
     */
    protected function validateOutput($output, $htmlContains = '')
    {
        $this->assertTrue(is_array($output));
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

                if (!empty($htmlContains))
                    $this->assertContains($htmlContains, $v['html']);
            }
        }
        else
            $this->fail('Empty Output');
    }
}
?>
