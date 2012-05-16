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
        $lifestream = new SimpleLifestream(array('Twitter' => array('username' => 'parishilton')));
        $lifestream->setCacheConfig('', false);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Github
     */
    public function testGithub()
    {
        $lifestream = new SimpleLifestream(array('Github' => array('username' => 'mpratt')));
        $lifestream->setCacheConfig('', false);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Youtube
     */
    public function testYoutube()
    {
        $lifestream = new SimpleLifestream(array('Youtube' => array('username' => 'mtppratt')));
        $lifestream->setCacheConfig('', false);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test StackOverflow
     */
    public function testStackOverflow()
    {
        $lifestream = new SimpleLifestream(array('StackOverflow' => array('username' => '430087')));
        $lifestream->setCacheConfig('', false);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Facebook Pages
     */
    public function testFacebookPages()
    {
        $lifestream = new SimpleLifestream(array('FacebookPages' => array('username' => '27469195051')));
        $lifestream->setCacheConfig('', false);
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
        $lifestream = new SimpleLifestream(array('Atom' => array('url' => 'http://www.michael-pratt.com/blog/rss/')));
        $lifestream->setCacheConfig('', false);
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
        $lifestream = new SimpleLifestream(array('Atom' => array('url' => 'http://en.wikipedia.org/w/index.php?title=Special:RecentChanges&feed=atom')));
        $lifestream->setCacheConfig('', false);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Ini file
     */
    public function testIniFile()
    {
        $lifestream = new SimpleLifestream(dirname(__FILE__) . '/testIni.ini');
        $lifestream->setCacheConfig('', false);
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output);
    }

    /**
     * Test Limit and Cache
     */
    public function testLimitAndCache()
    {
        $lifestream = new SimpleLifestream(dirname(__FILE__) . '/testIni.ini');
        $lifestream->setCacheConfig(dirname(__FILE__) . '/testCacheDir');
        $output1 = $lifestream->getLifestream(10);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(10, $output1);
        $this->validateOutput($output1);

        $lifestream = new SimpleLifestream(dirname(__FILE__) . '/testIni.ini');
        $lifestream->setCacheConfig(dirname(__FILE__) . '/testCacheDir');
        $output2 = $lifestream->getLifestream(1);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(1, $output2);
        $this->validateOutput($output2);

        $lifestream = new SimpleLifestream(dirname(__FILE__) . '/testIni.ini');
        $lifestream->setCacheConfig(dirname(__FILE__) . '/testCacheDir');
        $output3 = $lifestream->getLifestream(6);

        $this->assertFalse($lifestream->hasErrors());
        $this->assertCount(6, $output3);
        $this->validateOutput($output3);

        $this->assertEquals($output1[0], $output2[0], $output3[0]);
    }

    /**
     * Test language
     */
    public function testLanguages()
    {
        // English
        $lifestream = new SimpleLifestream(array('Twitter' => array('username' => 'parishilton')));
        $lifestream->setCacheConfig('', false);
        $lifestream->setLanguage('en');
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'view tweet');

        // Spanish
        $lifestream = new SimpleLifestream(array('Twitter' => array('username' => 'parishilton')));
        $lifestream->setCacheConfig('', false);
        $lifestream->setLanguage('eS');
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'ver tweet');

        // Non-Existant Language
        $lifestream = new SimpleLifestream(array('Twitter' => array('username' => 'parishilton')));
        $lifestream->setCacheConfig('', false);
        $lifestream->setLanguage('Unknown');
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'view');

        // Custom Translation
        $lifestream = new SimpleLifestream(array('Twitter' => array('username' => 'parishilton',
                                                                    'translation' => array('binary' => array('view' => '0101010101010101111000110001100')))));
        $lifestream->setCacheConfig('', false);
        $lifestream->setLanguage('binary');
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, '0101010101010101111000110001100');

        // Overwrite a translation
        $lifestream = new SimpleLifestream(array('Twitter' => array('username' => 'parishilton',
                                                                    'translation' => array('es' => array('view' => 'deseas observar este tweet')))));
        $lifestream->setCacheConfig('', false);
        $lifestream->setLanguage('es');
        $output = $lifestream->getLifestream();

        $this->assertFalse($lifestream->hasErrors());
        $this->validateOutput($output, 'deseas observar este tweet');
    }

    /**
     * Validates the output of a lifestream
     */
    protected function validateOutput($output, $searchFor = '')
    {
        $this->assertTrue(is_array($output));
        if (!empty($output))
        {
            foreach ($output as $k => $o)
            {
                $this->assertArrayHasKey('html', $o);
                $this->assertArrayHasKey('date', $o);

                if (empty($o['html']) || !is_string($o['html']))
                    $this->fail('**** Html key number ' . $k . ' is in the wrong format');
                else if (empty($o['date']) || !is_numeric($o['date']) || $o['date'] < 0 || strlen($o['date']) < 10)
                    $this->fail('**** Date key number ' . $k . ' is in the wrong format, it should be a timestamp');
                else if (count($o) != count($o, 1))
                    $this->warning('** Warning: Multidimensional array returned');
                else if (!empty($searchFor))
                    $this->assertContains($searchFor, $o['html']);
            }
        }
        else
            $this->warning('Empty Output');
    }
}
?>
