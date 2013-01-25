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
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $linkTemplate = '<a href="{url}">{text}</a>';
    protected $mergeConsecutive = false;

    /**
     * Instantiates available services on construction.
     *
     * @param array $config  An array with the service name and resource. A resource could be a username or a resource
     *                       and it depends on each service.
     * @return void
     */
    public function __construct(array $config = array())
    {
        $this->cache = new \SimpleLifestream\Core\Cache(dirname(__FILE__) . '/Cache');
        $this->lang  = new \SimpleLifestream\Languages\English();

        if (!empty($config))
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
        $class = '\SimpleLifestream\Services\\' . $service;
        $serviceObject = new $class();
        $serviceObject->setResource($resource);
        $this->services[] = $serviceObject;
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

        $cacheIndex = md5(serialize($this->services));
        $output = (array) $this->cache->read($cacheIndex);
        if (empty($output))
        {
            $output = array();
            foreach ($this->services as $service)
            {
                try {
                    $output = array_filter(array_merge($output, $service->getApiData()));
                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                    $this->cache->disable();
                }
            }

            $this->cache->store($cacheIndex, $output);
        }

        $output = $this->translate($output);
        usort($output, function ($a, $b) { return $a['stamp'] < $b['stamp']; });

        if ($this->mergeConsecutive)
            $output = $this->deleteConsecutive($output);

        if ($limit > 0 && count($output) > $limit)
            $output = array_slice($output, 0, $limit);

        return $output;
    }

    /**
     * Sets a custom cache engine.
     *
     * @param object An Object with caching capabilities and respects the Icache Interface.
     *               Null will disable the cache engine.
     * @return void
     */
    public function setCacheEngine($cache = null)
    {
        if ($cache instanceof \SimpleLifestream\Interfaces\ICache)
            $this->cache = $cache;
        else if (is_null($cache))
            $this->cache->disable();
        else
            throw new \InvalidArgumentException('Invalid Cache Engine Given');
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
            $class = '\SimpleLifestream\Languages\\' . $lang;
            $this->lang = new $class();
        }
        else
            throw new \InvalidArgumentException('Invalid Language Engine given');
    }

    /**
     * Returns an array with errors catched while executing the script.
     *
     * @param string $type     The type of event that should be ignored
     * @param string $service  When specified ignore only the $type on this service
     * @return void
     */
    public function ignoreType($type, $service = '') { $this->blacklist[lcfirst($type)] = strtolower($service); }

    /**
     * Sets the date format for each event.
     *
     * @param string A format supported by php's date function
     * @return void
     */
    public function setDateFormat($format) { $this->dateFormat = $format; }

    /**
     * When set to true, the library merges consecutive actions that
     * are equal.
     *
     * @param bool $merge
     * @return void
     */
    public function mergeConsecutive($merge) { $this->mergeConsecutive = (bool) $merge; }

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
     * Validates and translates the values returned by the
     * services.
     *
     * @param array $array
     * @return array
     */
    protected function translate(array $payload)
    {
        if (empty($payload))
            return array();

        $defaultValues = array('stamp' => 0,
                               'type'  => 'unknown',
                               'service' => 'unknown',
                               'url'  => '',
                               'text' => '',
                               'date' => '',
                               'resource' => '');

        $result = array();
        foreach ($payload as $v)
        {
            $v = array_merge($defaultValues, $v);
            $v['type']  = lcfirst($v['type']);
            $v['stamp'] = (int) $v['stamp'];
            $v['date']  = date($this->dateFormat, $v['stamp']);
            $v['text']  = htmlspecialchars($v['text'], ENT_QUOTES, 'UTF-8', false);
            $v['url']   = htmlspecialchars($v['url'], ENT_QUOTES, 'UTF-8', false);
            $v['service'] = strtolower($v['service']);

            $link = str_replace(array_map(function ($n){
                return '{' . $n . '}';
            }, array_keys($v)), array_values($v), $this->linkTemplate);

            $html = str_replace('{link}', $link, $this->lang->get($v['type']));
            $result[] = array_merge($v, array('link' => $link, 'html' => $html));
        }

        return $this->deleteBlacklisted($result);
    }

    /**
     * Merges/Deletes consecutive actions.
     *
     * @param array $payload
     * @return array
     */
    protected function deleteConsecutive(array $payload)
    {
        $count = count($payload);
        if ($count > 1)
        {
            $i = 0;
            while ($i < $count)
            {
                if (isset($payload[($i+1)]) && $payload[$i]['html'] == $payload[($i+1)]['html'])
                {
                    $payload[($i+1)]['date']  = $payload[$i]['date'];
                    $payload[($i+1)]['stamp'] = $payload[$i]['stamp'];
                    unset($payload[$i]);
                }

                $i++;
            }
        }

        return $payload;
    }

    /**
     * Removes blacklisted actions
     *
     * @param array $payload
     * @return array
     *
     */
    protected function deleteBlacklisted(array $payload)
    {
        $blacklist = $this->blacklist;
        return array_filter($payload, function($a) use ($blacklist) {
            if (isset($blacklist[$a['type']]))
                return (!empty($blacklist[$a['type']]) && $blacklist[$a['type']] != $a['service']);

            return true;
        });
    }
}

?>
