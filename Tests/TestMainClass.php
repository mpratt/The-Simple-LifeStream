<?php
/**
 * TestMainClass.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

date_default_timezone_set('America/Bogota');
class TestMainClass extends PHPUnit_Framework_TestCase
{
    /**
     * Test Twitter
     */
    public function testTwitter()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'parishilton'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Github
     */
    public function testGithub()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Github' => 'ircmaxell'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     *   Test Youtube
     */
    public function testYoutube()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test StackOverflow
     */
    public function testStackOverflow()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('StackOverflow' => '22656'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Facebook Pages
     */
    public function testFacebookPages()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('FacebookPages' => '6723083591'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Reddit
     */
    public function testReddit()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Reddit' => 'mpratt'));
        $lifestream->setCacheEngine(null);
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

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://www.wradio.com.co/feed.aspx?id=INICIO'));
        $lifestream->setCacheEngine(null);
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

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Ignore Types
     */
    public function testIgnoreTypes()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'parishilton'));
        $lifestream->setCacheEngine(null);
        $lifestream->ignoreType('tweeted');

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->assertEquals(array(), $lifestream->getErrors());
        $this->assertCount(0, $output);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'parishilton',
                                                                   'Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
        $lifestream->ignoreType('tweeted');
        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'favorited');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'parishilton',
                                                                   'Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
        $lifestream->ignoreType('favorited');
        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'tweeted');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'parishilton',
                                                                   'Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
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
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'AlvaroUribeVel'));
        $lifestream->setCacheEngine(null);
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
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'AlvaroUribeVel'));
        $lifestream->setCacheEngine(null);
        $lifestream->setLanguage('Spanish');

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'twitteó');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'AlvaroUribeVel'));
        $lifestream->setCacheEngine(null);
        $lifestream->setLanguage(new \SimpleLifestream\Languages\Spanish());

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'twitteó');
    }

    /**
     * Test Languages
     */
    public function testLanguagesExceptions()
    {
        $this->setExpectedException('InvalidArgumentException');

        $lifestream = new \SimpleLifestream\SimpleLifestream();
        $lifestream->setCacheEngine(null);
        $lifestream->setLanguage(new TwitterMock());
    }

    /**
     * Test Link Template
     */
    public function testLinkTemplate()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('StackOverflow' => '22656'));
        $lifestream->setCacheEngine(null);
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
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'ThatKevinSmith',
                                                                   'Youtube' => 'mtppratt',
                                                                   'Feed'    => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom'));
        $lifestream->setCacheEngine(null);
        $output1 = $lifestream->getLifestream(10);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(10, $output1);
        $this->validateOutput($output1);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'mtppratt',
                                                                   'Twitter' => 'ThatKevinSmith',
                                                                   'Feed'    => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom'));
        $lifestream->setCacheEngine(null);
        $output2 = $lifestream->getLifestream(1);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(1, $output2);
        $this->validateOutput($output2);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed'    => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom',
                                                                   'Twitter' => 'ThatKevinSmith',
                                                                   'Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
        $output3 = $lifestream->getLifestream(6);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(6, $output3);
        $this->validateOutput($output3);

        $this->assertEquals($output1[0], $output2[0], $output3[0]);
    }

    /**
     * Test Merges
     */
    public function testMerges()
    {
        $so = new StackOverflowMock();
        $so->setResource('testResrouce');
        $so->reply = file_get_contents(__DIR__ . '/Samples/SO-merges.json');

        $lifestream = new SimpleLifestreamMock();
        $lifestream->setCacheEngine(null);
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
     * Test the behaviour when an invalid cache is
     * being set.
     */
    public function testInvalidCacheException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $lifestream = new \SimpleLifestream\SimpleLifestream();
        $lifestream->setCacheEngine(new TwitterMock());
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

                if (!empty($contains))
                    $this->assertContains($htmlContains, $v['html']);
            }
        }
        else
            $this->fail('Empty Output');
    }
}
?>
