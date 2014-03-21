<?php
/**
 * TestLanguageFiles.php
 *
 * @author Michael Pratt <pratt@hablarmierda.net>
 * @link   http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestLanguageFiles extends PHPUnit_Framework_TestCase
{
    public function testInvalidGetCall()
    {
        $this->setExpectedException('InvalidArgumentException');

        $lang = new \SimpleLifestream\Languages\English();
        $lang->get();
    }

    public function testJustNow()
    {
        $lang = new \SimpleLifestream\Languages\English();
        $this->assertEquals($lang->getRelativeTranslation('just_now', 0), 'just now');
    }

    public function testPluralSingular()
    {
        $lang = new \SimpleLifestream\Languages\English();
        $this->assertEquals($lang->getRelativeTranslation('minutes', 1), '1 minute ago');
        $this->assertEquals($lang->getRelativeTranslation('minutes', 2), '2 minutes ago');

        $this->assertEquals($lang->getRelativeTranslation('hours', 1), '1 hour ago');
        $this->assertEquals($lang->getRelativeTranslation('hours', 2), '2 hours ago');

        $this->assertEquals($lang->getRelativeTranslation('days', 1), '1 day ago');
        $this->assertEquals($lang->getRelativeTranslation('days', 2), '2 days ago');

        $this->assertEquals($lang->getRelativeTranslation('months', 1), '1 month ago');
        $this->assertEquals($lang->getRelativeTranslation('months', 2), '2 months ago');

        $this->assertEquals($lang->getRelativeTranslation('years', 1), '1 year ago');
        $this->assertEquals($lang->getRelativeTranslation('years', 2), '2 years ago');
    }
}
?>
