<?php
/**
 * Pool.php
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
class Pool implements PoolInterface
{
    /** @var array Associative array with configuration directives */
    protected $config = array();

    /** inline {@inheritdoc} */
    public function __construct(array $config = array())
    {
        $this->config = array_merge(array(
            'cache_handler' => '\SimpleLifestream\Cache\File',
            'cache_ttl' => (60*10),
        ), $config);
    }

    /** inline {@inheritdoc} */
    public function getItem($key)
    {
        if (!is_string($key))
            throw new \InvalidArgumentException('The $key parameter must be a string');

        $config = array_merge(
            $this->config,
            array('key' => $key)
        );

        return new $this->config['cache_handler']($config);
    }

    /** inline {@inheritdoc} */
    public function getItems(array $keys)
    {
        $return = array();
        foreach ($keys as $k)
            $return[$k] = $this->getItem($k);

        return $return;
    }

    /** inline {@inheritdoc} */
    public function clear()
    {
        $this->getItem('')->clear();
        return $this;
    }
}

?>
