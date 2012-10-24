<?php
/**
 * SimpleLifestream.php
 * The main class of this library.
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream;

class SimpleLifestream
{
    protected $services   = array();
    protected $errors     = array();
    protected $blacklist  = array();
    protected $cache      = null;
    protected $lang       = null;
    protected $http       = null;
    protected $dateFormat = null;
    protected $linkTemplate = '<a href="{url}">{text}</a>';

    /**
     * Instantiates available services on construction.
     *
     * @param array $config  An array with the service name and resource. A resource could be a username or a resource
     *                       and it depends on each service.
     * @return void
     */
    public function __construct($config = array())
    {
        spl_autoload_register(array($this, 'autoload'));

        // Instantiate important objects. You could overwrite them later if you want!
        $this->cache = new \SimpleLifestream\Core\Cache(dirname(__FILE__) . '/Cache');
        $this->http  = new \SimpleLifestream\Core\Curl();
        $this->lang  = new \SimpleLifestream\Languages\English();

        if (!empty($config) && is_array($config))
        {
            foreach ($config as $service => $resource)
                $this->loadService($service, $resource);
        }
    }

    /**
     * Instantiates and initializes a service object!
     * It stores the object in the services array property.
     *
     * @param string $service
     * @param string $resource The resource related to the service, like a username or url
     * @return void
     */
    public function loadService($service, $resource)
    {
        try {
            $class = '\SimpleLifestream\Services\\' . $service;
            $serviceObject = new $class($this->http);
            $serviceObject->setResource($resource);
            $this->services[] = $serviceObject;
        } catch (\Exception $e) {
            $this->errors[] = 'Could not load the ' . $class . ' Service. Check if the service exists and extends the \SimpleLifestream\Core\Adapter class.';
        }
    }

    /**
     * Calls all available Services and fetches its data.
     * Returns an array with the results found sorted by date.
     *
     * @param int $limit The maximal amount of entries you want to get, 0 shows everything fetched
     * @return array
     */
    public function getLifestream($limit = 0)
    {
        if (empty($this->services))
            return array();

        if (is_object($this->cache))
        {
            $cacheIndex = md5(serialize($this->services));
            $output = $this->cache->read($cacheIndex);
        }

        if (empty($output) || !is_array($output))
        {
            $output = array();
            foreach ($this->services as $service)
            {
                try {
                    $output = array_filter(array_merge($output, $service->getApiData()));
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();

                    if (is_object($this->cache))
                        $this->cache->disable();
                }
            }

            $output = $this->translate($output);
            if (!empty($output))
            {
                // Sort entries by date, the latest ones come first
                usort($output, function ($a, $b) { return $a['stamp'] < $b['stamp']; });

                if (is_object($this->cache))
                    $this->cache->store($cacheIndex, $output);
            }
        }

        // Are there any type of events that we want to ignore?
        if (!empty($this->blacklist))
        {
            $blacklist = $this->blacklist;
            $output = array_filter($output, function($a) use ($blacklist){
                if (isset($blacklist[$a['type']]))
                {
                    if (empty($blacklist[$a['type']]) || strtolower($blacklist[$a['type']]) == $a['service'])
                        return false;
                }

                return true;
            });
        }

        if ($limit > 0 && count($output) > $limit)
            $output = array_slice($output, 0, $limit);

        return $output;
    }

    /**
     * Sets a custom http engine.
     *
     * @param object An Object with http capabilities and respects the IHttpRequest Interface
     * @return void
     */
    public function setHttpEngine(\SimpleLifestream\Interfaces\IHttpRequest $http) { $this->http = $http; }

    /**
     * Sets a custom cache engine.
     *
     * @param object An Object with caching capabilities and respects the Icache Interface.
     *               Null will disable the cache engine.
     * @return void
     */
    public function setCacheEngine($cache)
    {
        if ($cache instanceof \SimpleLifestream\Interfaces\ICache)
            $this->cache = $cache;
        else
            $this->cache = null;
    }

    /**
     * Changes the way a link is displayed by the library.
     *
     * @param string $template The template with a {url} and {text} placeholder
     * @return void
     */
    public function setLinkTemplate($template) { $this->linkTemplate = $template; }

    /**
     * Sets the Default Language
     *
     * @param mixed $lang
     * @return void
     */
    public function setLanguage($lang)
    {
        if ($lang instanceof \SimpleLifestream\Core\Lang)
            $this->lang = $lang;
        else if (is_string($lang))
        {
            try {
                $class = '\SimpleLifestream\Languages\\' . $lang;
                $this->lang = new $class();
            } catch (\Exception $e) { $this->errors[] = $e->getMessage(); }
        }
        else
            $this->errors[] = 'Invalid Language Engine given';
    }

    /**
     * Returns an array with errors catched while executing the script.
     *
     * @param string $type     The type of event that should be ignored
     * @param string $service  When specified ignore only the $type on this service
     * @return void
     */
    public function ignoreType($type, $service = '') { $this->blacklist[$type] = lcfirst($service); }

    /**
     * Sets the date format for each event.
     *
     * @param string A format supported by php's date function
     * @return void
     */
    public function setDateFormat($format) { $this->dateFormat = $format; }

    /**
     * Returns true if there were any errors on execution.
     *
     * @return bool
     */
    public function hasErrors() { return (bool) (!empty($this->errors)); }

    /**
     * Returns an array with errors catched while executing the script.
     *
     * @return array
     */
    public function getErrors() { return $this->errors; }

    /**
     * The autoload function for this library
     *
     * @param string $className
     * @return void
     */
    public function autoload($className)
    {
        $className = ltrim(str_replace('SimpleLifestream\\', '', $className), '\\');
        $fileName  = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\'))
        {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= $className . '.php';

        if (file_exists($fileName))
            require $fileName;
    }

    /**
     * Validates and translates the values returned by the
     * services.
     *
     * @param array $array
     * @return array
     */
    protected function translate($payload)
    {
        $result = array();
        foreach ($payload as $v)
        {
            // validate the service format
            if (empty($v['stamp']) || empty($v['service']) || empty($v['type']) || empty($v['url']) || empty($v['text']) || empty($v['resource']))
                continue;

            $v['type'] = lcfirst($v['type']);
            if (!is_numeric($v['stamp']))
                $v['stamp'] = 0;

            $date = (!is_null($this->dateFormat) ? date($this->dateFormat, $v['stamp']) : $v['stamp']);

            // Basic XSS protection
            $v['text'] = htmlspecialchars($v['text'], ENT_QUOTES, 'UTF-8', false);
            $v['url']  = htmlspecialchars($v['url'], ENT_QUOTES, 'UTF-8', false);

            $link = str_replace(array('{url}', '{text}', '{resource}', '{date}', '{service}'),
                                array($v['url'], $v['text'], $v['resource'], $date, strtolower($v['service'])),
                                $this->linkTemplate);

            $html = str_replace('{link}', $link, $this->lang->get($v['type']));

            $result[] = array('service'  => strtolower($v['service']),
                              'type'     => $v['type'],
                              'resource' => $v['resource'],
                              'url'      => $v['url'],
                              'text'     => $v['text'],
                              'stamp'    => $v['stamp'],
                              'date'     => $date,
                              'link'     => $link,
                              'html'     => $html);
        }

        return $result;
    }
}
?>
