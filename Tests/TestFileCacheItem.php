<?php
/**
 * TestFileCacheItem.php
 *
 * @author Michael Pratt <pratt@hablarmierda.net>
 * @link   http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestFileCacheItem extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $cache = new \SimpleLifestream\Cache\File();
        $cache->clear();
    }

    public function tearDown()
    {
        $cache = new \SimpleLifestream\Cache\File();
        $cache->clear();
    }

    public function testStoreArray()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'array'));
        $array = array('1', 'asdasd eregrergfdgf dfgdfgjk dfg', '#$^4@35454*(/)');

        $this->assertTrue($cache->set($array));
        $this->assertEquals($cache->get(), $array);
    }

    public function testStoreObjects()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'object'));
        $object = (object) array('1', 'asdasd eregrergfdgf dfgdfgjk dfg', '#$^4@35454*(/)');

        $this->assertTrue($cache->set($object));
        $this->assertEquals($cache->get('key_object'), $object);
    }

    public function testStoreStrings()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'string'));
        $string = 'This is a string! ? \' 345 345 sdf # @ $ % & *';

        $this->assertTrue($cache->set($string, (time() + 50)));
        $this->assertEquals($cache->get(), $string);
    }

    public function testStoreKeyTwice()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'twice'));

        $this->assertTrue($cache->set('this is the first string'));
        $this->assertTrue($cache->set('This is the second string'));
        $this->assertEquals($cache->get(), 'This is the second string');
    }

    public function testNonExistant()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'unknown_key'));
        $this->assertNull($cache->get());
    }

    public function testDuration()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'duration'));
        $this->assertTrue($cache->set('Dummy Data', 1));

        sleep(2);

        $this->assertNull($cache->get());
    }

    public function testDuration2()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'duration2', 'cache_ttl' => 1));
        $this->assertTrue($cache->set('Dummy Data'));

        sleep(2);

        $this->assertNull($cache->get());
    }

    public function testDelete()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'delete'));

        $this->assertTrue($cache->set('this is an example'));
        $this->assertTrue($cache->delete() instanceof \SimpleLifestream\Cache\ItemInterface);
        $this->assertNull($cache->get());
        $this->assertTrue($cache->delete() instanceof \SimpleLifestream\Cache\ItemInterface);
    }

    public function testClear()
    {
        $cache = new \SimpleLifestream\Cache\File(array('key' => 'clear_1'));
        $cache->set('clear');
        $this->assertEquals('clear', $cache->get());

        $cache = new \SimpleLifestream\Cache\File(array('key' => 'clear_2'));
        $cache->set('clear');
        $this->assertEquals('clear', $cache->get());

        $cache = new \SimpleLifestream\Cache\File(array('key' => 'clear_3'));
        $cache->set('clear');
        $this->assertEquals('clear', $cache->get());

        $cache->clear();

        $cache = new \SimpleLifestream\Cache\File(array('key' => 'clear_1'));
        $this->assertNull($cache->get());

        $cache = new \SimpleLifestream\Cache\File(array('key' => 'clear_2'));
        $this->assertNull($cache->get());

        $cache = new \SimpleLifestream\Cache\File(array('key' => 'clear_3'));
        $this->assertNull($cache->get());
    }
}
?>
