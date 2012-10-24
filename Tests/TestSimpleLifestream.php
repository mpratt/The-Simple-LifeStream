<?php
/**
 * TestSimpleLifestream.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

date_default_timezone_set('America/Bogota');
require_once(dirname(__FILE__) . '/../Lib/SimpleLifestream.php');

class TestSimpleLifestream extends PHPUnit_Framework_TestCase
{
    protected $knownTypes = array('Twitter' => array('tweeted'),
                                  'Github'  => array('pushEvent', 'createEvent', 'createGist', 'updateGist', 'starred', 'followed'),
                                  'Youtube' => array('favorited'),
                                  'StackOverflow' => array('badgeWon', 'commented', 'acceptedAnswer', 'asked', 'answered'),
                                  'FacebookPages' => array('link'),
                                  'Feed'    => array('posted'),
                                  'Reddit'  => array('commented', 'posted'));
    /**
     * Test Dependencies
     */
    public function testDependencies()
    {
        $this->assertTrue(function_exists('curl_init'));
        $this->assertTrue(function_exists('json_decode'));
        $this->assertTrue(function_exists('utf8_decode'));
    }

    /**
     * Test Twitter
     */
    public function testTwitter()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'parishilton'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, $this->knownTypes['Twitter']);
    }

    /**
     * Test Github
     */
    public function testGithub()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Github' => 'mpratt'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, $this->knownTypes['Github']);
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
        $this->validateOutput($output, $this->knownTypes['Youtube']);
    }

    /**
     * Test StackOverflow
     */
    public function testStackOverflow()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('StackOverflow' => '430087'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, $this->knownTypes['StackOverflow']);
    }

    /**
     * Test Facebook Pages
     */
    public function testFacebookPages()
    {
        $lifestream = new \SimpleLifestream\SimpleLifestream(array('FacebookPages' => '27469195051'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, $this->knownTypes['FacebookPages']);
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
        $this->validateOutput($output, $this->knownTypes['Reddit']);
    }

    /**
     * Test RSS feed
     */
    public function testRSS()
    {
        $this->assertTrue(function_exists('simplexml_load_string'));

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed' => 'http://www.michael-pratt.com/blog/rss/'));
        $lifestream->setCacheEngine(null);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, $this->knownTypes['Feed']);
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
        $this->validateOutput($output, $this->knownTypes['Feed']);
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
        $this->assertCount(0, $output);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'parishilton',
                                                                   'Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
        $lifestream->ignoreType('tweeted', 'Twitter');
        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());

        $this->validateOutput($output, $this->knownTypes['Youtube']);
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
        $this->validateOutput($output, $this->knownTypes['Twitter']);

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
        $this->validateOutput($output, $this->knownTypes['Twitter'], 'twitteó');

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'AlvaroUribeVel'));
        $lifestream->setCacheEngine(null);
        $lifestream->setLanguage(new \SimpleLifestream\Languages\Spanish());

        $output = $lifestream->getLifestream();
        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, $this->knownTypes['Twitter'], 'twitteó');
    }

    /**
     * Test Limit
     */
    public function testLimit()
    {
        $expectedTypes = array_merge($this->knownTypes['Twitter'],
                                     $this->knownTypes['Youtube'],
                                     $this->knownTypes['Feed']);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Twitter' => 'ThatKevinSmith',
                                                                   'Youtube' => 'mtppratt',
                                                                   'Feed'    => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom'));
        $lifestream->setCacheEngine(null);
        $output1 = $lifestream->getLifestream(10);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(10, $output1);
        $this->validateOutput($output1, $expectedTypes);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Youtube' => 'mtppratt',
                                                                   'Twitter' => 'ThatKevinSmith',
                                                                   'Feed'    => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom'));
        $lifestream->setCacheEngine(null);
        $output2 = $lifestream->getLifestream(1);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(1, $output2);
        $this->validateOutput($output2, $expectedTypes);

        $lifestream = new \SimpleLifestream\SimpleLifestream(array('Feed'    => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom',
                                                                   'Twitter' => 'ThatKevinSmith',
                                                                   'Youtube' => 'mtppratt'));
        $lifestream->setCacheEngine(null);
        $output3 = $lifestream->getLifestream(6);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(6, $output3);
        $this->validateOutput($output3, $expectedTypes);

        $this->assertEquals($output1[0], $output2[0], $output3[0]);
    }

    /**
     * Validates the output of a lifestream.
     *
     * @param array $output
     * @param array $types
     * @param string $htmlContains
     * @return void
     */
    protected function validateOutput($output, $types, $htmlContains = '')
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

                $this->assertTrue((in_array($v['type'], $types)));

                if (!empty($contains))
                    $this->assertContains($htmlContains, $v['html']);
            }
        }
        else
            $this->fail('Empty Output');
    }
}
?>
