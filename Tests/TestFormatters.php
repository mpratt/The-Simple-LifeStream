<?php
/**
 * TestFormatters.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestFormatters extends TestService
{
    protected $lifestream;

    public function setUp()
    {
        $this->lifestream = new SimpleLifestreamMock(array('cache_ttl' => 1));
        $this->lifestream->providers = array(
            'id1' => $this->getStream('Github', 'dummySample3', 'events-2013-10-16.json'),
            'id2' => $this->getStream('StackOverflow', 'dummySample2', 'VonC-2013-10-16.json'),
            'id3' => $this->getStream('Reddit', 'dummySample1', 'dafaqau-2013-10-15.json'),
        );
    }

    public function testTemplateFormatter()
    {
        $formatter = new \SimpleLifestream\Formatters\Template($this->lifestream);
        $formatter->setTemplate('{link}|');
        $return = $formatter->getLifestream(10);

        $test = explode('|', $return);
        $this->assertCount(11, $test);
    }

    public function testTemplateFormatter2()
    {
        $formatter = new \SimpleLifestream\Formatters\Template($this->lifestream);
        $formatter->beforeTemplate('>>');
        $formatter->setTemplate('{link}|');
        $formatter->afterTemplate('<<');
        $return = $formatter->getLifestream(10);

        $this->assertTrue((bool) preg_match('~^>>(.*?)<<$~', $return));
    }

    public function testTemplateFormatter3()
    {
        $formatter = new \SimpleLifestream\Formatters\Template($this->lifestream);
        $formatter->getLifestream();

        $this->assertFalse($formatter->hasErrors());
    }

    public function testTemplateFormatter4()
    {
        $this->setExpectedException('InvalidArgumentException');

        $formatter = new \SimpleLifestream\Formatters\Template($this->lifestream);
        $formatter->UnknownMethod();
    }

    public function testTemplateFormatter5()
    {
        $formatter = new \SimpleLifestream\Formatters\Template($this->lifestream);
        $return = $formatter->getLifestream();

        $this->assertEquals($return, '');
    }

    public function testHtmlListFormatter()
    {
        $formatter = new \SimpleLifestream\Formatters\HtmlList($this->lifestream);
        $return = $formatter->getLifestream();

        $this->assertTrue((bool) preg_match('~^<ul class="simplelifestream">(.*?)</ul>$~is', $return));
    }

    public function testHtmlListFormatter2()
    {
        $formatter = new \SimpleLifestream\Formatters\HtmlList($this->lifestream);
        $formatter->getLifestream();

        $this->assertFalse($formatter->hasErrors());
    }

    public function testHtmlListFormatter3()
    {
        $this->setExpectedException('InvalidArgumentException');

        $formatter = new \SimpleLifestream\Formatters\HtmlList($this->lifestream);
        $formatter->UnknownMethod();
    }

    /**
     * @large
     */
    public function testFormatterReal()
    {
        $streams = array(new \SimpleLifestream\Stream('Reddit', 'mpratt'));

        // Instantiate stream before decorating
        $lifestream = new \SimpleLifestream\SimpleLifestream();
        $lifestream->loadStreams($streams);

        $lifestream = new \SimpleLifestream\Formatters\HtmlList($lifestream);
        $output = $lifestream->getLifestream();

        $this->assertTrue(!empty($output));
        $this->assertTrue((bool) preg_match('~^<ul class="simplelifestream">(.*?)</ul>$~is', $output));
        $this->assertFalse($lifestream->hasErrors());

        // Load Streams already decorated
        $lifestream = new \SimpleLifestream\SimpleLifestream();
        $lifestream = new \SimpleLifestream\Formatters\HtmlList($lifestream);
        $lifestream->loadStreams($streams);
        $output = $lifestream->getLifestream();

        $this->assertTrue(!empty($output));
        $this->assertTrue((bool) preg_match('~^<ul class="simplelifestream">(.*?)</ul>$~is', $output));
        $this->assertFalse($lifestream->hasErrors());

        // Check Output and method chaining
        $lifestream = new \SimpleLifestream\SimpleLifestream();
        $lifestream = new \SimpleLifestream\Formatters\HtmlList($lifestream);
        $output = $lifestream->loadStreams($streams)->getLifestream();

        $this->assertTrue(!empty($output));
        $this->assertTrue((bool) preg_match('~^<ul class="simplelifestream">(.*?)</ul>$~is', $output));
        $this->assertFalse($lifestream->hasErrors());
    }
}
?>
