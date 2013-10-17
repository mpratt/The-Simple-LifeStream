<?php
/**
 * File.php
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
class File extends Handler implements ItemInterface
{
    /** @var array Registry array with fetched information */
    protected $registry = array();

    /** inline {@inheritdoc} */
    public function __construct(array $config = array())
    {
        parent::__construct(array_merge(array(
            'cache_dir' => sys_get_temp_dir(),
            'cache_prefix' => 'SimpleLifestreamFileCache',
            'cache_suffix' => 'cache',
        ), $config));
    }

    /** inline {@inheritdoc} */
    public function getKey() { return $this->config['key']; }

    /** inline {@inheritdoc} */
    public function get()
    {
        if (!$this->isHit())
            return null;

        return $this->registry[$this->config['key']]['content'];
    }

    /** inline {@inheritdoc} */
    public function set($value = null, $ttl = null)
    {
        if (!is_dir($this->config['cache_dir']) || !is_writable($this->config['cache_dir']))
            return false;

        $expire = $this->calculateExpiration($ttl);
        $data = array(
            'expire_time' => (int) $expire,
            'expire_time_date' => date('Y-m-d H:i:s', $expire),
            'created' => date('Y-m-d H:i:s'),
            'content'  => $value,
        );

        $createFile = file_put_contents($this->getFileName(), serialize($data), LOCK_EX);
        return ($createFile !== false && $createFile > 0);
    }

    /** inline {@inheritdoc} */
    public function isHit()
    {
        if (!isset($this->registry[$this->config['key']]['expire_time']))
        {
            if (!$this->exists())
                return false;

            $this->registry[$this->config['key']] = unserialize(file_get_contents($this->getFileName()));
        }

        return ($this->registry[$this->config['key']]['expire_time'] > time());
    }

    /** inline {@inheritdoc} */
    public function delete()
    {
        unset($this->registry[$this->config['key']]);
        @unlink($this->getFileName());
        return $this;
    }

    /** inline {@inheritdoc} */
    public function exists() { return (file_exists($this->getFileName())); }

    /** inline {@inheritdoc} */
    public function clear()
    {
        $this->registry = array();
        $pattern = rtrim($this->config['cache_dir'], '/') . '/' . $this->config['cache_prefix'] . '*.' . $this->config['cache_suffix'];
        if ($list = glob($pattern))
        {
            foreach ($list as $file)
                @unlink($file);
        }
    }

    /**
     * Generates the filename for the given $key
     *
     * @param string $key
     * @return string
     */
    protected function getFileName()
    {
        $location = rtrim($this->config['cache_dir'], '/');
        $location .= '/' . $this->config['cache_prefix'] . '-';
        $location .= md5($this->config['key']) . '.' . $this->config['cache_suffix'];

        return $location;
    }
}

?>
