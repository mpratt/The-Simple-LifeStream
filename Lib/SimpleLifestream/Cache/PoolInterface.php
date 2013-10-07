<?php
/**
 * PoolInterface.php
 *
 * @package SimpleLifestream
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleLifestream\Cache;

/**
 * \Cache\PoolInterface generates Cache\Item objects.
 * Based on PHP FIG's CacheInterface Proposal
 * @link https://github.com/Crell/fig-standards/blob/Cache/proposed/cache.md
 */
interface PoolInterface
{
    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return an ItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key The key for which to return the corresponding Cache Item.
     * @return \Cache\ItemInterface The corresponding Cache Item.
     * @throws \Cache\InvalidArgumentException If the $key string is not a legal value a InvalidArgumentException MUST be thrown.
     */
    public function getItem($key);

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys An indexed array of keys of items to retrieve.
     * @return \Traversable A traversable collection of Cache Items in the same order as the $keys
     *                      parameter, keyed by the cache keys of each item. If no items are found
     *                      an empty Traversable collection will be returned.
     */
    public function getItems(array $keys);

    /**
     * Deletes all items in the pool.
     *
     * @return \Cache\PoolInterface The current pool.
     */
    public function clear();
}

?>
