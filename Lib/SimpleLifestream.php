<?php
/**
 * SimpleLifestream.php
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

require_once(dirname(__FILE__) . '/SimpleLifestreamAdapter.php');
require_once(dirname(__FILE__) . '/SimpleLifestreamCache.php');
class SimpleLifestream
{
    protected $services = array();
    protected $errors   = array();
    protected $defaultLang = 'en';
    protected $enableCaching;
    protected $cacheLocation;
    protected $cacheDuration;

    /**
     * Instantiates available services on construction.
     *
     * @param mixed $config You can pass an array with all the information
     *                      or a string with the location of a ini file with all the data.
     * @return void
     */
    public function __construct($config = array())
    {
        if (is_string($config) && is_readable($config))
            $config = parse_ini_file($config, true);

        if (!empty($config) && is_array($config))
        {
            foreach ($config as $serviceName => $values)
                $this->loadService($serviceName, $values);
        }

        $this->setCacheConfig(dirname(__FILE__) . '/Cache');
    }

    /**
     * Instantiates and initializes a service object!
     * It stores the object in the services property.
     *
     * @param string $serviceName
     * @param array $values an array with the service options
     * @return void
     */
    public function loadService($serviceName, $values)
    {
        $serviceName .= 'Service';
        if (is_readable(dirname(__FILE__) . '/Services/' . $serviceName . '.php'))
        {
            require_once(dirname(__FILE__) . '/Services/' . $serviceName . '.php');
            $serviceObject = new $serviceName();
            $serviceObject->setConfig($values);
            $this->services[] = $serviceObject;
        }
        else
            $this->errors[] = 'The service ' . $serviceName . ' does not exist';
    }

    /**
     * Calls all available Services and gets all the
     * Api data and returns an array with the service name, date stamp and html for outputting.
     *
     * @param int $limit The maximal amount of entries you want to get.
     * @return array
     */
    public function getLifestream($limit = 0)
    {
        $cacheIndex = md5(serialize($this->services) . $limit . $this->defaultLang);
        $cache  = new SimpleLifestreamCache($this->cacheLocation, $this->enableCaching);
        $output = $cache->read($cacheIndex);

        if (empty($this->services))
            return array();

        if (empty($output) || !is_array($output))
        {
            foreach ($this->services as $service)
            {
                try {

                    $service->setLanguage($this->defaultLang);
                    $output[] = $service->getApiData();

                } catch (Exception $e) { $this->errors[] = $e->getMessage(); }
            }

            $output = $this->flattenArray($output);
            if (!empty($output) && is_array($output))
            {
                usort($output, array($this, 'orderByDate'));

                if ($limit > 0 && count($output) > $limit)
                    $output = array_slice($output, 0, $limit);

                if ($this->enableCaching && empty($this->errors))
                    $cache->store($cacheIndex, $output, $this->cacheDuration);
            }
        }

        return $output;
    }

    /**
     * This method confirms if the process had any errors
     * @return bool
     */
    public function hasErrors()
    {
       return (!empty($this->errors));
    }

    /**
     * Returns an array with all the errors.
     * @return array
     */
    public function getErrors()
    {
       return $this->errors;
    }

    /**
     * Sets the cache configuration
     *
     * @param string $path  The directory where the cache files are going to be stored
     * @param bool $enable  Wether caching is enabled or not. ()
     * @param int  $ttl     The duration of the cache file in seconds. (10 minutes by default)
     * @return void
     */
    public function setCacheConfig($path, $enable = true, $time = 600)
    {
        $this->cacheLocation = $path;
        $this->enableCaching = (bool) $enable;
        $this->cacheDuration = (int) $time;
    }

     /**
     * Sets the Default Language
     *
     * @param string $lang
     * @return void
     */
    public function setLanguage($lang) { $this->defaultLang = strtolower($lang); }

    /**
     * flattens a multidimensional array
     *
     * @param array $array
     * @return array
     */
    protected function flattenArray($array)
    {
        $result = array();
        if (!is_array($array) || empty($array))
            return $result;

        foreach ($array as $value)
            $result = array_merge($result, $value);

        return array_filter($result);
    }

    /**
     * Callback method that organizes the stream by most recent events
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    protected function orderByDate($a, $b)
    {
        if (empty($a['date']) || !is_numeric($a['date']))
            $a['date'] = 0;

        if (empty($b['date']) || !is_numeric($b['date']))
            $b['date'] = 0;

        return $a['date'] < $b['date'];
    }
}
?>
