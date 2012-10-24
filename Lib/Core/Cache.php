<?php
/**
 * Cache.php
 * This class has the hability to cache data into a file.
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleLifestream\Core;

class Cache implements \SimpleLifestream\Interfaces\ICache
{
    protected $prefix  = 'SimpleLifestreamFileCache';
    protected $enabled = true;
    protected $ttl;
    protected $location;

    /**
     * Construct
     *
     * @param string $location The path where the files are going to be stored
     * @return void
     */
    public function __construct($location)
    {
        $this->ttl = (60*10);
        $this->location = $location;
        if ($this->enabled)
        {
            if (!file_exists($this->location))
                mkdir($this->location, 0755);

            if (!is_writable($this->location) || !is_dir($this->location))
                $this->enabled = false;
        }
        else
            $this->enabled = false;
    }

    /**
     * Stores the cache data to a file
     *
     * @param string $key The Identifier key for the file
     * @param mixed $data The data that is going to be saved
     * @param int $ttl The time in seconds that the cache is going to last
     * @return bool True if the cache was saved successfully. False otherwise
     */
    public function store($key, $data, $ttl = 0)
    {
        if (!$this->enabled || empty($data) || empty($key))
            return false;

        $dataArray = array('expire_time' => (time() + ((is_numeric($ttl) && $ttl > 0 ? $ttl : $this->ttl))),
                           'content'     => $data,
                           'created'     => date('Y-m-d H:i:s'));

        $createFile = file_put_contents($this->location . '/' . $this->createFileName($key), serialize($dataArray), LOCK_EX);

        return (bool) ($createFile !== false && $createFile > 0);
    }

    /**
     * Stores the cache data to a file
     *
     * @param int $ttl Default duration of the cache.
     * @return void
     */
    public function setTTL($ttl) { $this->ttl = (int) $ttl;}


    /**
     * Enables Caching capabilities
     *
     * @return void
     */
    public function enable() { $this->enabled = true; }

    /**
     * Disables Caching capabilities
     *
     * @return void
     */
    public function disable() { $this->enabled = false; }

    /**
     * Reads cache data
     *
     * @param string $key the identifier of the cache file
     * @return mixed The cached data or null if it failed
     */
    public function read($key)
    {
        $file = $this->location . '/' . $this->createFileName($key);
        if (!$this->enabled || !file_exists($file))
            return null;

        $data = unserialize(file_get_contents($file));
        if (!$data || !is_array($data) || empty($data['expire_time']) || empty($data['content']) || ($data['expire_time'] < time()))
        {
            $this->delete($key);
            return null;
        }

        return $data['content'];
    }

    /**
     * Deletes a Cache file based on its key
     *
     * @param string $key the identifier of the cache file
     * @return bool True if the file was deleted, false otherwise
     */
    public function delete($key)
    {
        $file = $this->location . '/' . $this->createFileName($key);
        if (file_exists($file))
            return unlink($file);

        return false;
    }

    /**
     * flushes all cache files in $this->location
     *
     * @return int The count of files deleted
     */
    public function flush()
    {
        $count = 0;
        $list = glob($this->location . '/*');
        if (empty($list))
            return 0;

        foreach ($list as $file)
        {
            $file = basename($file);
            if (is_file($this->location . '/' . $file) && strpos($file, '.cache') !== false)
            {
                unlink($this->location . '/' . $file);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Calculates the filename for $key
     *
     * @param string $key The key identifier for the file
     * @return string
     */
    protected function createFileName($key)
    {
        return $this->prefix . '-' . str_replace(array('/', '"', '\'', '.'), '', $key) . '_' . md5($key) . '.cache';
    }
}
?>
