<?php
/**
 * TestCachePool.php
 *
 * @author Michael Pratt <pratt@hablarmierda.net>
 * @link   http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestCachePool extends PHPUnit_Framework_TestCase
{
    public function testGetItem()
    {
        $pool = new \SimpleLifestream\Cache\Pool();
        $item = $pool->getItem('house');

        $this->assertTrue($item instanceof \SimpleLifestream\Cache\ItemInterface);
        $this->assertEquals('house', $item->getKey());
    }

    public function testGetItem2()
    {
        $this->setExpectedException('InvalidArgumentException');

        $pool = new \SimpleLifestream\Cache\Pool();
        $pool->getItem(null);
    }

    public function testGetItem3()
    {
        $this->setExpectedException('InvalidArgumentException');

        $pool = new \SimpleLifestream\Cache\Pool();
        $pool->getItem(array());
    }

    public function testGetItems()
    {
        $pool = new \SimpleLifestream\Cache\Pool();
        $keys = array(
            'superman',
            'batman',
            'wonder woman',
            'flash',
            'green lantern',
        );

        $items = $pool->getItems($keys);

        foreach ($items as $k => $i)
        {
            $this->assertTrue($i instanceof \SimpleLifestream\Cache\ItemInterface);
            $this->assertEquals($k, $i->getKey());
        }
    }

    public function testClear()
    {
        $pool = new \SimpleLifestream\Cache\Pool();
        $this->assertTrue($pool->clear() instanceof \SimpleLifestream\Cache\PoolInterface);
    }
}
?>
