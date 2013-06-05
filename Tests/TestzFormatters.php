<?php
/**
 * TestzFormatters.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestzFormatters extends PHPUnit_Framework_TestCase
{
    protected $lifestream;

    public function setUp()
    {
        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/StackOverflow/1.json'));
        $so = new \SimpleLifestream\Services\StackOverflow($http, 'testResource');

        $http = new MockHttp(file_get_contents(__DIR__ . '/Samples/Reddit/1.json'));
        $rd = new \SimpleLifestream\Services\Reddit($http, 'testResource');

        $this->lifestream = new SimpleLifestreamMock();
        $this->lifestream->services = array($so, $rd);
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

        $this->assertTrue((bool) preg_match('~^<ul class="simplelifestream">(.*?)</ul>$~', $return));
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
}
?>
