<?php
/**
 * Handler.php
 *
 * @package Cache
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleLifestream\Cache;

/**
 * This class has the hability to cache data into a file.
 */
abstract class Handler
{
    /** @var array Associative array wwith configuration directives */
    protected $config = array();

    /**
     * Construct
     *
     * @param array $config Associative array with configuration directives.
     * @return void
     */
    public function __construct(array $config = array())
    {
        $this->config = array_merge(array(
            'key' => '',
            'handler' => get_class($this),
            'cache_ttl' => (60*10),
        ), $config);
    }

    /**
     * Calculates the expiration date of a cache item,
     * based on the given $ttl
     *
     * @param int|DateTime $ttl
     * @return int
     */
    protected function calculateExpiration($ttl = null)
    {
        if (is_numeric($ttl))
            return (time() + $ttl);
        else if ($ttl instanceof \DateTimeInterface)
            return $ttl->getTimestamp();
        else
            return (time() + $this->config['cache_ttl']);
    }

    /**
     * flushes all cache files in this directory
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function clear() {}
}

?>
