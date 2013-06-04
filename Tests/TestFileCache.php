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

class TestFileCache extends PHPUnit_Framework_TestCase
{
    protected $cacheConfig;

    /**
     * Setup the environment
     */
    public function setUp()
    {
        $this->cacheConfig = array(
            'cache' => true,
            'cache_dir' => __DIR__ . '/Samples',
            'cache_ttl' => 10,
            'cache_prefix' => 'testPrefix'
        );

        $cache = new \SimpleLifestream\FileCache($this->cacheConfig);
        $cache->flush();
    }

    /**
     * Cleanup the environment after testing
     */
    public function tearDown()
    {
        $cache = new \SimpleLifestream\FileCache($this->cacheConfig);
        $cache->enable();
        $cache->flush();
    }

    /**
     * Test Cache stores arrays
     */
    public function testStoreArray()
    {
        $cache = new \SimpleLifestream\FileCache($this->cacheConfig);
        $array = array('1', 'asdasd eregrergfdgf dfgdfgjk dfg', '#$^4@35454*(/)');

        $this->assertTrue($cache->store('key_array', $array));
        $this->assertEquals($cache->read('key_array'), $array);
    }

    /**
     * Test Cache stores Objects
     */
    public function testStoreObjects()
    {
        $cache  = new \SimpleLifestream\FileCache($this->cacheConfig);
        $object = (object) array('1', 'asdasd eregrergfdgf dfgdfgjk dfg', '#$^4@35454*(/)');

        $this->assertTrue($cache->store('key_object', $object));
        $this->assertEquals($cache->read('key_object'), $object);
    }

    /**
     * Test Cache stores Strings
     */
    public function testStoreStrings()
    {
        $cache  = new \SimpleLifestream\FileCache($this->cacheConfig);
        $string = 'This is a string! ? \' 345 345 sdf # @ $ % & *';

        $this->assertTrue($cache->store('key_string', $string));
        $this->assertEquals($cache->read('key_string'), $string);
    }

    /**
     * Test Cache Storing twice by the same key
     */
    public function testStoreKeyTwice()
    {
        $cache  = new \SimpleLifestream\FileCache($this->cacheConfig);

        $this->assertTrue($cache->store('key_string', 'This is the first string'));
        $this->assertTrue($cache->store('key_string', 'This is the second string'));
        $this->assertEquals($cache->read('key_string'), 'This is the second string');
    }
    /**
     * Test Cache for nonexistant keys
     */
    public function testNonExistant()
    {
        $cache  = new \SimpleLifestream\FileCache($this->cacheConfig);
        $this->assertNull($cache->read('unknown_key'));
    }

    /**
     * Test Cache Duration
     */
    public function testDuration()
    {
        $cache  = new \SimpleLifestream\FileCache(
            array_merge($this->cacheConfig, array(
                    'cache_ttl' => 1
                )
            )
        );

        $this->assertTrue($cache->store('timed_string', 'Dummy Data'));

        sleep(2);

        $this->assertNull($cache->read('timed_string'));
    }

    /**
     * Test Cache deletion
     */
    public function testDelete()
    {
        $cache  = new \SimpleLifestream\FileCache($this->cacheConfig);

        $this->assertTrue($cache->store('delete_key', 'this is an example'));
        $this->assertTrue($cache->delete('delete_key'));
        $this->assertFalse($cache->delete('unknown_key'));
        $this->assertFalse($cache->delete('delete_key'));
    }

    /**
     * Test Cache Disable
     */
    public function testDisabled()
    {
        $cache  = new \SimpleLifestream\FileCache($this->cacheConfig);
        $cache->disable();

        $this->assertNull($cache->store('disabled_key', 'Dummy Data'));
        $this->assertNull($cache->read('disabled_key'));
    }

    /**
     * Test Cache Flush
     */
    public function testFlush()
    {
        $cache  = new \SimpleLifestream\FileCache($this->cacheConfig);
        $cache->flush();

        $this->assertTrue($cache->store('flush_key1', 'Dummy Data'));
        $this->assertTrue($cache->store('flush_key2', 'Dummy Data'));
        $this->assertTrue($cache->store('flush_key3', 'Dummy Data'));

        $this->assertEquals($cache->flush(), 3);

        $this->assertNull($cache->read('flush_key1'));
        $this->assertNull($cache->read('flush_key2'));
        $this->assertNull($cache->read('flush_key3'));
    }
}
?>
