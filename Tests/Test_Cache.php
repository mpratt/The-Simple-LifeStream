<?php
/**
 * Test_Cache.php
 *
 * @author Michael Pratt <pratt@hablarmierda.net>
 * @link   http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Test_Cache extends PHPUnit_Framework_TestCase
{
    protected $cacheDir;

    /**
     * Setup the environment
     */
    public function setUp()
    {
        $this->cacheDir = dirname(__FILE__) . '/testCacheDir';
        $cache = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $cache->flush();
    }

    /**
     * Cleanup the environment after testing
     */
    public function tearDown()
    {
        $cache = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $cache->enable();
        $cache->flush();

        rmdir($this->cacheDir);
    }

    /**
     * Test Cache stores arrays
     */
    public function testStoreArray()
    {
        $cache = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $array = array('1', 'asdasd eregrergfdgf dfgdfgjk dfg', '#$^4@35454*(/)');

        $this->assertTrue($cache->store('key_array', $array, 10));
        $this->assertEquals($cache->read('key_array'), $array);
    }

    /**
     * Test Cache stores Objects
     */
    public function testStoreObjects()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $object = (object) array('1', 'asdasd eregrergfdgf dfgdfgjk dfg', '#$^4@35454*(/)');

        $this->assertTrue($cache->store('key_object', $object, 10));
        $this->assertEquals($cache->read('key_object'), $object);
    }

    /**
     * Test Cache stores Strings
     */
    public function testStoreStrings()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $string = 'This is a string! ? \' 345 345 sdf # @ $ % & *';
        $cache->setTTL(10);

        $this->assertTrue($cache->store('key_string', $string));
        $this->assertEquals($cache->read('key_string'), $string);
    }

    /**
     * Test Cache Storing twice by the same key
     */
    public function testStoreKeyTwice()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $cache->setTTL(10);

        $this->assertTrue($cache->store('key_string', 'This is the first string'));
        $this->assertTrue($cache->store('key_string', 'This is the second string'));
        $this->assertEquals($cache->read('key_string'), 'This is the second string');
    }
    /**
     * Test Cache for nonexistant keys
     */
    public function testNonExistant()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $this->assertNull($cache->read('unknown_key'));
    }

    /**
     * Test Cache Duration
     */
    public function testDuration()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);

        $this->assertTrue($cache->store('timed_string', 'Dummy Data', 1));
        sleep(2);
        $this->assertNull($cache->read('timed_string'));
    }

    /**
     * Test Cache deletion
     */
    public function testDelete()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $this->assertTrue($cache->store('delete_key', 'this is an example', 10));
        $this->assertTrue($cache->delete('delete_key'));
        $this->assertFalse($cache->delete('unknown_key'));
        $this->assertFalse($cache->delete('delete_key'));
    }

    /**
     * Test Cache Disable
     */
    public function testDisabled()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $cache->disable();

        $this->assertNull($cache->store('disabled_key', 'Dummy Data', 10));
        $this->assertNull($cache->read('disabled_key'));
    }

    /**
     * Test Cache Flush
     */
    public function testFlush()
    {
        $cache  = new \SimpleLifestream\Core\Cache($this->cacheDir);
        $cache->flush();

        $this->assertTrue($cache->store('flush_key1', 'Dummy Data', 10));
        $this->assertTrue($cache->store('flush_key2', 'Dummy Data', 10));
        $this->assertTrue($cache->store('flush_key3', 'Dummy Data', 10));

        $this->assertEquals($cache->flush(), 3);

        $this->assertNull($cache->read('flush_key1'));
        $this->assertNull($cache->read('flush_key2'));
        $this->assertNull($cache->read('flush_key3'));
    }
}
?>
